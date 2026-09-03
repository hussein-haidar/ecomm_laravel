<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Stok;
use App\Models\M_Laporan_stok_mingguan;
use App\Models\M_Website;
use Barryvdh\DomPDF\Facade\Pdf;

class Admin_laporan_mingguan extends Controller
{
    protected $M_Stok;
    protected $M_Laporan_stok_mingguan;

    public function __construct(M_Stok $M_Stok, M_Laporan_stok_mingguan $M_Laporan_stok_mingguan)
    {
        $this->M_Stok = $M_Stok;
        $this->M_Laporan_stok_mingguan = $M_Laporan_stok_mingguan ;
    }

    public function laporanStok(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $data_laporan_admin = ($start_date && $end_date)
            ? $this->M_Laporan_stok_mingguan->getLaporanByDateRange($start_date, $end_date)
            : $this->M_Stok->getStok();

        return view('admin.laporan_stok_admin_mingguan.v_laporan_admin', [
            'title' => 'Daftar Laporan Stok Produk Mingguan',
            'title2' => 'Data Laporan Stok Produk Mingguan',
            'data_laporan_admin' => $data_laporan_admin,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function filterStokByDate(Request $request)
    {
        return redirect()->route('admin_laporan_mingguan.laporanStok', [
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ]);
    }

    public function cetakLaporanStok(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $data_laporan_admin = $this->M_Laporan_stok_mingguan->getLaporanByDateRange($start_date, $end_date);

        $toko = (new M_Website())->getWebsite_By_Sesi()->first();

        $pdf = Pdf::loadView('admin.laporan_stok_admin_mingguan.v_cetak_laporan_admin_pdf', [
            'title3' => 'Cetak Laporan Stok Produk Mingguan',
            'data_laporan_admin' => $data_laporan_admin,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'toko' => $toko,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Stok-Mingguan.pdf');
    }

    public function resetFilterStok()
    {
        return redirect()->route('admin_laporan_mingguan.laporanStok');
    }
}
