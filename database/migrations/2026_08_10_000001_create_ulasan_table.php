<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('id_ulasan');
            $table->unsignedBigInteger('id_stok');
            $table->unsignedBigInteger('id_pelanggan')->nullable();
            $table->string('nama_user', 100);
            $table->tinyInteger('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->tinyInteger('terverifikasi')->default(1);
            $table->timestamp('tanggal_ulasan')->useCurrent();

            $table->index('id_stok');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ulasan');
    }
};
