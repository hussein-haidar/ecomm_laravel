<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_Jenis extends Model
{
    protected $table = 'jenis_produk';
    protected $primaryKey = 'id_jenis';
    protected $fillable = [
        'sesi_user',
        'jenis_produk'
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    public function getJenis()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('jenis_produk.*')
            ->orderBy('jenis_produk', 'DESC')
            ->where('.sesi_user', $sesi_user)
            ->get();
    }
}
