<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfilPegawaiToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('pangkat_golongan')->nullable()->after('jabatan');
            $table->string('no_hp')->nullable()->after('pangkat_golongan');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'jabatan', 'pangkat_golongan', 'no_hp']);
        });
    }
}