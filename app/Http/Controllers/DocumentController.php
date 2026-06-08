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

        // 1. Cek apakah yang login Super Admin?
        if ($user->is_super_admin) {
            $documents = \App\Document::orderBy('created_at', 'desc')->paginate(10);
        } else {
            // Kacamata Kuda Anti-Bocor Total
            $documents = \App\Document::where(function($query) use ($user) {
                // 1. Jalur Pribadi (Milik dia sendiri)
                $query->where('id_user', $user->id);
                
                // 2. Jalur Divisi (Cek HANYA JIKA user punya divisi yang valid & bukan NULL)
                if ($user->divisi != null && $user->divisi != '') {
                    $query->orWhere(function($q) use ($user) {
                        $q->whereNotNull('divisi')->where('divisi', $user->divisi);
                    });
                }

                // 3. Jalur Publik
                $query->orWhere('divisi', 'Semua');
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
        // 1. Validasi Inputan
        $request->validate([
            'nama_arsip' => 'required',
            'id_kategori' => 'required|array' 
        ]);

        // 2. Tentukan Hak Akses Personal
        $idUser = $request->input('id_user') ?: auth()->id();

        // 3. Simpan Data Folder Utama (Level 1) ke tabel `arsip`
        $document = \App\Document::create([
            'nama_arsip' => $request->input('nama_arsip'),
            'deskripsi'  => $request->input('deskripsi'),
            'id_user'    => $idUser,
            'divisi'     => $request->input('divisi'), 
        ]);

        // 4. Simpan Relasi Kategori Pakai Cara Preman (Jaminan 100% Masuk!)
        if ($request->has('id_kategori')) {
            foreach($request->input('id_kategori') as $kat_id) {
                \Illuminate\Support\Facades\DB::table('arsip_kategori')->insert([
                    'id_arsip'    => $document->id_arsip,
                    'id_kategori' => $kat_id
                ]);
            }
        }

        // 5. Pencatatan Log
        $document->newActivity("Membuat folder baru");

        \Flash::success("Folder Utama (Level 1) Berhasil Dibuat!");
        return redirect()->route('documents.index');
    }

    public function show($id)
    {
        $document = \App\Document::with('tags')->where('id_arsip', $id)->first();

        if (empty($document)) {
            \Flash::error('Folder tidak ditemukan.');
            return redirect(route('documents.index'));
        }

        return view('documents.show')->with('document', $document);
    }
    public function edit($id)
    {
        // 1. Cari data folder sekalian bawa relasi tags-nya
        $document = \App\Document::with('tags')->where('id_arsip', $id)->firstOrFail();
        
        // 2. Amunisi buat dropdown
        $kategori_arsip = \App\Tag::pluck('nama_kategori', 'id_kategori');
        $pegawais = \App\User::pluck('name', 'id');

        // 3. AMUNISI BARU: Tarik ID kategori yang udah kepilih sebelumnya
        $selected_tags = $document->tags->pluck('id_kategori')->toArray();

        // 4. Kirim semua ke form edit
        return view('documents.edit', compact('document', 'kategori_arsip', 'pegawais', 'selected_tags'));
    }

    public function update(Request $request, $id)
    {
        // 1. Cari data foldernya
        $document = \App\Document::where('id_arsip', $id)->firstOrFail();
        
        // 2. Simpan nama baru, deskripsi baru, dll
        $document->update($request->all());

        // 3. Simpan perubahan Kategori / Level 2
        // Pastikan 'tags' ini sama dengan attribute 'name' di tag <select> lu
        if ($request->has('id_kategori')) {
            $document->tags()->sync($request->id_kategori);
        } else {
            $document->tags()->detach();
        }

        // 4. Notifikasi dan tendang balik
        Flash::success("Data Folder berhasil diperbarui Komandan!");
        return redirect(route('documents.index'));
    }
    public function destroy($id) { 
        Document::destroy($id);
        Flash::success("Arsip Berhasil Dihapus!");
        return redirect()->back();
    }

    public function showUploadFilesUi($id)
    {
        $document = \App\Document::with('tags')->where('id_arsip', $id)->firstOrFail();
        
        // TRIK BAJAKAN: Ambil pilihan dari Kategori Arsip, BUKAN dari Tipe File General
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
                
                // Ambil isi file dan nama aslinya sebelum dipindah-pindah
                $fileContents = file_get_contents($file);
                $namaFileAsli = $file->getClientOriginalName();
                
                // Bikin nama unik HANYA untuk di lokal laptop (biar OS Windows/Linux ga bentrok)
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // [JALUR 1] SIMPAN KE LAPTOP: Biar fitur Lihat/Download di web langsung lancar
                $file->move(storage_path('app/files/original'), $fileName);

                $metaData = $filesData[$index] ?? [];

                // [JALUR 2] BACKUP KE GDRIVE: Cari tahu nama kategori
                $kategori = \App\Tag::where('id_kategori', $metaData['file_type_id'] ?? null)->first();
                $nama_kategori = $kategori ? $kategori->nama_kategori : 'Tanpa_Kategori';

                // Bikin struktur strata GDrive tapi pake NAMA ASLI UTUH tanpa acakan angka
                $nama_arsip = $document->nama_arsip;
                $jalur_strata_gdrive = $nama_arsip . '/' . $nama_kategori . '/' . $namaFileAsli;

                // Kirim ke Google Drive sebagai backup rapi
                \Storage::disk('google')->put($jalur_strata_gdrive, $fileContents);

                // [JALUR 3] CATAT KE DATABASE: Pake nama lokal biar sistem ga bingung nyari filenya
                \App\File::create([
                    'id_arsip'         => $document->id_arsip,
                    'file_type_id'     => $metaData['file_type_id'] ?? null,
                    'created_by'       => auth()->id(),
                    'name'             => $metaData['name'] ?? $namaFileAsli,
                    'file'             => $fileName, // Mengunci ke file lokal laptop
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
        // 1. Cari data filenya di database
        $file = \App\File::findOrFail($id);

        // 2. MANTRA SAKTI: Hapus fisik filenya dari Google Drive
        if (\Storage::disk('google')->exists($file->file)) {
            \Storage::disk('google')->delete($file->file);
        }

        // 3. Hapus catatannya dari database MySQL lu
        $file->delete();

        \Flash::success('Sip! File berhasil dihapus dari Sistem dan Google Drive!');
        return redirect()->back();
    }    

}