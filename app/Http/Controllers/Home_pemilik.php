<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Home_pemilik;
use App\Models\M_Stok;
use App\Models\M_Home_admin;

class Home_pemilik extends Controller
{
    protected $M_Home_pemilik;
    protected $M_Stok;
    protected $M_Home_admin;

    public function __construct()
    {
        $this->M_Home_pemilik = new M_Home_pemilik();
        $this->M_Stok = new M_Stok();
        $this->M_Home_admin = new M_Home_admin();
    }

    public function index(Request $request)
    {
        // Ambil data session user
        $user = session('user'); // Sesuaikan sesuai cara Anda menyimpan session
        $title = "Dashboard " . ($user['nama_title'] ?? 'Admin');

        // Ambil parameter tahun dan bulan
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        // Ambil data stok berdasarkan cabang
        $dataStok = $this->M_Stok->getStok();

        // Panggil model untuk ambil data
        $model = new M_Home_admin();

        $data = [
            'title' => $title,
            'title2' => 'Index',
            'tot_jenis' => $this->M_Home_pemilik->tot_jenis_by_sesi(),
            'tot_varian' => $this->M_Home_pemilik->tot_varian_by_sesi(),
            'tot_produk' => $this->M_Home_pemilik->tot_produk_by_sesi(),
            'tot_stok' => $this->M_Home_pemilik->tot_stok_by_sesi(),
            'tot_penjualan' => $model->totPenjualanBySesi(),
            'tot_nilai_penjualan' => $model->totNilaiPenjualanBySesi(),
            'tahun' => $tahun,
            'bulan' => $bulan,
            'grafik_penjualan' => $model->getPenjualanPerBulan($tahun, $bulan),
            'grafik_nilai' => $model->getNilaiPenjualanPerBulan($tahun, $bulan),
            'produk_terlaris' => $model->getProdukTerlaris($tahun),
            'produk_tersedikit' => $model->getProdukTersedikit($tahun),
            'produk_nilai_tertinggi' => $model->getProdukNilaiTertinggi($tahun),
            'produk_nilai_terendah' => $model->getProdukNilaiTerendah($tahun),
            'data_stok' => $dataStok,
        ];
        return view('pemilik.home_pemilik', $data);
    }
}
