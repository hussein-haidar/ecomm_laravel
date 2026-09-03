<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_penjual', function (Blueprint $table) {
            $table->id('id_chat');
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('nama_pelanggan', 100);
            $table->unsignedBigInteger('id_user');
            $table->string('nama_toko', 100);
            $table->unsignedBigInteger('id_stok')->nullable();
            $table->string('nama_produk', 191)->nullable();
            $table->string('last_pesan', 255)->nullable();
            $table->dateTime('last_waktu')->nullable();
            $table->dateTime('created_at')->nullable();

            $table->index('id_pelanggan');
            $table->index('id_user');
        });

        Schema::create('pesan_chat', function (Blueprint $table) {
            $table->id('id_pesan');
            $table->unsignedBigInteger('id_chat');
            $table->string('pengirim', 20);
            $table->unsignedBigInteger('pengirim_id');
            $table->string('nama_pengirim', 100);
            $table->text('pesan');
            $table->tinyInteger('dibaca')->default(0);
            $table->dateTime('created_at')->nullable();

            $table->index('id_chat');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesan_chat');
        Schema::dropIfExists('chat_penjual');
    }
};
