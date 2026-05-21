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
        // 1. STATISTIK UMUM
        $documentCounts = Document::count();
        $filesCounts = File::count();
        $pegawaiAktif = User::where('id', '!=', 1)->where('status', 'Active')->count();
        $arsipHariIni = Document::whereDate('created_at', Carbon::today())->count();

        // 2. DATA UNTUK GRAFIK (Jumlah Arsip per Kategori)
        $tags = Tag::withCount('documents')->get();
        $chartLabels = $tags->pluck('name')->toJson();
        $chartData = $tags->pluck('documents_count')->toJson();

        // 3. LOG AKTIVITAS (Timeline)
        $activities = Activity::with(['createdBy', 'document']);
        if($request->has('activity_range')){
            $dates = explode("to",$request->get('activity_range'));
            $activities->whereDate('created_at','>=',$dates[0]??'');
            $activities->whereDate('created_at','<=',$dates[1]??'');
        }
        $activities = $activities->orderByDesc('id')->paginate(10); // Ambil 10 aktivitas terakhir

        // 4. DAFTAR DOKUMEN (Untuk Quick Upload)
        $documents = Document::orderByDesc('id')->get();

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
        // 1. Biarkan nama filenya tetap ACAK (hashed) seperti di dalam folder!
        $name = $file; 
        
        $attachment = 'inline';
        if($request->has('force')){
            $attachment = 'attachment';
        }
        
        $filePath = storage_path('app/files/' . $dir . '/') . $file;
        if (!file_exists($filePath)) {
            abort(404);
        }

        // 2. Tentukan tipe ekstensi untuk browser
        $extension = strtolower(last(explode('.', $file)));
        $mimeTypes = [
            'pdf' => 'application/pdf', 'doc' => 'application/msword', 
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

        // 3. PROSES DEKRIPSI
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