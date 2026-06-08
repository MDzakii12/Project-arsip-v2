<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriArsipTable extends Migration
{
    public function up()
    {
        Schema::create('kategori_arsip', function (Blueprint $table) {
            $table->increments('id_kategori'); // int, Primary Key
            $table->string('nama_kategori', 30); // varchar(30)
            $table->string('label_warna', 10); // varchar(10)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_arsip');
    }
}