<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_home_admin
{
    // Ambil session user
    protected function getSesiUser()
    {
        return Session::get('sesi_user');
    }

    // Fungsi total per sesi user
    public function totJenisBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('jenis_produk')
            ->where('sesi_user', $sesi_user)
            ->get()
            ->toArray();
    }

    public function totVarianBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('varian_produk')
            ->where('sesi_user', $sesi_user)
            ->get()
            ->toArray();
    }

    public function totProdukBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('produk')
            ->where('sesi_user', $sesi_user)
            ->get()
            ->toArray();
    }

    public function totStokBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('stok_produk')
            ->where('sesi_user', $sesi_user)
            ->get()
            ->toArray();
    }

    public function totJumlahStokBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return (int) DB::table('stok_produk')
            ->where('sesi_user', $sesi_user)
            ->sum('jumlah_stok_produk');
    }

    public function totHargaStokBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return (float) DB::table('stok_produk')
            ->where('sesi_user', $sesi_user)
            ->selectRaw('SUM(jumlah_stok_produk * harga_produk) AS total_harga')
            ->value('total_harga') ?? 0;
    }

    public function totPenjualanBySesi()
    {
        $sesi_user = $this->getSesiUser();
        return (int) DB::table('pembelian')
            ->where('sesi_user', $sesi_user)
            ->where('status_beli', 'Berhasil')
            ->sum('jumlah_produk');
    }

    public function totNilaiPenjualanBySesi()
    {
        $sesi_user = $this->getSesiUser();

        $result = DB::table('pembelian')
            ->leftJoin('keuntungan_toko', 'keuntungan_toko.sesi_user', '=', 'pembelian.sesi_user')
            ->leftJoin('biaya_platform', 'biaya_platform.id_biaya', '=', 'keuntungan_toko.id_biaya')
            ->select(
                DB::raw('SUM(pembelian.total_harga - (pembelian.total_harga * biaya_platform.persentase / 100)) as total_harga_new')
            )
            ->where('pembelian.sesi_user', $sesi_user)
            ->where('pembelian.status_beli', 'Berhasil')
            ->first();

        return (float) ($result->total_harga_new ?? 0);
    }
    
    public function totPembayaranSelesai()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembayaran')
            ->where('sesi_user', $sesi_user)
            ->where('status_bayar', 'Dibayar')
            ->get()
            ->toArray();
    }

    public function totPembayaranBatal()
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembayaran')
            ->where('sesi_user', $sesi_user)
            ->where('status_bayar', 'Dibatalkan')
            ->get()
            ->toArray();
    }

    // Grafik penjualan per bulan
    public function getPenjualanPerBulan($tahun, $bulan = null)
    {
        $sesi_user = $this->getSesiUser();

        $query = DB::table('pembelian')
            ->selectRaw('MONTH(waktu_pembelian) as bulan, nama_produk, SUM(jumlah_produk) as total')
            ->whereYear('waktu_pembelian', $tahun)
            ->where('sesi_user', $sesi_user)
            ->where('status_beli', 'Berhasil');

        if ($bulan) {
            $query->whereMonth('waktu_pembelian', $bulan);
        }

        return $query->groupBy('bulan', 'nama_produk')
            ->orderBy('bulan')
            ->get()
            ->toArray();
    }

    public function getNilaiPenjualanPerBulan($tahun, $bulan = null)
    {
        $sesi_user = $this->getSesiUser();

        $query = DB::table('pembelian')
            ->selectRaw('MONTH(waktu_pembelian) as bulan, nama_produk, SUM(total_harga) as total_nilai')
            ->whereYear('waktu_pembelian', $tahun)
            ->where('sesi_user', $sesi_user)
            ->where('status_beli', 'Berhasil');

        if ($bulan) {
            $query->whereMonth('waktu_pembelian', $bulan);
        }

        return $query->groupBy('bulan', 'nama_produk')
            ->orderBy('bulan')
            ->get()
            ->toArray();
    }

    // Produk Terlaris
    public function getProdukTerlaris($tahun)
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembelian')
            ->join('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->whereYear('pembelian.waktu_pembelian', $tahun)
            ->where('pembelian.sesi_user', $sesi_user)
            ->where('pembelian.status_beli', 'Berhasil')
            ->groupBy('produk.nama_produk')
            ->selectRaw('produk.nama_produk, SUM(pembelian.jumlah_produk) as total')
            ->orderByDesc('total')
            ->limit(1)
            ->first();
    }

    // Produk paling sedikit terjual
    public function getProdukTersedikit($tahun)
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembelian')
            ->join('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->whereYear('pembelian.waktu_pembelian', $tahun)
            ->where('pembelian.sesi_user', $sesi_user)
            ->where('pembelian.status_beli', 'Berhasil')
            ->groupBy('produk.nama_produk')
            ->selectRaw('produk.nama_produk, SUM(pembelian.jumlah_produk) as total')
            ->orderBy('total')
            ->limit(2)
            ->first();
    }

    // Produk nilai tertinggi
    public function getProdukNilaiTertinggi($tahun)
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembelian')
            ->join('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->whereYear('pembelian.waktu_pembelian', $tahun)
            ->where('pembelian.sesi_user', $sesi_user)
            ->where('pembelian.status_beli', 'Berhasil')
            ->groupBy('produk.nama_produk')
            ->selectRaw('produk.nama_produk, SUM(pembelian.total_harga) as total')
            ->orderByDesc('total')
            ->limit(1)
            ->first();
    }

    // Produk nilai terendah
    public function getProdukNilaiTerendah($tahun)
    {
        $sesi_user = $this->getSesiUser();
        return DB::table('pembelian')
            ->join('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->whereYear('pembelian.waktu_pembelian', $tahun)
            ->where('pembelian.sesi_user', $sesi_user)
            ->where('pembelian.status_beli', 'Berhasil')
            ->groupBy('produk.nama_produk')
            ->selectRaw('produk.nama_produk, SUM(pembelian.total_harga) as total')
            ->orderBy('total')
            ->limit(2)
            ->first();
    }
}