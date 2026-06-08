<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    // Kasih tahu nama tabelnya
    protected $table = 'divisi';

    // Kasih tahu primary key-nya (sesuai Class Diagram)
    protected $primaryKey = 'id_divisi';

    // Kasih tahu kolom apa aja yang boleh diisi
    protected $fillable = [
        'nama_divisi'
    ];
}