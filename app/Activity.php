<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Nama tabel di database
    public $table = 'activities';

    // Kolom yang diizinkan untuk diisi
    // Kuncinya ada di sini: kita pakai id_arsip
    public $fillable = [
        'activity',
        'created_by',
        'id_arsip'  
    ];

    // Relasi ke tabel Documents
    public function document()
    {
        return $this->belongsTo(\App\Document::class, 'id_arsip', 'id_arsip');
    }

    // Relasi ke tabel Users
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}