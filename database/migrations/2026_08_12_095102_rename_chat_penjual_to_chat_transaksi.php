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
        if (Schema::hasTable('chat_penjual') && !Schema::hasTable('chat_transaksi')) {
            Schema::rename('chat_penjual', 'chat_transaksi');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('chat_transaksi', 'chat_penjual');
    }
};
