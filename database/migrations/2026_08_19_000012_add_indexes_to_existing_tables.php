<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $indexes = [
            ['pembelian', 'pembelian_sesi_user_status_beli_index', '(`sesi_user`, `status_beli`)'],
            ['pembelian', 'pembelian_nama_pelanggan_status_beli_index', '(`nama_pelanggan`, `status_beli`)'],
            ['pembayaran', 'pembayaran_sesi_user_status_bayar_index', '(`sesi_user`, `status_bayar`)'],
            ['produk', 'produk_sesi_user_deleted_at_index', '(`sesi_user`, `deleted_at`)'],
            ['produk', 'produk_jenis_produk_index', '(`jenis_produk`)'],
            ['produk', 'produk_nama_produk_index', '(`nama_produk`)'],
            ['stok_produk', 'stok_produk_sesi_user_nama_produk_index', '(`sesi_user`, `nama_produk`)'],
            ['stok_produk', 'stok_produk_nama_produk_index', '(`nama_produk`)'],
            ['ekspedisi', 'ekspedisi_nama_pelanggan_status_kirim_index', '(`nama_pelanggan`, `status_kirim`)'],
            ['ekspedisi', 'ekspedisi_id_beli_index', '(`id_beli`)'],
            ['ekspedisi', 'ekspedisi_id_bayar_index', '(`id_bayar`)'],
            ['ulasan', 'ulasan_id_pelanggan_index', '(`id_pelanggan`)'],
            ['keranjang', 'keranjang_nama_pelanggan_status_keranjang_index', '(`nama_pelanggan`, `status_keranjang`)'],
        ];

        foreach ($indexes as [$table, $name, $columns]) {
            $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]);
            if (empty($exists)) {
                DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$name}`{$columns}");
            }
        }
    }

    public function down()
    {
        $indexNames = [
            'pembelian_sesi_user_status_beli_index',
            'pembelian_nama_pelanggan_status_beli_index',
            'pembayaran_sesi_user_status_bayar_index',
            'produk_sesi_user_deleted_at_index',
            'produk_jenis_produk_index',
            'produk_nama_produk_index',
            'stok_produk_sesi_user_nama_produk_index',
            'stok_produk_nama_produk_index',
            'ekspedisi_nama_pelanggan_status_kirim_index',
            'ekspedisi_id_beli_index',
            'ekspedisi_id_bayar_index',
            'ulasan_id_pelanggan_index',
            'keranjang_nama_pelanggan_status_keranjang_index',
        ];

        $tables = [
            'pembelian' => ['pembelian_sesi_user_status_beli_index', 'pembelian_nama_pelanggan_status_beli_index'],
            'pembayaran' => ['pembayaran_sesi_user_status_bayar_index'],
            'produk' => ['produk_sesi_user_deleted_at_index', 'produk_jenis_produk_index', 'produk_nama_produk_index'],
            'stok_produk' => ['stok_produk_sesi_user_nama_produk_index', 'stok_produk_nama_produk_index'],
            'ekspedisi' => ['ekspedisi_nama_pelanggan_status_kirim_index', 'ekspedisi_id_beli_index', 'ekspedisi_id_bayar_index'],
            'ulasan' => ['ulasan_id_pelanggan_index'],
            'keranjang' => ['keranjang_nama_pelanggan_status_keranjang_index'],
        ];

        foreach ($tables as $table => $names) {
            foreach ($names as $name) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$name}`");
            }
        }
    }
};
