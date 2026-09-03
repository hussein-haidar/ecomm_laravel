<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class M_Kurir extends Model
{
    protected $table = 'kurir';
    protected $primaryKey = 'id_kurir';
    protected $fillable = [
        'sesi_user',
        'jenis_kurir',
        'tipe_kurir',
        'ongkir',
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;
    
    public function getKurir()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table($this->table)
            ->where('kurir.sesi_user', $sesi_user)
            ->orderByDesc($this->primaryKey)
            ->get()
            ->toArray();
    }

    public function detailKurir($id_kurir)
    {
        return DB::table($this->table)
            ->where('id_kurir', $id_kurir)
            ->first();
    }

    public function add(array $data)
    {
        DB::table($this->table)->insert($data);
    }

    public function edit(array $data)
    {
        DB::table($this->table)
            ->where($this->primaryKey, $data[$this->primaryKey])
            ->update($data);
    }

    public function delete_data(array $data)
    {
        DB::table($this->table)
            ->where($this->primaryKey, $data[$this->primaryKey])
            ->delete();
    }
}
