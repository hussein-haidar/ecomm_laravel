<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_TemplateSyaket extends Model
{
    protected $table = 'template_syaket';
    protected $primaryKey = 'id_tpl_syaket';

    protected $fillable = [
        'tipe', 'judul', 'isi', 'urutan', 'status'
    ];

    public function getSyaket()
    {
        return $this->orderBy('urutan', 'ASC')->orderBy('id_tpl_syaket', 'ASC')->get();
    }

    public function getSyaketById($id)
    {
        return $this->findOrFail($id);
    }
}