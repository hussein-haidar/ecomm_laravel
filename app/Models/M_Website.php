<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Website extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel  
    protected $table = 'website';

    // Tentukan primary key yang digunakan di tabel (misalnya kode_produk)  
    protected $primaryKey = 'id_website';


    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    // Tentukan atribut yang dapat diisi (mass assignable)  
    protected $fillable = [
        'id_user',
        'id_promo',
        'level',
        'sesi_user',
        'nama_toko',
        'alamat_pusat',
        'latitude_pusat',
        'longitude_pusat',
        'wa_pusat',
        'latitude_cabang',
        'longitude_cabang',
        'alamat_cabang',
        'wa_cabang',
        'footer_title',
        'link_IG',
        'link_Tiktok',
        'link_FB',
        'logo_website',
        'bgd_web',
        'status_website',
        'alasan_nonaktif'
    ];

    public function getWebsite_By_Level()
    {
        return $this->select([
            'website.id_website',
            'website.id_user',
            'website.sesi_user', // pastikan ini diambil
            'website.nama_toko',
            'website.wa_pusat',
            'website.logo_website',
            'website.bgd_web',
            'website.status_website',
            'website.alasan_nonaktif',
            'event_promo.nama_promo', // atau kolom promo yang dibutuhkan
            // tambahkan kolom lain yang diperlukan
        ])
            ->leftJoin('event_promo', 'website.id_promo', '=', 'event_promo.id_promo')
            ->whereIn('website.level', ['pemilik'])
            ->orderBy('website.id_website', 'DESC')
            ->get();
    }

    public function getWebsite_By_Sesi()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('website.*', 'event_promo.*')
            ->leftJoin('event_promo', 'website.id_promo', '=', 'event_promo.id_promo')
            ->where('website.sesi_user', $sesi_user)
            ->orderBy('id_website', 'DESC')
            ->get();
    }

    public function getEvent()
    {
        return DB::table('event_promo')
            ->where('deleted_at', 0)
            ->orderBy('id_promo', 'DESC')
            ->get();
    }

}