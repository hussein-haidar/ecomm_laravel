<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Home_superadmin
{
    public function tot_bank_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('bank')->where('sesi_user', $sesiUser)->get();
    }

    public function tot_kurir_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('kurir')->where('sesi_user', $sesiUser)->get();
    }

    public function tot_toko_by_sesi()
    {
        return DB::table('website')->get();
    }

    public function tot_komisi_by_sesi()
    {
        return DB::table('keuntungan_toko as kt')
            ->select(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat',
                'pembelian.id_beli',
                'pembelian.total_harga',
                'pembelian.waktu_pembelian',
                'biaya_platform.persentase as biaya_platform',
                DB::raw('MAX(pembelian.total_harga * biaya_platform.persentase / 100) as potongan_biaya'),
                DB::raw('MAX(pembelian.total_harga - (pembelian.total_harga * biaya_platform.persentase / 100)) as total_harga_new')
            )
            ->leftJoin('website', 'website.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('pembelian', 'pembelian.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('biaya_platform', 'biaya_platform.id_biaya', '=', 'kt.id_biaya')
            ->groupBy(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat',
                'pembelian.id_beli',
                'pembelian.total_harga',
                'pembelian.waktu_pembelian',
                'biaya_platform.persentase'
            )
            ->orderByDesc('id_keuntungan')
            ->get();
    }

    // ✅ Fungsi utama: komisi per toko dengan filter bulan & tahun
    public function getKomisiPerToko($tahun, $bulan = null)
    {
        $query = DB::table('keuntungan_toko as kt')
            ->select(
                'website.nama_toko',
                DB::raw('COUNT(pembayaran.id_bayar) as jumlah_transaksi'),
                DB::raw('SUM(pembayaran.total_harga - (pembayaran.total_harga * biaya_platform.persentase / 100)) as total_komisi')
            )
            ->leftJoin('website', 'website.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'kt.id_bayar')
            ->leftJoin('biaya_platform', 'biaya_platform.id_biaya', '=', 'kt.id_biaya')
            ->where('kt.status_masuk_untung', 'Berhasil')
            ->whereNotNull('pembayaran.id_bayar')
            ->whereNotNull('pembayaran.waktu_pembayaran');

        if ($bulan) {
            $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
            $query->whereRaw("pembayaran.waktu_pembayaran LIKE ?", ["{$tahun}-{$bulan}-%"]);
        } else {
            $query->whereRaw("pembayaran.waktu_pembayaran LIKE ?", ["{$tahun}-%"]);
        }

        return $query->groupBy('website.id_website', 'website.nama_toko')
            ->orderByDesc('total_komisi')
            ->limit(10)
            ->get();
    }

    public function getJumlahToko()
    {
        $total = DB::table('website')->count();
        $aktif = DB::table('website')->where('status_website', 'Aktif')->count();
        return ['total' => $total, 'aktif' => $aktif];
    }
}
