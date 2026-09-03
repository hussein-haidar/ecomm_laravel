<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Home_admin;

class Home_admin extends WebsiteController
{
    protected $M_Home_admin;

    public function __construct()
    {
        // ✅ WAJIB: Panggil dulu constructor parent
        parent::__construct();
        
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

        // Panggil model untuk ambil data
        $model = new M_Home_admin();

        // Siapkan data untuk view
        $data = [
            'title' => $title,
            'title2' => 'Dashboard',
            'tot_jenis' => $model->totJenisBySesi(),
            'tot_varian' => $model->totVarianBySesi(),
            'tot_produk' => $model->totProdukBySesi(),
            'tot_stok' => $model->totStokBySesi(),
            'tot_jumlah_stok' => $model->totJumlahStokBySesi(),
            'tot_harga_stok' => $model->totHargaStokBySesi(),
            'tot_penjualan' => $model->totPenjualanBySesi(),
            'tot_nilai_penjualan' => $model->totNilaiPenjualanBySesi(),
            'tot_pembayaran_selesai' => $model->totPembayaranSelesai(),
            'tot_pembayaran_batal' => $model->totPembayaranBatal(),
            'tahun' => $tahun,
            'bulan' => $bulan,
            'grafik_penjualan' => $model->getPenjualanPerBulan($tahun, $bulan),
            'grafik_nilai' => $model->getNilaiPenjualanPerBulan($tahun, $bulan),
            'produk_terlaris' => $model->getProdukTerlaris($tahun),
            'produk_tersedikit' => $model->getProdukTersedikit($tahun),
            'produk_nilai_tertinggi' => $model->getProdukNilaiTertinggi($tahun),
            'produk_nilai_terendah' => $model->getProdukNilaiTerendah($tahun),
        ];

        return view('admin.home_admin', $data);
    }
}
