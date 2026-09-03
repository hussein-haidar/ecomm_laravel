<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Home_superadmin;

class Home_superadmin extends WebsiteController
{
    protected $M_Home_superadmin;

    public function __construct()
    {
        parent::__construct();
        $this->M_Home_superadmin = new M_Home_superadmin();
    }

    public function index(Request $request)
    {
        $superadmin = session('superadmin');
        $title = "Dashboard " . ($superadmin['nama_title'] ?? 'Superadmin');

        $tahun = $request->input('tahun', date('Y'));
        $bulan_input = $request->input('bulan');
        $bulan = $bulan_input ? str_pad($bulan_input, 2, '0', STR_PAD_LEFT) : null;

        $jumlahToko = $this->M_Home_superadmin->getJumlahToko();

        $data = [
            'title' => $title,
            'title2' => 'Index',
            'tot_bank' => $this->M_Home_superadmin->tot_bank_by_sesi(),
            'tot_kurir' => $this->M_Home_superadmin->tot_kurir_by_sesi(),
            'tot_toko' => $this->M_Home_superadmin->tot_toko_by_sesi(),
            'tot_komisi' => $this->M_Home_superadmin->tot_komisi_by_sesi(),
            'tahun' => $tahun,
            'bulan' => $bulan_input,
            'bulan_nama' => $bulan ? date('F', mktime(0, 0, 0, (int)$bulan, 1)) : null,
            'komisi_per_toko' => $this->M_Home_superadmin->getKomisiPerToko($tahun, $bulan),
            'jumlah_toko_total' => $jumlahToko['total'],
            'jumlah_toko_aktif' => $jumlahToko['aktif'],
        ];

        return view('superadmin.home_superadmin', $data);
    }
}
