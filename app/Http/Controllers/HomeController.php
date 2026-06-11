<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Document;
use App\File;
use App\Http\Requests\UpdateProfileRequest;
use App\Rules\CurrentPassword;
use App\Tag;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class HomeController extends AppBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. [SUNTIKAN] Jembatan Sakti: Ubah ID Divisi jadi Teks
        $namaDivisi = trim(\DB::table('divisi')->where('id_divisi', $user->id_divisi)->value('nama_divisi'));

        // 2. [SUNTIKAN] Tambahin withoutGlobalScopes() buat ngebunuh Satpam Rahasia
        $docQuery = \App\Document::withoutGlobalScopes();
        
        if (!$user->is_super_admin) {
            $docQuery->where(function($query) use ($user, $namaDivisi) {
                // Yang dia upload sendiri
                $query->where('id_user', $user->id);
                
                // Yang divisinya cocok (Pakai Jembatan Sakti)
                if (!empty($namaDivisi)) {
                    $query->orWhere(function($q) use ($namaDivisi) {
                        $q->whereNotNull('divisi')->where('divisi', 'LIKE', '%' . $namaDivisi . '%');
                    });
                }

                // Yang buat Semua divisi
                $query->orWhere('divisi', 'LIKE', '%Semua%');
            });
        }

        // --- DARI SINI KE BAWAH NGGAK ADA YANG GUA SENTUH SAMA SEKALI BRAY! AMAN 100% ---
        $total_documents = $docQuery->count();

        if ($user->is_super_admin) {
            $total_files = \App\File::count(); 
        } else {
            $documentIds = $docQuery->pluck('id_arsip');
            $total_files = \App\File::whereIn('id_arsip', $documentIds)->count();
        }

        $documents = $docQuery->orderBy('created_at', 'desc')->paginate(10);
        $documentCounts = (clone $docQuery)->count();
        $filesCounts = \App\File::count(); 
        $pegawaiAktif = \App\User::count(); 
        $arsipHariIni = (clone $docQuery)->whereDate('tanggal_upload', \Carbon\Carbon::today())->count(); 

        $kategoris = \App\Tag::all(); 
        $labels = [];
        $data = [];

        foreach ($kategoris as $kategori) {
            $labels[] = $kategori->nama_kategori; 
            
            $jumlahArsip = (clone $docQuery)->whereIn('id_arsip', function($q) use ($kategori) {
                $q->select('id_arsip')
                  ->from('arsip_kategori')
                  ->where('id_kategori', $kategori->id_kategori);
            })->count();
                            
            $data[] = $jumlahArsip; 
        }

        $chartLabels = json_encode($labels);
        $chartData = json_encode($data);

        $activities = \App\Activity::with(['createdBy']);
        if($request->has('activity_range')){
            $dates = explode("to",$request->get('activity_range'));
            $activities->whereDate('created_at','>=',$dates[0]??'');
            $activities->whereDate('created_at','<=',$dates[1]??'');
        }
       $activities = $activities->orderBy('created_at', 'desc')->paginate(10); 

        $documents = (clone $docQuery)->orderBy('created_at', 'desc')->take(5)->get(); 

        return view('home', compact(
            'documentCounts', 
            'filesCounts', 
            'pegawaiAktif', 
            'arsipHariIni', 
            'chartLabels', 
            'chartData', 
            'activities', 
            'documents'
        ));
    }

    public function welcome()
    {
        \Artisan::call("inspire");
        $quotes = \Artisan::output();
        return view('welcome',compact('quotes'));
    }

    public function profile(UpdateProfileRequest $request)
    {
        $profile = User::findOrFail(\Auth::id());
        $data = $request->all();
        if($request->isMethod('POST')){
            if($request->has('btnprofile')){
                \Flash::success("Profile Updated Successfully");
            }elseif ($request->has('btnpass')){
                $data['password'] = bcrypt($data['new_password']);
                \Flash::success('Password Updated Successfully');
            }
            $profile->update($data);
            return redirect()->route('profile.manage');
        }
        return view('profile',compact('profile'));
    }

    public function showFile(Request $request, $dir = 'original', $file = null)
    {
        $name = $file; 
        
        $attachment = 'inline';
        if($request->has('force')){
            $attachment = 'attachment';
        }
        
        $filePath = storage_path('app/files/' . $dir . '/') . $file;
        if (!file_exists($filePath)) {
            abort(404);
        }

        $extension = strtolower(last(explode('.', $file)));
        $mimeTypes = [
            'pdf' => 'application/pdf', 'doc' => 'application/msword', 
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

        if ($dir == 'original') {
            try {
                $encryptedContents = file_get_contents($filePath);
                $decryptedContents = Crypt::decrypt($encryptedContents);
                
                return response($decryptedContents)
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', $attachment.'; filename="' . $name . '"');
            } catch (DecryptException $e) {
                return response()->file($filePath, ['Content-disposition' => $attachment.'; filename="' . $name . '"']);
            }
        } else {
            return response()->file($filePath, ['Content-disposition' => $attachment.'; filename="' . $name . '"']);
        }
    }

    public function downloadZip(Request $request, $id, $dir = 'all')
    {
        $document = Document::findOrFail($id);
        $tmpDir = storage_path('app/tmp/');
        if(!file_exists($tmpDir)){
            mkdir($tmpDir,0755,true);
        }
        $docFileTitle = Str::slug($document->name)."_".Str::slug($dir)."_".$document->id.".zip";
        $zip_file = $tmpDir.$docFileTitle;

        $directories = [];
        $imageVariants = explode(",",config('settings.image_files_resize'));
        if($dir=='all' || $dir=='original'){
            $directories[] = "original";
        }else{
            $directories[] = $dir;
        }
        if($dir=='all'){
            foreach ($imageVariants as $imageVariant) {
                $directories[] = $imageVariant;
            }
        }

        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if(!empty($dir) && !empty($directories)){
            foreach ($directories as $directory) {
                foreach ($document->files as $file) {
                    $newName = $directory."/".Str::slug($file->name). "_" .$file->id;
                    $newName .= "." . last(explode('.', $file->file));
                    $existingFile = storage_path("app/files/$directory/$file->file");
                    if(file_exists($existingFile)) {
                        $zip->addFile($existingFile, $newName);
                    }
                }
            }
        }
        $zip->close();
        return response()->download($zip_file)->deleteFileAfterSend();
    }

    public function downloadPdf(Request $request)
    {
        $files = $request->get('images','');
        $varient = $request->get('images_varient','original');
        if(empty($files)){
            return redirect()->back();
        }
        $files = explode(",",$files);
        $docName = Document::whereHas('files',function ($q) use ($files){
            return $q->where('file',$files[0]);
        })->pluck('name')->first();
        $docName = Str::slug($docName)."_".$varient;
        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = Image::make(storage_path("app/files/$varient/$file"))->encode('data-url');
        }
        $pdf = PDF::loadView('pdf', compact('docName','filePaths'));
        return $pdf->download($docName.".pdf");
    }
}