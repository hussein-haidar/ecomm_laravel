<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Produk extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel  
    protected $table = 'produk';

    // Tentukan primary key yang digunakan di tabel (misalnya kode_produk)  
    protected $primaryKey = 'id_produk';

    protected $dates = ['deleted_at'];

    // Tentukan atribut yang dapat diisi (mass assignable)  
    protected $fillable = [
        'sesi_user',
        'kode_produk',
        'jenis_produk',
        'varian_produk',
        'nama_produk',
        'carousel',
        'ukuran_produk',
        'deskripsi_produk',
        'harga_produk',
        'berat_produk',
        'satuan_berat',
        'foto_produk',
        'size_guide_image',
        'gallery_images',
        'video_url',
        'deleted_at'
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    public function getProduk()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('produk.*')
            ->where('produk.deleted_at', 0)
            ->where('produk.sesi_user', $sesi_user)
            ->orderBy('id_produk', 'DESC')
            ->get();
    }

    public function get_carousel()
    {
        return $this->select('produk.*')
            ->where('produk.deleted_at', 0)
            ->where('produk.carousel', 1)
            ->orderBy('id_produk', 'DESC')
            ->get()
            ->toArray();

        return $results;
    }

    public function get_produk_dihapus()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('produk.*')
            ->where('produk.deleted_at', 1)
            ->where('produk.sesi_user', $sesi_user)
            ->orderBy('id_produk', 'DESC')
            ->get();
    }

    public function getVarian()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('varian_produk.*')
            ->where('varian_produk.sesi_user', $sesi_user)
            ->orderBy('varian_produk', 'DESC')
            ->get();
    }

    public function getJenis()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('jenis_produk.*')
            ->orderBy('jenis_produk', 'DESC')
            ->where('.sesi_user', $sesi_user)
            ->get();
    }

    public function getSatuan()
    {
        return $this->select('satuan_produk.*')
            ->orderBy('id_satuan', 'DESC')
            ->get();
    }
}
