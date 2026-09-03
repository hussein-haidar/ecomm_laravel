<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Kupon extends Model
{
    protected $table = 'kupon';
    protected $primaryKey = 'id_kupon';
    public $timestamps = false;

    protected $fillable = [
        'kode_kupon', 'nama_kupon', 'tipe_diskon', 'nilai_diskon', 'min_pembelian', 'max_diskon',
        'kuota', 'terpakai', 'tanggal_mulai', 'tanggal_akhir', 'sesi_user', 'status_aktif', 'deleted_at'
    ];

    public static function getBySesi()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('kupon')
            ->where('sesi_user', $sesi_user)
            ->where('deleted_at', 0)
            ->orderByDesc('id_kupon')
            ->get();
    }

    public static function validasiKupon($kode, $totalBelanja)
    {
        $kupon = DB::table('kupon')
            ->where('kode_kupon', $kode)
            ->where('status_aktif', 'Aktif')
            ->where('deleted_at', 0)
            ->where('kuota', '>', DB::raw('terpakai'))
            ->first();

        if (!$kupon) return ['valid' => false, 'message' => 'Kupon tidak valid atau sudah habis'];

        // Validasi masa berlaku
        $hariIni = now()->toDateString();
        if (!empty($kupon->tanggal_mulai) && $hariIni < $kupon->tanggal_mulai) {
            return ['valid' => false, 'message' => 'Kupon belum mulai berlaku'];
        }
        if (!empty($kupon->tanggal_akhir) && $hariIni > $kupon->tanggal_akhir) {
            return ['valid' => false, 'message' => 'Kupon sudah kedaluwarsa'];
        }

        if ($totalBelanja < $kupon->min_pembelian) {
            return ['valid' => false, 'message' => 'Minimal pembelian Rp ' . number_format($kupon->min_pembelian, 0, ',', '.')];
        }

        $diskon = 0;
        if ($kupon->tipe_diskon == 'persen') {
            $diskon = $totalBelanja * $kupon->nilai_diskon / 100;
            if ($kupon->max_diskon > 0) $diskon = min($diskon, $kupon->max_diskon);
        } else {
            $diskon = min($kupon->nilai_diskon, $totalBelanja);
        }

        return ['valid' => true, 'kupon' => $kupon, 'diskon' => $diskon];
    }

    public static function gunakanKupon($idKupon)
    {
        DB::table('kupon')->where('id_kupon', $idKupon)->increment('terpakai');
    }
}
