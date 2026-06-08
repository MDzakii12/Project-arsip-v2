<?php

namespace App;
use Illuminate\Database\Eloquent\Builder;
use Eloquent as Model;

class Document extends Model
{
    protected $table = 'arsip';
    protected $primaryKey = 'id_arsip';

    // 1. TAMBAHKAN KOLOM 'divisi' DI SINI BIAR BISA DISIMPAN!
    protected $fillable = [
        'id_user',       // Untuk "Tugaskan ke Pegawai" (Personal)
        'divisi',        // Untuk "Tugaskan ke Divisi" (Grup)
        'nama_arsip',    // Nama Folder Level 1
        'deskripsi',
        // Kolom lain (status_surat, lokasi, file_path) KITA KOSONGIN DULU
        // Karena form bikin folder Level 1 belum butuh itu. Nanti diisi pas Level 3.
    ];

    public static $rules = [
        'nama_arsip' => 'required',
    ];

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'id_user', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'arsip_kategori', 'id_arsip', 'id_kategori', 'id_arsip', 'id_kategori');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'id_arsip', 'id_arsip');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('kunci_dokumen_pegawai', function ($builder) {
            if (auth()->check() && !auth()->user()->is_super_admin) {
                $builder->where(function ($query) {
                    $query->where('divisi', auth()->user()->divisi)
                          ->orWhere('id_user', auth()->id()); // Ganti created_by jadi id_user
                }); 
            } 
        }); 
    }

    public function newActivity($activity_text, $include_document = true) {
        if ($include_document) {
            $activity_text .= " : " . '<a href="' . route('documents.show', $this->id_arsip) . '">' . $this->nama_arsip . "</a>";
        }
        
        // Kuncinya di sini: pastikan kolom yang diisi adalah 'id_arsip'
        \App\Activity::create([
            'activity'    => $activity_text,
            'created_by'  => \Auth::id(),
            'id_arsip'    => $this->id_arsip, // <-- INI HARUS 'id_arsip'
        ]);
    }
}