<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateStatusPengiriman extends Command
{
    protected $signature = 'update:status-pengiriman';
    protected $description = 'Update status pengiriman otomatis berdasarkan estimasi_waktu';

    public function handle()
    {
        $now = Carbon::now();

        // === 1. Dibayar → Dikemas (semua pesanan, 2 menit) ===
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Dibayar')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(2))
            ->update([
                'status_kirim' => 'Dikemas',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Dikemas')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_dikemas' => $now, 'updated_at' => $now]);
        }

        // === 2. ALUR INSTAN: estimasi_waktu mengandung "jam" ===
        // a. Dikemas → Diantar kurir (1 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Dikemas')
            ->where('estimasi_waktu', 'like', '%jam%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(1))
            ->update([
                'status_kirim' => 'Diantar kurir',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Diantar kurir')
                ->where('estimasi_waktu', 'like', '%jam%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_diantar_kurir' => $now, 'updated_at' => $now]);
        }

        // b. Diantar kurir → Sampai tujuan (5 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Diantar kurir')
            ->where('estimasi_waktu', 'like', '%jam%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(5))
            ->update([
                'status_kirim' => 'Sampai tujuan',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Sampai tujuan')
                ->where('estimasi_waktu', 'like', '%jam%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_sampai_tujuan' => $now, 'updated_at' => $now]);
        }

        // === 3. ALUR REGULER: estimasi_waktu mengandung "hari" ===
        // a. Dikemas → Dikirim dari toko (2 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Dikemas')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(2))
            ->update([
                'status_kirim' => 'Dikirim dari toko',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Dikirim dari toko')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_dikirim_toko' => $now, 'updated_at' => $now]);
        }

        // b. Dikirim dari toko → Disortir (2 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Dikirim dari toko')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(2))
            ->update([
                'status_kirim' => 'Disortir',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Disortir')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_disortir' => $now, 'updated_at' => $now]);
        }

        // c. Disortir → Dikirim dari gudang (1 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Disortir')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(1))
            ->update([
                'status_kirim' => 'Dikirim dari gudang',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Dikirim dari gudang')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_dikirim_gudang' => $now, 'updated_at' => $now]);
        }

        // d. Dikirim dari gudang → Sampai gudang tujuan (6 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Dikirim dari gudang')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(6))
            ->update([
                'status_kirim' => 'Sampai gudang tujuan',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Sampai gudang tujuan')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_sampai_gudang_tujuan' => $now, 'updated_at' => $now]);
        }

        // e. Sampai gudang tujuan → Diantar kurir (2 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Sampai gudang tujuan')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(2))
            ->update([
                'status_kirim' => 'Diantar kurir',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Diantar kurir')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_diantar_kurir' => $now, 'updated_at' => $now]);
        }

        // f. Diantar kurir → Sampai tujuan (4 menit)
        $affected = DB::table('ekspedisi')
            ->where('status_kirim', 'Diantar kurir')
            ->where('estimasi_waktu', 'like', '%hari%')
            ->whereNotNull('keterangan_waktu')
            ->where('keterangan_waktu', '<=', $now->copy()->subMinutes(4))
            ->update([
                'status_kirim' => 'Sampai tujuan',
                'keterangan_waktu' => $now
            ]);

        if ($affected) {
            $ids = DB::table('ekspedisi')
                ->select('id_beli')
                ->where('status_kirim', 'Sampai tujuan')
                ->where('estimasi_waktu', 'like', '%hari%')
                ->where('keterangan_waktu', $now)
                ->pluck('id_beli');

            DB::table('lacak_pesanan')
                ->whereIn('id_beli', $ids)
                ->update(['waktu_sampai_tujuan' => $now, 'updated_at' => $now]);
        }

        $this->info('✅ Status pengiriman diperbarui: ' . $now->format('Y-m-d H:i:s'));
    }
}
