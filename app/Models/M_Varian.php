<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_Varian extends Model
{
    protected $table = 'varian_produk';
    protected $primaryKey = 'id_varian';
    protected $fillable = [
        'sesi_user',
        'varian_produk'
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    public function getVarian()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('varian_produk.*')
            ->where('varian_produk.sesi_user', $sesi_user)
            ->orderBy('varian_produk', 'DESC')
            ->get();
    }
}
