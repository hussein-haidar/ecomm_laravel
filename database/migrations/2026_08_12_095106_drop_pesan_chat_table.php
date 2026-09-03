<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('pesan_chat');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pesan_chat', function (Blueprint $table) {
            $table->id('id_pesan');
            $table->unsignedBigInteger('id_chat');
            $table->string('pengirim');
            $table->integer('pengirim_id');
            $table->string('nama_pengirim');
            $table->text('pesan');
            $table->boolean('dibaca')->default(0);
            $table->timestamps();

            $table->foreign('id_chat')->references('id_chat')->on('chat_transaksi')->onDelete('cascade');
        });
    }
};
