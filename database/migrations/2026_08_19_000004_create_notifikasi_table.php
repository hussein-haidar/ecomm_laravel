<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->integer('id_pelanggan')->nullable();
            $table->string('nama_pelanggan')->nullable();
            $table->string('sesi_user')->nullable();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info');
            $table->string('link')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamp('waktu')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifikasi');
    }
};
