<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class M_Ulasan extends Model
{
    use HasFactory;

    protected $table = 'ulasan';
    protected $primaryKey = 'id_ulasan';
    public $timestamps = false;

    protected $fillable = [
        'id_stok',
        'id_pelanggan',
        'nama_user',
        'rating',
        'komentar',
        'terverifikasi',
        'tanggal_ulasan',
    ];

    public static function getByNamaProduk($namaProduk)
    {
        return DB::table('ulasan')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->where('stok_produk.nama_produk', $namaProduk)
            ->where('ulasan.terverifikasi', 1) // hanya ulasan yang sudah disetujui admin
            ->select(
                'ulasan.*',
                'stok_produk.nama_produk',
                'stok_produk.sesi_user'
            )
            ->orderBy('ulasan.tanggal_ulasan', 'desc')
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->all();
    }

    public static function getRingkasanByNamaProduk($namaProduk)
    {
        return DB::table('ulasan')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->where('stok_produk.nama_produk', $namaProduk)
            ->where('ulasan.terverifikasi', 1)
            ->selectRaw('COUNT(*) as total, ROUND(AVG(ulasan.rating), 1) as rata, SUM(CASE WHEN ulasan.rating >= 4 THEN 1 ELSE 0 END) as rekomendasi')
            ->first();
    }

    public static function getByToko($sesiUser, $limit = 10)
    {
        return DB::table('ulasan')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->where('stok_produk.sesi_user', $sesiUser)
            ->select(
                'ulasan.*',
                'stok_produk.nama_produk',
                'stok_produk.sesi_user'
            )
            ->orderBy('ulasan.tanggal_ulasan', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->all();
    }

    public static function getRingkasanByToko($sesiUser)
    {
        return DB::table('ulasan')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->where('stok_produk.sesi_user', $sesiUser)
            ->selectRaw('COUNT(*) as total, ROUND(AVG(ulasan.rating), 1) as rata, SUM(CASE WHEN ulasan.rating >= 4 THEN 1 ELSE 0 END) as rekomendasi')
            ->first();
    }

    public static function ringkasanPerProduk()
    {
        $rows = DB::table('ulasan')
            ->join('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->select(
                'stok_produk.nama_produk',
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND(AVG(ulasan.rating), 1) as rata')
            )
            ->groupBy('stok_produk.nama_produk')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->nama_produk] = [
                'total' => (int) $row->total,
                'rata' => (float) $row->rata,
            ];
        }

        return $map;
    }
}
