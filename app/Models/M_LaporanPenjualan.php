<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_LaporanPenjualan extends Model
{
    public $timestamps = false;

    public static function getLaporanPenjualan($bulan = null, $tahun = null)
    {
        $sesi_user = Session::get('sesi_user');
        $query = DB::table('pembelian as pb')
            ->leftJoin('produk as p', 'p.nama_produk', '=', 'pb.nama_produk')
            ->leftJoin('pembayaran as bayar', 'bayar.id_bayar', '=', 'pb.id_bayar')
            ->select(
                'pb.nama_produk',
                'p.foto_produk',
                'p.harga_produk',
                DB::raw('SUM(pb.jumlah_produk) as total_terjual'),
                DB::raw('SUM(pb.total_harga) as total_pendapatan'),
                DB::raw('COUNT(pb.id_beli) as jumlah_transaksi')
            )
            ->where('pb.sesi_user', $sesi_user)
            ->where('pb.status_beli', 'Berhasil');

        if ($bulan) {
            $query->whereMonth('bayar.waktu_pembayaran', $bulan);
        }
        if ($tahun) {
            $query->whereYear('bayar.waktu_pembayaran', $tahun);
        }

        return $query->groupBy('pb.nama_produk', 'p.foto_produk', 'p.harga_produk')
            ->orderByDesc('total_pendapatan')
            ->get();
    }

    public static function getTotalPendapatan($bulan = null, $tahun = null)
    {
        $sesi_user = Session::get('sesi_user');
        $query = DB::table('pembelian as pb')
            ->leftJoin('pembayaran as bayar', 'bayar.id_bayar', '=', 'pb.id_bayar')
            ->selectRaw('SUM(pb.total_harga) as total, COUNT(pb.id_beli) as transaksi, SUM(pb.jumlah_produk) as terjual')
            ->where('pb.sesi_user', $sesi_user)
            ->where('pb.status_beli', 'Berhasil');

        if ($bulan) $query->whereMonth('bayar.waktu_pembayaran', $bulan);
        if ($tahun) $query->whereYear('bayar.waktu_pembayaran', $tahun);

        return $query->first();
    }

    public static function getGrafikPenjualan($tahun = null)
    {
        $sesi_user = Session::get('sesi_user');
        $tahun = $tahun ?? date('Y');
        return DB::table('pembelian as pb')
            ->leftJoin('pembayaran as bayar', 'bayar.id_bayar', '=', 'pb.id_bayar')
            ->select(
                DB::raw('MONTH(bayar.waktu_pembayaran) as bulan'),
                DB::raw('SUM(pb.total_harga) as total_pendapatan'),
                DB::raw('COUNT(pb.id_beli) as jumlah_transaksi'),
                DB::raw('SUM(pb.jumlah_produk) as jumlah_terjual')
            )
            ->where('pb.sesi_user', $sesi_user)
            ->where('pb.status_beli', 'Berhasil')
            ->whereYear('bayar.waktu_pembayaran', $tahun)
            ->groupBy(DB::raw('MONTH(bayar.waktu_pembayaran)'))
            ->orderBy('bulan')
            ->get();
    }
}
