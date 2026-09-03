<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Home_pemilik
{
    public function tot_jenis_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('jenis_produk')
            ->where('sesi_user', $sesiUser)
            ->get();
    }

    public function tot_varian_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('varian_produk')
            ->where('sesi_user', $sesiUser)
            ->get();
    }

    public function tot_produk_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('produk')
            ->where('sesi_user', $sesiUser)
            ->get();
    }

    public function tot_stok_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('stok_produk')
            ->where('sesi_user', $sesiUser)
            ->get();
    }

    public function tot_laporan_by_sesi()
    {
        $sesiUser = Session::get('sesi_user');
        return DB::table('stok_produk')
            ->where('sesi_user', $sesiUser)
            ->get();
    }
}
