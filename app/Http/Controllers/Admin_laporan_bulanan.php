<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Stok;
use App\Models\M_Laporan_stok_bulanan;
use App\Models\M_Website;
use Barryvdh\DomPDF\Facade\Pdf;

class Admin_laporan_bulanan extends Controller
{
    protected $M_Stok;
    protected $M_Laporan_stok_bulanan;

    public function __construct(M_Stok $M_Stok, M_Laporan_stok_bulanan $M_Laporan_stok_bulanan)
    {
        $this->M_Stok = $M_Stok;
        $this->M_Laporan_stok_bulanan = $M_Laporan_stok_bulanan;
    }

    public function laporanStok()
    {
        $data = [
            'title' => 'Daftar Laporan Stok Produk Bulanan',
            'title2' => 'Data Laporan Stok Produk Bulanan',
            'data_laporan_admin' => $this->M_Stok->getStok(),
            'start_month' => null,
            'end_month' => null,
        ];

        return view('admin.laporan_stok_admin_bulanan.v_laporan_admin', $data);
    }

    public function filterStokByMonth(Request $request)
    {
        $start_month = $request->query('start_month');
        $end_month = $request->query('end_month');

        // Ambil data laporan jika bulan valid, jika tidak ambil stok biasa
        $data_laporan_admin = ($start_month && $end_month)
            ? $this->M_Laporan_stok_bulanan->getLaporanByMonthRange($start_month, $end_month)
            : $this->M_Stok->getStok();

        return view('admin.laporan_stok_admin_bulanan.v_laporan_admin', [
            'title' => 'Daftar Laporan Stok Produk Bulanan',
            'title2' => 'Data Laporan Stok Produk Bulanan',
            'data_laporan_admin' => $data_laporan_admin,
            'start_month' => $start_month,
            'end_month' => $end_month,
        ]);
    }

    public function cetakLaporanStok(Request $request)
    {
        $start_month = $request->query('start_month');
        $end_month = $request->query('end_month');

        $data_laporan_pemilik = ($start_month && $end_month)
            ? $this->M_Laporan_stok_bulanan->getLaporanByMonthRange($start_month, $end_month)
            : $this->M_Stok->getStok();

        $toko = (new M_Website())->getWebsite_By_Sesi()->first();

        $pdf = Pdf::loadView('admin.laporan_stok_admin_bulanan.v_cetak_laporan_admin_pdf', [
            'title3' => 'Cetak Laporan Stok Produk Bulanan',
            'data_laporan_admin' => $data_laporan_pemilik,
            'start_month' => $start_month,
            'end_month' => $end_month,
            'toko' => $toko,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Stok-Bulanan.pdf');
    }

    public function resetFilterStok()
    {
        return redirect()->route('admin_laporan_bulanan.laporanStok');
    }
}
