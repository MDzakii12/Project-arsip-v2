<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArsipTable extends Migration
{
    public function up()
    {
        Schema::create('arsip', function (Blueprint $table) {
            $table->increments('id_arsip'); // int, Primary Key
            $table->integer('id_kategori'); // int, Foreign Key ke kategori_arsip
            $table->bigInteger('id_user')->unsigned(); // bigint, Foreign Key ke users
            $table->string('nama_arsip', 50); // varchar(50)
            $table->string('nomor_arsip', 30)->nullable(); // varchar(30)
            $table->string('file_path', 255); // varchar(255)
            $table->date('tanggal_upload'); // date
            $table->text('deskripsi')->nullable(); // text
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arsip');
    }
}