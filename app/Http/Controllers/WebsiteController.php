<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View; // Tambahkan ini

class WebsiteController extends Controller
{
    protected $dataWebsite;

    public function __construct()
    {
        // Ambil data website yang aktif
        $website = DB::table('website')
        ->where('status_website', 'Aktif')
            ->where('sesi_user', 'Superadmin')
        ->first();

        $this->dataWebsite = [
            'nama_toko' => $website ? $website->nama_toko : 'Nama Toko Default',
            'logo_website' => $website ? $website->logo_website : null,
            'wa_pusat' => $website ? $website->wa_pusat : '',
        ];

        // Share ke semua view
        View::share('dataWebsite', $this->dataWebsite);
    }
}
