<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_FaqToko extends Model
{
    protected $table = 'faq_toko';
    protected $primaryKey = 'id_faq_toko';

    protected $fillable = [
        'sesi_user', 'kategori', 'pertanyaan', 'jawaban', 'urutan', 'status'
    ];

    // Semua FAQ milik satu toko (sesi_user)
    public function getFaq()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->where('faq_toko.sesi_user', $sesi_user)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id_faq_toko', 'ASC')
            ->get();
    }

    public function getFaqById($id)
    {
        $sesi_user = Session::get('sesi_user');
        return $this->where('faq_toko.id_faq_toko', $id)
            ->where('faq_toko.sesi_user', $sesi_user)
            ->firstOrFail();
    }

    public static function getFaqBySesi($sesi_user)
    {
        return self::where('sesi_user', $sesi_user)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id_faq_toko', 'ASC')
            ->get();
    }

    public static function getKategoriList()
    {
        return ['pemesanan', 'pembayaran', 'pengiriman', 'retur', 'akun'];
    }
}