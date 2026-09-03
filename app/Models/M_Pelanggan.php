<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class M_Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';

    // Disable timestamps if not used  
    public $timestamps = false;
    
    // Define the fillable attributes  
    protected $fillable = [
        'email',
        'password',
        'nama_pelanggan',
        'jenis_kelamin',
        'level',
        'tanggal_lahir',
        'no_telpon',
        'longitude',
        'latitude',
        'alamat',
        'foto_pelanggan',
        'last_login',
    ];

    public function get_profile()
    {
        
        return DB::table($this->table)
        
            ->get()
            ->toArray();
    }

    public function getUserById($nama_pelanggan)
    {
        return DB::table('pelanggan')
            ->select([
            'pelanggan.*',
            'pembelian.*',
            'pembayaran.*',
                ])
            ->join('pembelian', 'pelanggan.nama_pelanggan', '=', 'pembelian.nama_pelanggan')
            ->join('pembayaran', 'pelanggan.nama_pelanggan', '=', 'pembayaran.nama_pelanggan')
            ->where('pelanggan.nama_pelanggan', $nama_pelanggan)
            ->first();
    }

    // Get user by name
    public static function get_user_by_id($nama_pelanggan)
    {
        return DB::table('pelanggan')
            ->select([
            'pelanggan.*',
            ])
            ->where('nama_pelanggan', $nama_pelanggan)
            ->first();
    }


    public function detailProfile($id_pelanggan)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id_pelanggan)
            ->first();
    }

    public function add(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function findByEmail(string $email)
    {
        return DB::table($this->table)
            ->where('email', $email)
            ->first();
    }

    public function updateLastLogin(int $userId)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $userId)
            ->update(['last_login' => now()]);
    }

    public function updatePassword(int $id_pelanggan, string $password)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id_pelanggan)
            ->update(['password' => $password]);
    }

    public function edit(int $id_pelanggan, array $data): bool
    {
        try {
            $rowsAffected = DB::table($this->table)
                ->where($this->primaryKey, $id_pelanggan)
                ->update($data);

            return $rowsAffected > 0;
        } catch (\Exception $e) {
            Log::error("Error saat mengupdate data pengguna: " . $e->getMessage());
            return false;
        }
    }

    public function delete_data(int $id_pelanggan): bool
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id_pelanggan)
            ->delete();
    }
}
