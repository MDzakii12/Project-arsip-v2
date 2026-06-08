<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogAktivitasTable extends Migration
{
    public function up()
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('id_log'); // bigint, Primary Key
            $table->bigInteger('id_user')->unsigned(); // bigint, Foreign Key ke users
            $table->text('rincian_aktivitas'); // text
            $table->timestamp('tanggal_akses')->useCurrent(); // timestamp
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_aktivitas');
    }
}