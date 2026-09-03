<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flash_sale', function (Blueprint $table) {
            $table->id('id_flash_sale');
            $table->string('nama_flash_sale');
            $table->string('deskripsi')->nullable();
            $table->string('sesi_user');
            $table->string('nama_toko');
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->string('status')->default('Aktif');
            $table->integer('deleted_at')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('flash_sale');
    }
};
