<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Wishlist extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id_wishlist';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'id_stok',
        'tanggal_tambah',
    ];

    public static function getByPelanggan($idPelanggan)
    {
        return DB::table('wishlist')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'wishlist.id_stok')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->leftJoin('website', 'website.sesi_user', '=', 'stok_produk.sesi_user')
            ->where('wishlist.id_pelanggan', $idPelanggan)
            ->orderByDesc('wishlist.tanggal_tambah')
            ->select(
                'wishlist.id_wishlist',
                'wishlist.id_stok',
                'wishlist.tanggal_tambah',
                'stok_produk.nama_produk',
                'stok_produk.ukuran_produk',
                'stok_produk.harga_produk',
                'stok_produk.jumlah_stok_produk',
                'stok_produk.satuan_produk',
                'stok_produk.sesi_user',
                'website.nama_toko',
                'produk.foto_produk'
            )
            ->get();
    }

    public static function tambah($idPelanggan, $idStok)
    {
        $ada = self::where('id_pelanggan', $idPelanggan)->where('id_stok', $idStok)->first();

        if ($ada) {
            $ada->delete();
            return ['status' => 'removed'];
        }

        self::create([
            'id_pelanggan' => $idPelanggan,
            'id_stok' => $idStok,
            'tanggal_tambah' => now(),
        ]);

        return ['status' => 'added'];
    }

    public static function jumlahByPelanggan($idPelanggan)
    {
        return self::where('id_pelanggan', $idPelanggan)->count();
    }
}
