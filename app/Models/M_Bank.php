<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class M_Bank extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel  
    protected $table = 'bank';

    // Tentukan primary key yang digunakan di tabel (misalnya kode_produk)  
    protected $primaryKey = 'id_bank';

    // Tentukan atribut yang dapat diisi (mass assignable)  
    protected $fillable = [
        'sesi_user',
        'nama_bank',
        'no_rek',
        'a_n',
        'deleted_at'
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    public function getBank()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('bank.*')
            ->where('bank.deleted_at', 0)
            ->where('bank.sesi_user', $sesi_user)
            ->orderBy('id_bank', 'DESC')
            ->get();
    }

    public static function add($data_ekspedisi)
    {
        return DB::table('bank')->insert($data_ekspedisi);
    }

    public function get_bank_dihapus()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('bank.*')
            ->where('bank.deleted_at', 0)
            ->where('bank.sesi_user', $sesi_user)
            ->orderBy('id_bank', 'DESC')
            ->get();
    }

}

?>

