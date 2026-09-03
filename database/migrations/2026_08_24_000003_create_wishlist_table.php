<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id('id_wishlist');
            $table->unsignedBigInteger('id_pelanggan')->index();
            $table->unsignedBigInteger('id_stok')->index();
            $table->timestamp('tanggal_tambah')->useCurrent();

            $table->unique(['id_pelanggan', 'id_stok']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wishlist');
    }
};
