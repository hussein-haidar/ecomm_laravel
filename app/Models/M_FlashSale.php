<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_FlashSale extends Model
{
    protected $table = 'flash_sale';
    protected $primaryKey = 'id_flash_sale';
    public $timestamps = false;

    protected $fillable = [
        'nama_flash_sale', 'deskripsi', 'sesi_user', 'nama_toko', 'diskon_persen',
        'waktu_mulai', 'waktu_selesai', 'status', 'deleted_at'
    ];

    public static function getBySesi()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('flash_sale')
            ->where('sesi_user', $sesi_user)
            ->where('deleted_at', 0)
            ->orderByDesc('id_flash_sale')
            ->get();
    }

    public static function getActive()
    {
        $now = now();
        return DB::table('flash_sale')
            ->where('status', 'Aktif')
            ->where('waktu_mulai', '<=', $now)
            ->where('waktu_selesai', '>=', $now)
            ->where('deleted_at', 0)
            ->get();
    }

    public static function getItems($idFlashSale)
    {
        return DB::table('flash_sale_item')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'flash_sale_item.nama_produk')
            ->where('flash_sale_item.id_flash_sale', $idFlashSale)
            ->where('flash_sale_item.deleted_at', 0)
            ->get();
    }
}
