<?php

// app/Http/Controllers/Home_toko.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_Home_toko;
use App\Models\M_Produk;
use App\Models\M_Pembayaran;
use App\Models\M_FaqToko;
use App\Models\M_SyaketToko;
use App\Models\M_TemplateFaq;
use App\Models\M_TemplateSyaket;
use Illuminate\Support\Facades\DB;

class Home_toko extends WebsiteController
{
    protected $M_Home_toko;
    protected $M_Produk;
    protected $M_Pembayaran;

    public function __construct()
    {
        // ✅ WAJIB: Panggil dulu constructor parent
        parent::__construct();
        
        $this->M_Home_toko = new M_Home_toko();
        $this->M_Produk = new M_Produk();
        $this->M_Pembayaran = new M_Pembayaran();
    }

    protected function lampirkanRatingUlasan(array &$produkData)
    {
        $map = \App\Models\M_Ulasan::ringkasanPerProduk();

        foreach ($produkData as &$produk) {
            $produk['rata_rating'] = $map[$produk['nama_produk']]['rata'] ?? 0;
            $produk['jumlah_ulasan'] = $map[$produk['nama_produk']]['total'] ?? 0;
        }
        unset($produk);
    }

    public function index(Request $request)
    {
        $session = session();
        $keyword = $request->input('keyword');
        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Proses ukuran produk
        foreach ($produkData as &$produk) {
            if (strpos($produk['ukuran_produk'], '-') !== false) {
                $ukuran_list = explode('-', $produk['ukuran_produk']);
            } elseif (strpos($produk['ukuran_produk'], ',') !== false) {
                $ukuran_list = explode(',', $produk['ukuran_produk']);
            } else {
                $ukuran_list = [$produk['ukuran_produk']];
            }
            $produk['ukuran_list'] = array_map('trim', $ukuran_list);
        }

        $this->lampirkanRatingUlasan($produkData);

        $data = [
            'title2' => 'Katalog Produk',
            'produk_data' => $produkData,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'data_carousel' => $this->M_Home_toko->getPromo(), // Kini mengembalikan event promo
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('v_home_toko', $data);
    }

    public function view_toko($nama_toko, Request $request)
    {
        $session = session();
        $nama_toko = urldecode($nama_toko);
        $keyword = $request->input('keyword');
        $hargaMin = $request->input('harga_min');
        $hargaMax = $request->input('harga_max');
        $sortBy = $request->input('sort_by');
        $jenisProduk = $request->input('jenis_produk');

        // Ambil data produk berdasarkan nama toko dan filter lainnya
        $produkData = $this->M_Home_toko->getProduk_ByToko(
            $nama_toko,
            $keyword,
            $hargaMin,
            $hargaMax,
            $sortBy,
            $jenisProduk
        );

        // Tambahkan ukuran_list ke setiap produk
        foreach ($produkData as &$produk) {
            if (strpos($produk['ukuran_produk'], '-') !== false) {
                $produk['ukuran_list'] = array_map('trim', explode('-', $produk['ukuran_produk']));
            } elseif (strpos($produk['ukuran_produk'], ',') !== false) {
                $produk['ukuran_list'] = array_map('trim', explode(',', $produk['ukuran_produk']));
            } else {
                $produk['ukuran_list'] = [$produk['ukuran_produk']];
            }
        }

        // Ambil data website berdasarkan nama toko
        $website = $this->M_Home_toko->getWebsite($nama_toko);

        $this->lampirkanRatingUlasan($produkData);

        // Data toko + rating agregat seluruh produk di toko (dinamis per sesi_user)
        $websiteData = null;
        $ringkasanToko = null;
        if (!empty($website) && count($website) > 0) {
            $first = $website->first();
            // Hanya tampilkan jika status verifikasi Disetujui DAN status website Aktif
            if (($first->status_verifikasi ?? 'Menunggu') === 'Disetujui' && ($first->status_website ?? 'Non-aktif') === 'Aktif') {
                $websiteData = (array) $first;
                $sesiUserToko = $websiteData['sesi_user'] ?? null;
                if ($sesiUserToko) {
                    $ringkasanToko = \App\Models\M_Ulasan::getRingkasanByToko($sesiUserToko);
                }
            }
        }

        // Kirim data ke view
        $data = [
            'title2' => 'Profil Toko',
            'produk_data' => $produkData,
            'website' => $website,
            'website_data' => $websiteData,
            'ringkasan_toko' => $ringkasanToko,
            'total_produk' => count($produkData),
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            // Kirim kembali filter yang dipilih untuk ditampilkan di form
            'keyword' => $keyword,
            'harga_min' => $hargaMin,
            'harga_max' => $hargaMax,
            'sort_by' => $sortBy,
            'jenis_produk' => $jenisProduk,
        ];

        return view('pelanggan.toko.v_toko', $data);
    }
    
    // v_katalog
    public function katalog(Request $request)
    {
        $session = session();
        $keyword = $request->input('keyword', '');
        $hargaMin = $request->input('harga_min');
        $hargaMax = $request->input('harga_max');
        $jenisProduk = $request->input('jenis_produk');
        $sortHarga = $request->input('sort_harga');
        $sortNama = $request->input('sort_nama');
        $sortBy = $sortHarga ?: $sortNama;
        $ratingFilter = $request->input('rating', []); // array of star values like ['4','5']
        $minRating = $ratingFilter ? min(array_map('intval', $ratingFilter)) : null;
        $viewMode = $request->input('view', 'grid'); // 'grid' or 'list'

        // Ambil dropdown dulu
        $jenis_produk_dropdown = $this->M_Home_toko->getJenisProdukDropdown();

        // Lalu ambil produk
        $produkData = $this->M_Home_toko->getProduk(
            $keyword,
            $hargaMin,
            $hargaMax,
            $sortBy,
            $jenisProduk
        );

        foreach ($produkData as &$produk) {
            // Mendeteksi format ukuran produk
            if (strpos($produk['ukuran_produk'], '-') !== false) {
                // Format seperti "m-l-xl-xxl"
                $ukuran_list = explode('-', $produk['ukuran_produk']);
            } elseif (strpos($produk['ukuran_produk'], ',') !== false) {
                // Format seperti "Small, Medium, Large"
                $ukuran_list = explode(',', $produk['ukuran_produk']);
            } else {
                // Jika tidak ada pemisah, asumsi hanya satu ukuran
                $ukuran_list = [$produk['ukuran_produk']];
            }

            // Membersihkan spasi tambahan dari setiap elemen array
            $ukuran_list = array_map('trim', $ukuran_list);

            // Menyimpan hasil ke dalam array produk
            $produk['ukuran_list'] = $ukuran_list;
        }

        $this->lampirkanRatingUlasan($produkData);

        // Filter by rating (min rating) after rating is attached
        if ($minRating !== null) {
            $produkData = array_filter($produkData, function($produk) use ($minRating) {
                return ($produk['rata_rating'] ?? 0) >= $minRating;
            });
            $produkData = array_values($produkData); // re-index
        }

        $id_pelanggan = $session->get('id_pelanggan');

        if (!$id_pelanggan) {
            $pelangganModel = new \App\Models\M_Pelanggan();
            $pelanggan = $pelangganModel->where('id_pelanggan')->first(); // pastikan id_user benar

            if ($pelanggan) {
                $id_pelanggan = $pelanggan['id_pelanggan'];
            }
        }

        // Trigger hanya jika ada nama_pelanggan dan sesi_user
        if ($id_pelanggan) {
            $this->M_Pembayaran->batalkanTransaksiExpiredByUser($id_pelanggan);
        }
        
        // Isi data akhir
        $data = [
            'title2' => 'Katalog Produk',
            'produk_data' => $produkData,
            'data_carousel' => $this->M_Home_toko->getPromo(),
            'jenis_produk_dropdown' => $jenis_produk_dropdown,
            'filter_params' => compact('keyword', 'hargaMin', 'hargaMax', 'sortHarga', 'sortNama', 'jenisProduk', 'ratingFilter'),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'viewMode' => $viewMode,
        ];

        return view('pelanggan.toko.v_katalog', $data);
    }

    public function jenisProduk($jenis_produk = null)
    {
        $session = session();
        $keyword = request('keyword');
        $jenis_produk = urldecode($jenis_produk); // tambahkan ini

        // Ambil data produk berdasarkan jenis dan keyword
        $produkData = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        foreach ($produkData as &$produk) {
            // Mendeteksi format ukuran produk
            if (strpos($produk['ukuran_produk'], '-') !== false) {
                // Format seperti "m-l-xl-xxl"
                $ukuran_list = explode('-', $produk['ukuran_produk']);
            } elseif (strpos($produk['ukuran_produk'], ',') !== false) {
                // Format seperti "Small, Medium, Large"
                $ukuran_list = explode(',', $produk['ukuran_produk']);
            } else {
                // Jika tidak ada pemisah, asumsi hanya satu ukuran
                $ukuran_list = [$produk['ukuran_produk']];
            }

            // Membersihkan spasi tambahan dari setiap elemen array
            $ukuran_list = array_map('trim', $ukuran_list);

            // Menyimpan hasil ke dalam array produk
            $produk['ukuran_list'] = $ukuran_list;
        }

        $this->lampirkanRatingUlasan($produkData);

        $id_pelanggan = $session->get('id_pelanggan');

        if (!$id_pelanggan) {
            $pelangganModel = new \App\Models\M_Pelanggan();
            $pelanggan = $pelangganModel->where('id_pelanggan')->first(); // pastikan id_user benar

            if ($pelanggan) {
                $id_pelanggan = $pelanggan['id_pelanggan'];
            }
        }

        // Trigger hanya jika ada nama_pelanggan dan sesi_user
        if ($id_pelanggan) {
            $this->M_Pembayaran->batalkanTransaksiExpiredByUser($id_pelanggan);
        }

        $data = [
            'produk_data' => $produkData,
              'title' => $jenis_produk,
            'title2' => $jenis_produk,
            'user_logged_in' => session('user_logged_in') === true,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
        ];

        return view('pelanggan.toko.v_produk', $data);
    }

    // ambil stok via AJAX
    public function ambilStok()
    {
        $session = session();
        $produkData = $this->M_Home_toko->getProduk();

        $stokData = array_map(function ($row) {
            return [
                'id_stok' => $row['id_stok'],
                'jumlah_stok_produk' => $row['jumlah_stok_produk']
            ];
        }, $produkData);

        return response()->json($stokData);
    }

    // detail produk
    public function detail_produk(Request $request, $namaProduk)
    {
        $session = session();
        $keyword = $request->input('keyword', '');
        $namaProduk = urldecode($namaProduk);

        // Ambil data produk berdasarkan jenis dan keyword
        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Cek detail produk & status toko
        $produkDetail = $this->M_Home_toko->detailStok($namaProduk);
        if (!$produkDetail || ($produkDetail['status_verifikasi'] ?? 'Menunggu') !== 'Disetujui' || ($produkDetail['status_website'] ?? 'Non-aktif') !== 'Aktif') {
            return redirect()->route('home_toko.index')
                ->with('pesan_warning', 'Produk tidak tersedia atau lapak belum diverifikasi.');
        }

        foreach ($produkData as &$produk) {
            // Mendeteksi format ukuran produk
            if (strpos($produk['ukuran_produk'], '-') !== false) {
                // Format seperti "m-l-xl-xxl"
                $ukuran_list = explode('-', $produk['ukuran_produk']);
            } elseif (strpos($produk['ukuran_produk'], ',') !== false) {
                // Format seperti "Small, Medium, Large"
                $ukuran_list = explode(',', $produk['ukuran_produk']);
            } else {
                // Jika tidak ada pemisah, asumsi hanya satu ukuran
                $ukuran_list = [$produk['ukuran_produk']];
            }

            // Membersihkan spasi tambahan dari setiap elemen array
            $ukuran_list = array_map('trim', $ukuran_list);

            // Menyimpan hasil ke dalam array produk
            $produk['ukuran_list'] = $ukuran_list;
        }

        $id_pelanggan = $session->get('id_pelanggan');

        if (!$id_pelanggan) {
            $pelangganModel = new \App\Models\M_Pelanggan();
            $pelanggan = $pelangganModel->where('id_pelanggan')->first(); // pastikan id_user benar

            if ($pelanggan) {
                $id_pelanggan = $pelanggan['id_pelanggan'];
            }
        }

        // Trigger hanya jika ada nama_pelanggan dan sesi_user
        if ($id_pelanggan) {
            $this->M_Pembayaran->batalkanTransaksiExpiredByUser($id_pelanggan);
        }
     
        $ulasan = \App\Models\M_Ulasan::getByNamaProduk($namaProduk);
        $ringkasan = \App\Models\M_Ulasan::getRingkasanByNamaProduk($namaProduk);

        // $produkDetail & $sesiUserToko sudah diperoleh di atas (guard status toko)
        $sesiUserToko = $produkDetail['sesi_user'] ?? null;

        $data = [
            'title2' => 'Detail Produk',
            'produk_data' => $produkData,
            'produk' => $produkDetail,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'ulasan' => $ulasan,
            'ringkasan_ulasan' => $ringkasan,
            'ringkasan_toko' => $sesiUserToko ? \App\Models\M_Ulasan::getRingkasanByToko($sesiUserToko) : null,
        ];

        return view('pelanggan.toko.v_detail_produk', $data);
    }
    
    // Syarat & Ketentuan
    public function syaket(Request $request)
    {
        $session = session();
        $keyword = $request->input('keyword', '');

        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Ambil konten S&K toko aktif; jika kosong, jatuh ke template superadmin
        $sesiUserToko = $this->sesiUserTokoAktif();
        $syaketToko = M_SyaketToko::getSyaketBySesi($sesiUserToko)->filter(fn($item) => $item->status);

        if ($syaketToko->isEmpty()) {
            $syaket = M_TemplateSyaket::where('status', true)
                ->orderBy('urutan', 'ASC')->get();
        } else {
            $syaket = $syaketToko->sortBy('urutan')->values();
        }

        $data = [
            'title2' => 'Syarat & Ketentuan',
            'produk_data' => $produkData,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'syaket' => $syaket,
            'nama_toko' => $this->dataWebsite['nama_toko'],
        ];
        return view('pelanggan.toko.v_syaket', $data);
    }

    // Bantuan / FAQ
    public function bantuan(Request $request)
    {
        $session = session();
        $keyword = $request->input('keyword', '');

        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Ambil FAQ toko aktif; jika kosong, jatuh ke template superadmin
        $sesiUserToko = $this->sesiUserTokoAktif();
        $faqToko = M_FaqToko::getFaqBySesi($sesiUserToko)->filter(fn($item) => $item->status);

        if ($faqToko->isEmpty()) {
            $faqTemplate = M_TemplateFaq::where('status', true)
                ->orderBy('urutan', 'ASC')->get();
            $faqs = $faqTemplate->groupBy('kategori');
        } else {
            $faqSorted = $faqToko->sortBy('urutan')->values();
            $faqs = $faqSorted->groupBy('kategori');
        }

        $data = [
            'title2' => 'Bantuan / FAQ',
            'produk_data' => $produkData,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'faqs' => $faqs,
        ];
        return view('pelanggan.toko.v_bantuan', $data);
    }

    // Bantuan / FAQ per-toko (lapak penjual)
    public function bantuan_toko($nama_toko, Request $request)
    {
        $nama_toko = urldecode($nama_toko);
        $session = session();
        $keyword = $request->input('keyword', '');

        $website = $this->M_Home_toko->getWebsite($nama_toko)->first();
        if (!$website) {
            abort(404);
        }
        // Hanya tampilkan jika status verifikasi Disetujui DAN status website Aktif
        if (($website->status_verifikasi ?? 'Menunggu') !== 'Disetujui' || ($website->status_website ?? 'Non-aktif') !== 'Aktif') {
            abort(404);
        }
        $namaTokoData = $website->nama_toko;
        $sesiUserToko = $website->sesi_user ?? 'Superadmin';
        $dataWebsiteToko = WebsiteController::buatDataWebsite($website);

        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Ambil FAQ milik toko; jika kosong, jatuh ke template superadmin
        $faqToko = M_FaqToko::getFaqBySesi($sesiUserToko)->filter(fn($item) => $item->status);

        if ($faqToko->isEmpty()) {
            $faqTemplate = M_TemplateFaq::where('status', true)
                ->orderBy('urutan', 'ASC')->get();
            $faqs = $faqTemplate->groupBy('kategori');
        } else {
            $faqSorted = $faqToko->sortBy('urutan')->values();
            $faqs = $faqSorted->groupBy('kategori');
        }

        $data = [
            'title2' => 'Bantuan / FAQ - ' . $namaTokoData,
            'produk_data' => $produkData,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'faqs' => $faqs,
            'dataWebsite' => $dataWebsiteToko, // override global agar CTA mengikuti toko ini
            'link_bantuan' => route('home_toko.toko.bantuan', ['nama_toko' => $namaTokoData]),
            'link_syaket' => route('home_toko.toko.syaket', ['nama_toko' => $namaTokoData]),
        ];
        return view('pelanggan.toko.v_bantuan', $data);
    }

    // Syarat & Ketentuan per-toko (lapak penjual)
    public function syaket_toko($nama_toko, Request $request)
    {
        $nama_toko = urldecode($nama_toko);
        $session = session();
        $keyword = $request->input('keyword', '');

        $website = $this->M_Home_toko->getWebsite($nama_toko)->first();
        if (!$website) {
            abort(404);
        }
        // Hanya tampilkan jika status verifikasi Disetujui DAN status website Aktif
        if (($website->status_verifikasi ?? 'Menunggu') !== 'Disetujui' || ($website->status_website ?? 'Non-aktif') !== 'Aktif') {
            abort(404);
        }
        $namaTokoData = $website->nama_toko;
        $sesiUserToko = $website->sesi_user ?? 'Superadmin';
        $dataWebsiteToko = WebsiteController::buatDataWebsite($website);

        $produkData = $this->M_Home_toko->getProduk($keyword);

        // Ambil S&K milik toko; jika kosong, jatuh ke template superadmin
        $syaketToko = M_SyaketToko::getSyaketBySesi($sesiUserToko)->filter(fn($item) => $item->status);

        if ($syaketToko->isEmpty()) {
            $syaket = M_TemplateSyaket::where('status', true)
                ->orderBy('urutan', 'ASC')->get();
        } else {
            $syaket = $syaketToko->sortBy('urutan')->values();
        }

        $data = [
            'title2' => 'Syarat & Ketentuan - ' . $namaTokoData,
            'produk_data' => $produkData,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'syaket' => $syaket,
            'nama_toko' => $namaTokoData,
            'dataWebsite' => $dataWebsiteToko, // override global agar CTA mengikuti toko ini
            'link_bantuan' => route('home_toko.toko.bantuan', ['nama_toko' => $namaTokoData]),
            'link_syaket' => route('home_toko.toko.syaket', ['nama_toko' => $namaTokoData]),
        ];
        return view('pelanggan.toko.v_syaket', $data);
    }

    // Helper: sesi_user dari website toko aktif (diperlihatkan di frontend)
    protected function sesiUserTokoAktif()
    {
        return DB::table('website')
                ->where('status_website', 'Aktif')
                ->where('sesi_user', 'Superadmin')
                ->value('sesi_user') ?? 'Superadmin';
    }
}
