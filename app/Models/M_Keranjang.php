<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'alamat',
        'latitude',
        'longitude',
        'id_stok',
        'nama_produk',
        'ukuran_produk',
        'jumlah_produk',
        'satuan_produk',
        'harga_produk',
        'berat_produk',
        'satuan_berat',
        'total_harga',
        'sesi_user',
        'nama_toko',
        'status_keranjang',
        'waktu_ditambahkan'
    ];

    public static function get_searchKeranjang()
    {
        $nama_pelanggan = Session::get('nama_pelanggan');

        return DB::table('keranjang')
            ->select(
                'keranjang.*',
                'produk.foto_produk',
                'stok_produk.jumlah_stok_produk'
            )
            ->leftJoin('stok_produk', 'stok_produk.id_stok', '=', 'keranjang.id_stok')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->where('keranjang.nama_pelanggan', $nama_pelanggan)
            ->where('keranjang.status_keranjang', 'Proses')
            ->orderByDesc('keranjang.id_keranjang')
            ->distinct();
        // ❗ JANGAN panggil ->get() atau ->toArray() di sini!
    }
    
    // Delete cart item by ID
    public static function delete_data($id_keranjang)
    {
        return DB::table('keranjang')
            ->where('id_keranjang', $id_keranjang)
            ->delete();
    }
}
