<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Flag pengecualian produk untuk retur (default: boleh diretur)
        Schema::table('stok_produk', function (Blueprint $table) {
            $table->tinyInteger('boleh_retur')->default(1)->after('total_harga');
        });

        // Penanggung biaya retur berdasarkan penyebab (S&K)
        Schema::table('retur', function (Blueprint $table) {
            $table->enum('penanggung_biaya', ['Toko', 'Pembeli'])->nullable()->after('jenis_alasan');
        });

        // Isi secara default: semua stok boleh diretur
        DB::table('stok_produk')->update(['boleh_retur' => 1]);
    }

    public function down(): void
    {
        Schema::table('stok_produk', function (Blueprint $table) {
            $table->dropColumn('boleh_retur');
        });

        Schema::table('retur', function (Blueprint $table) {
            $table->dropColumn('penanggung_biaya');
        });
    }
};