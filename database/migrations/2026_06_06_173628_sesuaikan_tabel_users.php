<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SesuaikanTabelUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom tambahan penyesuaian Class Diagram Bab 4
            $table->integer('id_divisi')->nullable(); // int
            $table->string('nama_lengkap', 50)->nullable(); // varchar(50)
            $table->string('nip', 20)->nullable(); // varchar(20)
            $table->string('jabatan', 30)->nullable(); // varchar(30)
            $table->string('no_hp', 15)->nullable(); // varchar(15)
            $table->string('status_akun', 20)->nullable(); // varchar(20)
            
            // Catatan: Kolom email, username, password sudah disediakan bawaan Laravel di file atasnya.
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_divisi', 'nama_lengkap', 'nip', 'jabatan', 'no_hp', 'status_akun']);
        });
    }
}