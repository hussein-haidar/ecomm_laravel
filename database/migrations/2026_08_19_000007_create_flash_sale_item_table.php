<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('flash_sale_item', function (Blueprint $table) {
            $table->id('id_flash_sale_item');
            $table->integer('id_flash_sale');
            $table->string('nama_produk');
            $table->integer('id_stok');
            $table->decimal('harga_normal', 12, 2);
            $table->decimal('harga_flash_sale', 12, 2);
            $table->integer('kuota')->default(0);
            $table->integer('terjual')->default(0);
            $table->integer('deleted_at')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('flash_sale_item');
    }
};
