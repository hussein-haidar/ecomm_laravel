<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class M_Event extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel  
    protected $table = 'event_promo';

    // Tentukan primary key yang digunakan di tabel (misalnya kode_produk)  
    protected $primaryKey = 'id_promo';

    // Tentukan atribut yang dapat diisi (mass assignable)  
    protected $fillable = [
        'sesi_user',
        'nama_promo',
        'persentase',
        'deskripsi',
        'status_event',
        'gambar_event',
        'gambar_event2',
        'gambar_event3',
        'waktu_mulai',
        'waktu_berakhir',
        'deleted_at'
    ];

    public function getEvent_By_Sesi()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('event_promo.*')
            ->where('event_promo.deleted_at', 0)
            ->where('event_promo.sesi_user', $sesi_user)
            ->orderBy('id_promo', 'DESC')
            ->get();
    }

    public function getEvent()
    {
        return $this->select('event_promo.*')
            ->where('event_promo.deleted_at', 0)
            ->orderBy('id_promo', 'DESC')
            ->get();
    }
}
