<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Superadmin extends Model
{
    use HasFactory;

    // Specify the table name if it's different from the default naming convention
    protected $table = 'superadmin';

    // Specify the primary key
    protected $primaryKey = 'id_user';

    // Disable timestamps if not used
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'sesi_user',
        'username',
        'password',
        'fullname',
        'nama_title',
        'level',
        'foto_user',
        'last_login',
    ];

    public function getUser()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('superadmin.*')
            ->where('superadmin.sesi_user', $sesi_user)
            ->orderBy('id_user', 'DESC')
            ->get();
    }

    public function getProfile($id_user)
    {
        return $this->where('id_user', $id_user)
            ->first(); // ambil satu baris data saja
    }

    public function getUserById($sesi_user)
    {
        return $this->where('superadmin.sesi_user', $sesi_user)
            ->first(); // ambil satu baris data saja
    }

    public function detailProfile($id_user)
    {
        return $this->where('id_user', $id_user)->first();
    }

    public function edit($id_user, $data)
    {
        $result = DB::table($this->table)
            ->where($this->primaryKey, $id_user)
            ->update($data);

        return $result ? true : false;
    }
}
