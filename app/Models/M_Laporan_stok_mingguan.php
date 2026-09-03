<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class M_Laporan_stok_mingguan extends Model
{
    use HasFactory;

    protected $table = 'stok_produk';
    protected $primaryKey = 'id_stok';
    protected $fillable = [
        'jumlah_stok_produk',
        'tanggal_masuk_produk',
        'nama_produk',
    ];

    public function allProduk()
    {
        return DB::table('tbl_data_produk')->get();
    }

    public function getLaporanByDateRange($start_date, $end_date)
    {
        return $this->select('stok_produk.*', 'produk.*')
        ->join('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->whereBetween('stok_produk.tanggal_masuk_produk', [$start_date, $end_date])
            ->orderBy('id_stok', 'DESC')
            ->get();
    }
       // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;   // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
}
