<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisiTable extends Migration
{
    public function up()
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->increments('id_divisi'); // int, Primary Key
            $table->string('nama_divisi', 30); // varchar(30)
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('divisi');
    }
}