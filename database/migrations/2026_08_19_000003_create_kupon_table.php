<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kupon', function (Blueprint $table) {
            $table->id('id_kupon');
            $table->string('kode_kupon')->unique();
            $table->string('nama_kupon');
            $table->string('tipe_diskon')->default('persen');
            $table->decimal('nilai_diskon', 12, 2)->default(0);
            $table->decimal('min_pembelian', 12, 2)->default(0);
            $table->decimal('max_diskon', 12, 2)->default(0);
            $table->integer('kuota')->default(0);
            $table->integer('terpakai')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('sesi_user');
            $table->string('status_aktif')->default('Aktif');
            $table->integer('deleted_at')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kupon');
    }
};
