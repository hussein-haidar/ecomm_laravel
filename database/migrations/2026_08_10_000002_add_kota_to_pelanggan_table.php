<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('kode_kota', 10)->nullable()->after('alamat');
            $table->string('nama_kota', 255)->nullable()->after('kode_kota');
        });
    }

    public function down()
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['kode_kota', 'nama_kota']);
        });
    }
};
