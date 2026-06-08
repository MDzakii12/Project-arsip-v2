<?php

namespace App;

use Eloquent as Model;

class File extends Model
{
    public $table = 'files';

    // Ganti document_id jadi id_arsip
    public $fillable = [
        'name',
        'file',
        'id_arsip', 
        'file_type_id',
        'created_by',
        'custom_fields',
        'masa_guna',
        'lokasi_hard_copy',
        'status'
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'file' => 'string',
        'id_arsip' => 'integer',
        'file_type_id' => 'integer',
        'created_by' => 'integer',
        'custom_fields' => 'array'
    ];

    // Relasi ke Folder (Document) yang BENAR
    public function document()
    {
        return $this->belongsTo(\App\Document::class, 'id_arsip', 'id_arsip');
    }

    // Relasi ke Kategori/Tipe File
    public function fileType()
    {
        return $this->belongsTo(\App\Tag::class, 'file_type_id', 'id_kategori');
    }

    // Relasi ke User pengupload
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}