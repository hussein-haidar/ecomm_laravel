<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class M_TemplateFaq extends Model
{
    protected $table = 'template_faq';
    protected $primaryKey = 'id_tpl_faq';

    protected $fillable = [
        'kategori', 'pertanyaan', 'jawaban', 'urutan', 'status'
    ];

    public function getFaq()
    {
        return $this->orderBy('urutan', 'ASC')->orderBy('id_tpl_faq', 'ASC')->get();
    }

    public function getFaqById($id)
    {
        return $this->findOrFail($id);
    }

    // Kategori untuk dropdown / filter
    public static function getKategoriList()
    {
        return ['pemesanan', 'pembayaran', 'pengiriman', 'retur', 'akun'];
    }
}