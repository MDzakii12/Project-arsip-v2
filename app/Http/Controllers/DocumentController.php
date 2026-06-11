<?php

namespace App\Http\Controllers;

use App\Document;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class DocumentController extends Controller
{
   public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. JEMBATAN SAKTI: Ambil nama divisi
        $namaDivisi = trim(\DB::table('divisi')->where('id_divisi', $user->id_divisi)->value('nama_divisi'));

        if ($user->is_super_admin) {
            // Super Admin: Dobrak semua batasan
            $documents = \App\Document::withoutGlobalScopes()
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        } else {
            // PEGAWAI: MANTRA WITHOUT GLOBAL SCOPES BUAT MEMBUNUH SATPAM RAHASIA!
            $documents = \App\Document::withoutGlobalScopes()->where(function($query) use ($user, $namaDivisi) {
                // Tarik yang dia upload sendiri
                $query->where('id_user', $user->id);
                
                // Tarik yang divisinya cocok
                if (!empty($namaDivisi)) {
                    $query->orWhere('divisi', 'LIKE', '%' . $namaDivisi . '%');
                }

                // Tarik yang buat Semua
                $query->orWhere('divisi', 'LIKE', '%Semua%');
                
            })->orderBy('created_at', 'desc')->paginate(10);
        }

        $tags = [];

        return view('documents.index', compact('documents', 'tags'));
    }

    public function create()
    {
        $kategori_arsip = \App\Tag::pluck('nama_kategori', 'id_kategori');
        
        $pegawais = \App\User::pluck('name', 'id');

        return view('documents.create', compact('kategori_arsip', 'pegawais'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'nama_arsip' => 'required',
            'id_kategori' => 'required|array' 
        ]);

        $idUser = $request->input('id_user') ?: auth()->id();

        $document = \App\Document::create([
            'nama_arsip' => $request->input('nama_arsip'),
            'deskripsi'  => $request->input('deskripsi'),
            'id_user'    => $idUser,
            'divisi'     => $request->input('divisi'), 
        ]);

        if ($request->has('id_kategori')) {
            foreach($request->input('id_kategori') as $kat_id) {
                \Illuminate\Support\Facades\DB::table('arsip_kategori')->insert([
                    'id_arsip'    => $document->id_arsip,
                    'id_kategori' => $kat_id
                ]);
            }
        }

        $document->newActivity("Membuat folder baru");

        \Flash::success("Folder Utama (Level 1) Berhasil Dibuat!");
        return redirect()->route('documents.index');
    }

    public function show($id)
    {
        // --- RUDAL PENEMBUS SATPAM RAHASIA DIPASANG DI SINI! 👇 ---
        $document = \App\Document::withoutGlobalScopes()
                        ->with('tags')
                        ->where('id_arsip', $id)
                        ->first();

        if (empty($document)) {
            \Flash::error('Folder tidak ditemukan.');
            return redirect(route('documents.index'));
        }

        return view('documents.show')->with('document', $document);
    }

    public function edit($id)
    {
        $document = \App\Document::with('tags')->where('id_arsip', $id)->firstOrFail();
        $kategori_arsip = \App\Tag::pluck('nama_kategori', 'id_kategori');
        $pegawais = \App\User::pluck('name', 'id');
        $selected_tags = $document->tags->pluck('id_kategori')->toArray();

        return view('documents.edit', compact('document', 'kategori_arsip', 'pegawais', 'selected_tags'));
    }

    public function update(Request $request, $id)
    {
        $document = \App\Document::where('id_arsip', $id)->firstOrFail();
        $document->update($request->all());

        if ($request->has('id_kategori')) {
            $document->tags()->sync($request->id_kategori);
        } else {
            $document->tags()->detach();
        }

        Flash::success("Data Folder berhasil diperbarui Komandan!");
        return redirect(route('documents.index'));
    }

    public function destroy($id) 
    { 
        $document = \App\Document::findOrFail($id);
        $namaArsip = $document->nama_arsip ?? $document->name ?? 'Arsip Tidak Bernama';

        // --- MANTRA BYPASS: Pakai cara save manual biar pasti tembus! ---
        $log = new \App\Activity();
        $log->id_arsip = $id; // Kirim ID Arsipnya biar database gak ngamuk
        $log->activity = 'Menghapus Dokumen Arsip: ' . $namaArsip;
        $log->created_by = auth()->id();
        $log->save();

        $document->delete();

        \Flash::success("Arsip Berhasil Dihapus!");
        return redirect()->back();
    }

    public function showUploadFilesUi($id)
    {
        $document = \App\Document::with('tags')->where('id_arsip', $id)->firstOrFail();
        
        $fileTypes = $document->tags->pluck('nama_kategori', 'id_kategori')->toArray();
        
        $customFields = []; 
        return view('documents.file_upload', compact('document', 'fileTypes', 'customFields'));
    }

    public function storeFiles(Request $request, $id)
    {
        $document = \App\Document::where('id_arsip', $id)->firstOrFail();

        $uploadedFiles = $request->file('files', []);
        $filesData = $request->input('files', []);

        if (empty($uploadedFiles)) {
            \Flash::error('Tidak ada file yang dipilih untuk diupload.');
            return redirect()->back();
        }

        foreach ($uploadedFiles as $index => $fileData) {
            if (isset($fileData['file'])) {
                $file = $fileData['file'];
                
                $fileContents = file_get_contents($file);
                $namaFileAsli = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();                
                $file->move(storage_path('app/files/original'), $fileName);
                $metaData = $filesData[$index] ?? [];
                $kategori = \App\Tag::where('id_kategori', $metaData['file_type_id'] ?? null)->first();
                $nama_kategori = $kategori ? $kategori->nama_kategori : 'Tanpa_Kategori';
                $nama_arsip = $document->nama_arsip;
                $jalur_strata_gdrive = $nama_arsip . '/' . $nama_kategori . '/' . $namaFileAsli;
                \Storage::disk('google')->put($jalur_strata_gdrive, $fileContents);

                \App\File::create([
                    'id_arsip'         => $document->id_arsip,
                    'file_type_id'     => $metaData['file_type_id'] ?? null,
                    'created_by'       => auth()->id(),
                    'name'             => $metaData['name'] ?? $namaFileAsli,
                    'file'             => $fileName, 
                    'masa_guna'        => $metaData['masa_guna'] ?? null,
                    'lokasi_hard_copy' => $metaData['lokasi_hard_copy'] ?? null,
                    'status'           => $metaData['status'] ?? 'Active',
                ]);
            }
        }

        $document->newActivity("Mengunggah file baru ke arsip (Lokal & Backup GDrive)");

        \Flash::success('Sempurna! File tersimpan di laptop dan ter-backup rapi di Google Drive!');
        
        return redirect()->route('documents.show', $document->id_arsip);
    }


    public function deleteFile($id)
    {
        $file = \App\File::findOrFail($id);
        $namaFile = $file->file ?? 'File';
        $idArsip = $file->id_arsip; // Tangkap ID Folder tempat file ini bernaung

        if (\Storage::disk('google')->exists($file->file)) {
            \Storage::disk('google')->delete($file->file);
        }

        // --- MANTRA BYPASS: Pakai cara save manual biar pasti tembus! ---
        $log = new \App\Activity();
        $log->id_arsip = $idArsip; // Kirim ID Arsipnya biar database gak ngamuk
        $log->activity = 'Menghapus file: ' . $namaFile; 
        $log->created_by = auth()->id();
        $log->save();

        $file->delete();

        \Flash::success('Sip! File berhasil dihapus dari Sistem dan Google Drive!');
        return redirect()->back();
    }    

}