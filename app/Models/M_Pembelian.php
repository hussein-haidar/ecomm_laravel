<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id_beli';
    public $timestamps = false;

    protected $fillable = [
        'kode_beli',
        'sesi_user',
        'nama_toko',
        'id_pelanggan',
        'nama_pelanggan',
        'id_stok',
        'id_bayar',
        'nama_produk',
        'ukuran_produk',
        'jumlah_produk',
        'satuan_produk',
        'total_harga',
        'total_berat',
        'satuan_berat',
        'status_beli',
        'waktu_pembelian'
    ];

    public static function get_beli_by_sesi()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('pembelian')
            ->select('pembelian.*', 'produk.*') // hindari select *
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk') // ✅ Gunakan id_produk
            ->where('pembelian.sesi_user', $sesi_user) // ✅ Pastikan nama_pelanggan ada di pembelian
            ->where('pembelian.status_beli', 'Dibayar')
            ->orderByDesc('pembelian.id_beli')
            ->get()
            ->toArray();
    }

    public static function get_beli_by_status()
    {
        $nama_pelanggan = Session::get('nama_pelanggan');
        return DB::table('pembelian')
            ->select('pembelian.*', 'produk.*') // hindari select *
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk') // ✅ Gunakan id_produk
            ->where('pembelian.nama_pelanggan', $nama_pelanggan) // ✅ Pastikan nama_pelanggan ada di pembelian
            ->where('pembelian.status_beli', 'Berhasil')
            ->orderByDesc('pembelian.id_beli')
            ->get()
            ->toArray();
    }

    public static function get_status_beli_by_status()
    {
        $id_pelanggan = Session::get('id_pelanggan');

        return DB::table('pembelian')
            ->select(
                'pembelian.*',
                'produk.nama_produk',
                'produk.foto_produk',
                'pembayaran.status_bayar',
                'ekspedisi.status_kirim',
                'ekspedisi.kode_resi',
                'lacak_pesanan.waktu_pesanan_diterima'
            )
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            // 🔥 JOIN EKSPEDISI BERDASARKAN id_beli, BUKAN id_bayar!
            ->leftJoin('ekspedisi', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            // 🔥 JOIN LACAK BERDASARKAN id_beli
            ->leftJoin('lacak_pesanan', 'lacak_pesanan.id_beli', '=', 'pembelian.id_beli')
            ->where('pembelian.status_beli', 'Berhasil')
            ->where('pembelian.id_pelanggan', $id_pelanggan)
            // ✅ Sekarang ini pasti 1-ke-1
            ->where('ekspedisi.status_kirim', 'Pesanan diterima')
            ->orderByDesc('pembelian.id_beli');
    }

    public function getTotJual()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('pembelian as pb')
            ->leftJoin('produk as p', 'p.nama_produk', '=', 'pb.nama_produk')
            ->select(
                'pb.nama_produk',
                DB::raw('MAX(pb.kode_beli) as kode_beli'),
                DB::raw('SUM(pb.jumlah_produk) as total_jual'),
                DB::raw('MAX(pb.satuan_produk) as satuan_produk'),
                DB::raw('SUM(pb.jumlah_produk * p.harga_produk) as total_harga'),
                DB::raw('MAX(pb.total_berat) as total_berat'),
                DB::raw('MAX(pb.satuan_berat) as satuan_berat'),
                DB::raw('MAX(p.foto_produk) as foto_produk'), // ✅ Ditambahkan
                DB::raw('MAX(pb.id_beli) as id_beli')
            )
            ->where('pb.sesi_user', $sesi_user)
            ->groupBy('pb.nama_produk')
            ->orderByDesc('id_beli')
            ->get();
    }

    public static function detailBeli($id_beli)
    {
        return DB::table('pembelian')
            ->select(
                'produk.*',
                'pembelian.*',
                'pembayaran.*',
                'ekspedisi.*',
                'pelanggan.*',
                'website.*',
            )
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->leftJoin('pelanggan', 'pelanggan.nama_pelanggan', '=', 'pembelian.nama_pelanggan')
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('ekspedisi', 'ekspedisi.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('website', 'website.nama_toko', '=', 'pembelian.nama_toko')
            ->where('pembelian.id_beli', $id_beli)
            ->where('ekspedisi.status_kirim', 'Pesanan diterima')
            ->first();
    }

    public static function get_bank()
    {
        return DB::table('bank')
            ->where('deleted_at', 0)
            ->get()
            ->toArray();
    }

    public static function get_kurir()
    {
        return DB::table('kurir')
            ->where('deleted_at', 0)
            ->get()->getResultArray();
    }
}
