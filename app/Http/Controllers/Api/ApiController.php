<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\M_Home_toko;
use App\Models\M_Ulasan;
use App\Models\M_Website;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $M_Home_toko;
    protected $M_Ulasan;
    protected $M_Website;

    public function __construct()
    {
        $this->M_Home_toko = new M_Home_toko();
        $this->M_Ulasan = new M_Ulasan();
        $this->M_Website = new M_Website();
    }

    public function toko()
    {
        $websites = $this->M_Website->getWebsite_By_Level();

        $data = collect($websites)->map(function ($item) {
            return $this->formatToko($item);
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function detail_toko($nama_toko)
    {
        $website = $this->M_Website->getWebsite($nama_toko)->first();

        if (!$website) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan.'], 404);
        }

        $produk = $this->M_Home_toko->getProduk_ByToko($nama_toko);

        return response()->json([
            'success' => true,
            'data' => [
                'toko' => $this->formatToko($website),
                'produk' => array_map(fn ($p) => $this->formatProduk($p), $produk),
            ],
        ]);
    }

    public function produk(Request $request)
    {
        $produk = $this->M_Home_toko->getProduk(
            $request->query('keyword', ''),
            $request->query('harga_min'),
            $request->query('harga_max'),
            $request->query('sort_by', ''),
            $request->query('jenis', '')
        );

        return response()->json([
            'success' => true,
            'data' => array_map(fn ($p) => $this->formatProduk($p), $produk),
        ]);
    }

    public function detail_produk($nama_produk)
    {
        $detail = $this->M_Home_toko->detailStok($nama_produk);

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $ulasan = $this->M_Ulasan->getByNamaProduk($nama_produk);
        $ringkasan = $this->M_Ulasan->getRingkasanByNamaProduk($nama_produk);

        return response()->json([
            'success' => true,
            'data' => [
                'produk' => $this->formatProduk($detail),
                'ulasan' => $ulasan,
                'ringkasan_ulasan' => $ringkasan,
            ],
        ]);
    }

    protected function formatToko($item)
    {
        return [
            'id_website' => $item->id_website ?? null,
            'nama_toko' => $item->nama_toko ?? null,
            'sesi_user' => $item->sesi_user ?? null,
            'wa_pusat' => $item->wa_pusat ?? null,
            'alamat_pusat' => $item->alamat_pusat ?? null,
            'status_website' => $item->status_website ?? null,
            'logo' => !empty($item->logo_website) ? url('logo_website/' . $item->logo_website) : null,
            'background' => !empty($item->bgd_web)
                ? (str_contains($item->bgd_web, '/') ? url($item->bgd_web) : url('logo_website/' . $item->bgd_web))
                : null,
        ];
    }

    protected function formatProduk($item)
    {
        return [
            'id_stok' => $item['id_stok'] ?? null,
            'nama_produk' => $item['nama_produk'] ?? null,
            'jenis_produk' => $item['jenis_produk'] ?? null,
            'harga' => $item['harga_produk'] ?? null,
            'stok' => $item['jumlah_stok_produk'] ?? null,
            'satuan' => $item['satuan_produk'] ?? null,
            'ukuran' => $item['ukuran_produk'] ?? null,
            'berat' => $item['berat_produk'] ?? null,
            'satuan_berat' => $item['satuan_berat'] ?? null,
            'foto' => !empty($item['foto_produk']) ? url('fotoproduk/' . $item['foto_produk']) : null,
            'toko' => $item['nama_toko'] ?? null,
        ];
    }
}