<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $table = 'kategori_arsip'; 
    
    protected $primaryKey = 'id_kategori'; 

    public $fillable = [
        'nama_kategori',
        'label_warna'
    ];

    public static $rules = [
        'nama_kategori' => 'required|string|max:30',
        'label_warna'   => 'nullable|string|max:10'
    ];
}