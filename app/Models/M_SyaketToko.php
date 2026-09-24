<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_SyaketToko extends Model
{
    protected $table = 'syaket_toko';
    protected $primaryKey = 'id_syaket_toko';

    protected $fillable = [
        'sesi_user', 'tipe', 'judul', 'isi', 'urutan', 'status'
    ];

    // Semua S&K milik satu toko (sesi_user)
    public function getSyaket()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->where('syaket_toko.sesi_user', $sesi_user)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id_syaket_toko', 'ASC')
            ->get();
    }

    public function getSyaketById($id)
    {
        $sesi_user = Session::get('sesi_user');
        return $this->where('syaket_toko.id_syaket_toko', $id)
            ->where('syaket_toko.sesi_user', $sesi_user)
            ->firstOrFail();
    }

    public static function getSyaketBySesi($sesi_user)
    {
        return self::where('sesi_user', $sesi_user)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id_syaket_toko', 'ASC')
            ->get();
    }
}