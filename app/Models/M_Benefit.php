<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Benefit extends Model
{
    protected $table = 'keuntungan_toko';

    protected $primaryKey = 'id_keuntungan';

    public $timestamps = false;

    protected $fillable = [
        'sesi_user',
        'id_bayar',
        'id_website',
        'id_biaya',
        'status_masuk_untung',
        'created_at',
        'updated_at',
    ];
    //admin//
    public function getBenefit_By_User()
    {
        $sesi_user = Session::get('sesi_user');

        return DB::table('keuntungan_toko as kt')
            ->select(
                'kt.sesi_user',
                'w.nama_toko',
                'w.logo_website',
                'p.total_harga',
                'p.waktu_pembayaran',
                'bp.persentase as biaya_platform',
                DB::raw('(p.total_harga * bp.persentase / 100) as potongan_biaya'),
                DB::raw('(p.total_harga - (p.total_harga * bp.persentase / 100)) as total_harga_new')
            )
            ->leftJoin('website as w', 'w.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('pembayaran as p', 'p.id_bayar', '=', 'kt.id_bayar')
            ->leftJoin('biaya_platform as bp', 'bp.id_biaya', '=', 'kt.id_biaya')
            ->where('kt.sesi_user', $sesi_user)
            ->where('kt.status_masuk_untung', 'Berhasil')
            ->whereNotNull('p.id_bayar')
            ->orderByDesc('kt.id_keuntungan')
            ->get();
    }

    //admin//
    public function getTotBenefit_By_User()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('keuntungan_toko as kt')
            ->select(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat',
                DB::raw('COUNT(pembayaran.id_bayar) as jumlah_transaksi'),
                DB::raw('SUM(pembayaran.total_harga) as total_pendapatan_kotor'),
                DB::raw('MAX(biaya_platform.persentase) as biaya_platform'),
                DB::raw('SUM(pembayaran.total_harga * biaya_platform.persentase / 100) as total_potongan_biaya'),
                DB::raw('SUM(pembayaran.total_harga - (pembayaran.total_harga * biaya_platform.persentase / 100)) as total_keuntungan_bersih')
            )
            ->leftJoin('website', 'website.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('pembayaran', 'pembayaran.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('biaya_platform', 'biaya_platform.id_biaya', '=', 'kt.id_biaya')
            ->whereNotNull('pembayaran.id_bayar') // Hanya hitung yang punya transaksi
            ->where('kt.sesi_user', $sesi_user)
            ->where('kt.status_masuk_untung', 'Berhasil')
            ->groupBy(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat'
            )
            ->orderByDesc('total_keuntungan_bersih')
            ->get();
    }

    //superadmin//
    public function getTotBenefit()
    {
        return DB::table('keuntungan_toko as kt')
            ->select(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat',
                DB::raw('COUNT(pembayaran.id_bayar) as jumlah_transaksi'),
                DB::raw('SUM(pembayaran.total_harga) as total_pendapatan_kotor'),
                DB::raw('MAX(biaya_platform.persentase) as biaya_platform'),
                DB::raw('SUM(pembayaran.total_harga * biaya_platform.persentase / 100) as total_potongan_biaya'),
                DB::raw('SUM(pembayaran.total_harga - (pembayaran.total_harga * biaya_platform.persentase / 100)) as total_keuntungan_bersih')
            )
            ->leftJoin('website', 'website.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('pembayaran', 'pembayaran.sesi_user', '=', 'kt.sesi_user')
            ->leftJoin('biaya_platform', 'biaya_platform.id_biaya', '=', 'kt.id_biaya')
            ->whereNotNull('pembayaran.id_bayar') // Hanya hitung yang punya transaksi
            ->groupBy(
                'website.nama_toko',
                'website.logo_website',
                'website.sesi_user',
                'website.alamat_pusat',
                'website.wa_pusat'
            )
            ->orderByDesc('total_keuntungan_bersih')
            ->get();
    }
}
