<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class M_Biaya_platform extends Model
{
    use HasFactory;

    protected $table = 'biaya_platform';

    protected $primaryKey = 'id_biaya';

    public $timestamps = false;

    protected $fillable = [
        'sesi_user',
        'persentase',
        'keterangan',
    ];

    public function getBiaya_ByUser()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('biaya_platform.*')
            ->where('biaya_platform.sesi_user', $sesi_user)
            ->where('biaya_platform.deleted_at', 0)
            ->orderBy('id_biaya', 'DESC')
            ->get();
    }

    public function getBiaya($id_biaya)
    {
        return DB::table('biaya_platform')
            ->where('id_biaya', $id_biaya)
            ->where('deleted_at', 0)
            ->first(); // ✅ ini mengembalikan objek tunggal
    }

    public static function add($data)
    {
        return DB::table('biaya_platform')->insert($data);
    }

    public function get_biaya_dihapus()
    {
        $sesi_user = Session::get('sesi_user');
        return $this->select('biaya_platform.*')
            ->where('biaya_platform.deleted_at', 0)
            ->where('biaya_platform.sesi_user', $sesi_user)
            ->orderBy('id_biaya', 'DESC')
            ->get();
    }
}
