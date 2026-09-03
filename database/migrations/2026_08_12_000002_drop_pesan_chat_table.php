<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('pesan_chat');
    }

    public function down()
    {
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
};