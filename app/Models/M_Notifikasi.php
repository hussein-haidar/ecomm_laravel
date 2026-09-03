<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan', 'nama_pelanggan', 'sesi_user', 'judul', 'pesan', 'tipe', 'link', 'dibaca', 'waktu'
    ];

    public static function getNotifikasi($idPelanggan)
    {
        return DB::table('notifikasi')
            ->where('id_pelanggan', $idPelanggan)
            ->orderByDesc('waktu')
            ->limit(20)
            ->get();
    }

    public static function getUnreadCount($idPelanggan)
    {
        return DB::table('notifikasi')
            ->where('id_pelanggan', $idPelanggan)
            ->where('dibaca', false)
            ->count();
    }

    public static function markAsRead($idNotifikasi)
    {
        DB::table('notifikasi')->where('id_notifikasi', $idNotifikasi)->update(['dibaca' => true]);
    }

    public static function markAllRead($idPelanggan)
    {
        DB::table('notifikasi')->where('id_pelanggan', $idPelanggan)->update(['dibaca' => true]);
    }

    public static function kirim($data)
    {
        $data['waktu'] = $data['waktu'] ?? now();
        $data['dibaca'] = false;
        DB::table('notifikasi')->insert($data);
    }
}
