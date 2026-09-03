<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_Satuan extends Model
{
    protected $table = 'satuan_produk';
    protected $primaryKey = 'id_satuan';
    protected $fillable = [
        'sesi_user',
        'satuan_produk',
        'jenis_satuan'
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    public function getSatuan()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('satuan_produk.*')
            ->where('satuan_produk.sesi_user', $sesi_user)
            ->orderBy('id_satuan', 'DESC')
            ->get();
    }
}
