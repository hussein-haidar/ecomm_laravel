<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('chat_penjual', 'chat_transaksi');
    }

    public function down()
    {
        Schema::rename('chat_transaksi', 'chat_penjual');
    }
};