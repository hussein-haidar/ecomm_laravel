<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class M_Laporan_stok_bulanan extends Model
{
    use HasFactory;

    protected $table = 'stok_produk';
    protected $primaryKey = 'id_stok';
    protected $fillable = [
        'jumlah_stok_produk',
        'tanggal_masuk_produk',
        'nama_produk',
    ];

    public function getStok()
    {
        return $this->select('stok_produk.*', 'produk.*')
        ->join('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
        ->orderBy('id_stok', 'DESC')
        ->get();
    }

    public function allProduk()
    {
        return DB::table('produk')->get();
    }

    public function getLaporanByMonthRange($start_month, $end_month)
    {
        $start_date = date('Y-m-01', strtotime($start_month));
        $end_date = date('Y-m-t', strtotime($end_month));

        return $this->select('stok_produk.*', 'produk.*')
        ->join('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->whereBetween('tanggal_masuk_produk', [$start_date, $end_date])
        ->orderBy('id_stok', 'DESC')
        ->get();
          
    }
    
    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;
    
}

