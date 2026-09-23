<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->string('kode_retur', 50)->unique();
            $table->unsignedBigInteger('id_beli');
            $table->unsignedBigInteger('id_bayar')->nullable();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_stok');
            $table->string('nama_pelanggan', 100);
            $table->string('nama_toko', 100);
            $table->string('sesi_user', 100)->nullable();
            $table->string('nama_produk', 100);
            $table->string('ukuran_produk', 100)->nullable();
            $table->integer('jumlah_produk')->default(1);
            $table->string('satuan_produk', 100)->nullable();
            $table->enum('jenis_alasan', [
                'Produk rusak/cacat',
                'Barang salah/keliru',
                'Tidak sesuai deskripsi',
                'Barang tidak lengkap',
                'Alasan lain',
            ])->default('Produk rusak/cacat');
            $table->text('alasan')->nullable();
            $table->enum('tipe_retur', ['penggantian', 'pengembalian_dana'])->default('penggantian');
            $table->string('foto_bukti')->nullable();
            $table->text('alamat_pengembalian')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('nomor_resi', 100)->nullable();
            $table->enum('status', [
                'Menunggu Verifikasi',
                'Disetujui',
                'Ditolak',
                'Menunggu Pengiriman',
                'Barang Dalam Perjalanan',
                'Barang Diterima',
                'Selesai',
                'Dibatalkan',
            ])->default('Menunggu Verifikasi');
            $table->text('catatan_admin')->nullable();
            $table->decimal('jumlah_refund', 10, 2)->default(0)->nullable();
            $table->enum('status_refund', ['Belum Diproses', 'Refund Diproses', 'Refund Selesai'])->nullable();
            $table->dateTime('waktu_pengajuan')->nullable();
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();

            $table->index(['id_pelanggan', 'status']);
            $table->index(['sesi_user', 'status']);
            $table->index('id_beli');
        });

        // Samakan collation dengan tabel lama yang memakai utf8mb4_general_ci
        // supaya JOIN ke produk/pembelian/pembayaran tidak error Illegal mix of collations.
        DB::statement('ALTER TABLE `retur` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};