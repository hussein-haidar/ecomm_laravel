<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\M_Home_toko;
use App\Models\M_OngkirApi;
use App\Models\M_Kurir;
use App\Models\M_Ekspedisi;
use App\Models\M_Website;
use App\Models\M_Bank;
use App\Models\M_Stok;
use App\Models\M_User;
use App\Models\M_Pelanggan;
use App\Models\M_Keranjang;
use App\Models\M_Pembelian;
use App\Models\M_Pembayaran;
use App\Models\M_Chat;
use App\Support\Uploads;
use App\Models\M_Kupon;
use App\Models\M_Notifikasi;
use App\Models\M_FlashSale;
use App\Models\M_Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class Pelanggan_data extends WebsiteController
{
    protected $M_Home_toko;
    protected $M_User;
    protected $M_Pelanggan;
    protected $M_Stok;
    protected $M_Kurir;
    protected $M_Ekspedisi;
    protected $Bank;
    protected $Website;
    protected $M_Keranjang;
    protected $M_Pembelian;
    protected $M_Pembayaran;

    public function __construct()
    {
        // ✅ WAJIB: Panggil dulu constructor parent
        parent::__construct();

        $this->M_Home_toko = new M_Home_toko();
        $this->M_User = new M_User();
        $this->M_Pelanggan = new M_Pelanggan();
        $this->M_Stok = new M_Stok();
        $this->M_Kurir = new M_Kurir();
        $this->M_Ekspedisi = new M_Ekspedisi();
        $this->M_Keranjang = new M_Keranjang();
        $this->M_Pembelian = new M_Pembelian();
        $this->M_Pembayaran = new M_Pembayaran();
    }

    /**
     * Harga aktif: pakai harga flash sale bila produk sedang masuk flash sale
     * yang berjalan (status Aktif, dalam jendela waktu, kuota masih tersedia).
     */
    protected function hargaAktif($id_stok, $hargaNormal)
    {
        $item = DB::table('flash_sale_item')
            ->join('flash_sale', 'flash_sale.id_flash_sale', '=', 'flash_sale_item.id_flash_sale')
            ->where('flash_sale_item.id_stok', $id_stok)
            ->where('flash_sale_item.deleted_at', 0)
            ->where('flash_sale.deleted_at', 0)
            ->where('flash_sale.status', 'Aktif')
            ->where('flash_sale.waktu_mulai', '<=', now())
            ->where('flash_sale.waktu_selesai', '>=', now())
            ->select('flash_sale_item.*')
            ->first();

        if ($item && ($item->kuota <= 0 || $item->terjual < $item->kuota) && $item->harga_flash_sale > 0) {
            return ['harga' => (float) $item->harga_flash_sale, 'flash' => true];
        }

        return ['harga' => (float) $hargaNormal, 'flash' => false];
    }

    /**
     * Tandai flash sale item sebagai terjual.
     */
    protected function tambahTerjualFlashSale($id_stok, $jumlah)
    {
        DB::table('flash_sale_item')
            ->join('flash_sale', 'flash_sale.id_flash_sale', '=', 'flash_sale_item.id_flash_sale')
            ->where('flash_sale_item.id_stok', $id_stok)
            ->where('flash_sale_item.deleted_at', 0)
            ->where('flash_sale.status', 'Aktif')
            ->where('flash_sale.waktu_mulai', '<=', now())
            ->where('flash_sale.waktu_selesai', '>=', now())
            ->update(['flash_sale_item.terjual' => DB::raw("flash_sale_item.terjual + {$jumlah}")]);
    }
    // ✅ Perbaikan method profil()
    public function profil()
    {
        $session = session();

        $keyword = request('keyword');

        // Default tampilkan semua produk atau bisa diganti misalnya "semua"
        $jenis_produk = ''; // kosong berarti tidak filter jenis

        // Ambil data produk dari model
        $produkData = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        // Ambil data profil user
        $dataProfil = $this->M_Pelanggan->where('id_pelanggan', Session::get('id_pelanggan'))->first();

        $data = [
            'produk_data' => $produkData,
            'title2' => 'Semua Produk',
            'user_logged_in' => session('user_logged_in') === true,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'dataProfil' => $dataProfil,
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.update_profile.v_profil', $data);
    }

    // ✅ Perbaikan method edit()
    public function edit($id_pelanggan)
    {
        $session = session();

        $keyword = request('keyword');

        // Default tampilkan semua produk atau bisa diganti "semua"
        $jenis_produk = ''; // kosong berarti tidak filter jenis

        // Ambil data produk dari model
        $produk_data = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        // Ambil data profil user
        $dataProfil = $this->M_Pelanggan->findOrFail($id_pelanggan);

        $data = [
            'produk_data' => $produk_data,
            'title2' => 'Edit Profil',
            'user_logged_in' => session('user_logged_in') === true,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
            'dataProfil' => $dataProfil,
        ];

        return view('pelanggan.update_profile.v_edit', $data);
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['nullable'],
            'nama_pelanggan' => ['required'],
            'no_telpon' => ['required'],
            'foto_pelanggan' => ['nullable', 'image', 'max:1024']
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $id_pelanggan = Session::get('id_pelanggan');
        $profil = M_Pelanggan::findOrFail($id_pelanggan);

        if ($request->hasFile('foto_pelanggan')) {
            Uploads::delete('fotopelanggan', $profil->foto_pelanggan ?? '');
            $file = $request->file('foto_pelanggan');
            $filename = $file->hashName();
            Uploads::store('fotopelanggan', $file, $filename);
            $profil->foto_pelanggan = $filename;
        }

        // Hanya isi field yang dikirim & jangan timpa alamat/koordinat dengan null/empty
        $data = $request->only([
            'email', 'password', 'nama_pelanggan', 'no_telpon', 'tanggal_lahir',
            'longitude', 'latitude', 'alamat', 'jenis_kelamin', 'kode_kota', 'nama_kota'
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Simpan password ter-hash
            $data['password'] = Hash::make($data['password']);
        }
        if (empty($data['alamat'])) {
            unset($data['alamat']);
        }
        if (empty($data['longitude'])) {
            unset($data['longitude']);
        }
        if (empty($data['latitude'])) {
            unset($data['latitude']);
        }
        if (empty($data['kode_kota'])) {
            unset($data['kode_kota']);
        }
        if (empty($data['nama_kota'])) {
            unset($data['nama_kota']);
        }

        $profil->fill($data);
        $profil->save();

        // ✅ Perbaikan: Gunakan put() bukan post()
        Session::put([
            'email' => $profil->email,
            'nama_pelanggan' => $profil->nama_pelanggan,
            'no_telpon' => $profil->no_telpon,
            'alamat' => $profil->alamat,
            'tanggal_lahir' => $profil->tanggal_lahir,
            'longitude' => $profil->longitude,
            'latitude' => $profil->latitude,
            'kode_kota' => $profil->kode_kota,
            'nama_kota' => $profil->nama_kota,
            'foto_pelanggan' => $profil->foto_pelanggan ?? Session::get('foto_pelanggan'),
            'password' => $profil->password ? '••••••••' : '', // indikator password ada
        ]);

        Session::flash('pesan_profil', 'Profil berhasil diperbarui.');
        return redirect()->route('pelanggan_data.profil');
    }

    public function cart(Request $request)
    {
        $session = session();
        $search = $request->input('search');

        // Ambil query builder dan paginate
        $keranjang = M_Keranjang::get_searchKeranjang()
            ->when($search, function ($query, $search) {
                return $query->where('produk.nama_produk', 'like', "%{$search}%")
                    ->orWhere('keranjang.ukuran_produk', 'like', "%{$search}%");
            })
            ->paginate(10);

        // Ambil data produk untuk mapping
        $produkData = $this->M_Home_toko->getProduk($request->input('keyword', ''));

        // Buat produk_map
        $produk_map = [];
        foreach ($produkData as $produk) {
            $ukuran_produk = $produk['ukuran_produk'] ?? '';
            if (strpos($ukuran_produk, '-') !== false) {
                $ukuran_list = array_map('trim', explode('-', $ukuran_produk));
            } elseif (strpos($ukuran_produk, ',') !== false) {
                $ukuran_list = array_map('trim', explode(',', $ukuran_produk));
            } else {
                $ukuran_list = [$ukuran_produk];
            }

            $produk_map[$produk['nama_produk']] = [
                'ukuran_list' => $ukuran_list,
                'nama_produk' => $produk['nama_produk'],
                'foto_produk' => $produk['foto_produk'],
            ];
        }

        // ✅ Tambahkan ukuran_list ke setiap item DI DALAM PAGINATOR
        foreach ($keranjang as $item) {
            $nama = $item->nama_produk;
            if (isset($produk_map[$nama])) {
                $item->ukuran_list = $produk_map[$nama]['ukuran_list'];
                $item->foto_produk = $produk_map[$nama]['foto_produk'];
            } else {
                $item->ukuran_list = [];
            }
        }

        // Total seluruh item di keranjang (bukan hanya halaman pagination)
        $semuaKeranjang = M_Keranjang::get_searchKeranjang()->get();
        $totalSemua = $semuaKeranjang->sum('total_harga');
        $jumlahItem = $semuaKeranjang->count();

        $data = [
            'title' => 'Keranjang',
            'title2' => 'Keranjang',
            'keranjang' => $keranjang, // ✅ ini adalah LengthAwarePaginator
            'produk_data' => $produkData,
            'total_semua' => $totalSemua,
            'jumlah_item' => $jumlahItem,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.data_keranjang.v_keranjang', $data);
    }

    public function add_to_cart(Request $request)
    {
        // Ambil data dari form
        $id_stok = $request->input('id_stok');
        $nama_produk = $request->input('nama_produk');
        $ukuran_produk_full = $request->input('ukuran_produk'); // Misal: "M"
        $jumlah_produk = $request->input('jumlah_produk');
        $satuan_produk = $request->input('satuan_produk');
        $harga_produk = $request->input('harga_produk');
        $berat_produk = $request->input('berat_produk');
        $satuan_berat = $request->input('satuan_berat');
        $sesi_user = $request->input('sesi_user');
        $nama_toko = $request->input('nama_toko');
        $status_keranjang = $request->input('status_keranjang') ?? 'proses';

        $total_harga = $jumlah_produk * $harga_produk;

        // Ambil nama pelanggan dari session
        $id_pelanggan = Session::get('id_pelanggan');
        $nama_pelanggan = Session::get('nama_pelanggan');

        // Cek apakah item sudah ada di keranjang dengan ukuran yang sama
        $existing_cart = M_Keranjang::where([
            ['id_pelanggan', '=', $id_pelanggan],
            ['nama_pelanggan', '=', $nama_pelanggan],
            ['id_stok', '=', $id_stok],
            ['nama_produk', '=', $nama_produk],
            ['ukuran_produk', '=', $ukuran_produk_full], // tambahkan pengecekan ukuran
            ['nama_toko', '=', $nama_toko],
            ['status_keranjang', '=', $status_keranjang]
        ])->first();

        if ($existing_cart) {
            // Update jumlah dan total harga
            $new_jumlah = $existing_cart->jumlah_produk + $jumlah_produk;
            $new_total_harga = $new_jumlah * $harga_produk;

            $existing_cart->update([
                'jumlah_produk' => $new_jumlah,
                'total_harga' => $new_total_harga,
            ]);
        } else {
            // Produk belum ada → tambahkan entri baru
            M_Keranjang::create([
                'id_pelanggan' => $id_pelanggan,
                'nama_pelanggan' => $nama_pelanggan,
                'id_stok' => $id_stok,
                'nama_produk' => $nama_produk,
                'ukuran_produk' => $ukuran_produk_full, // simpan ukuran asli
                'jumlah_produk' => $jumlah_produk,
                'satuan_produk' => $satuan_produk,
                'harga_produk' => $harga_produk,
                'berat_produk' => $berat_produk,
                'satuan_berat' => $satuan_berat,
                'total_harga' => $total_harga,
                'sesi_user' => $sesi_user,
                'nama_toko' => $nama_toko,
                'waktu_ditambahkan' => now(),
            ]);
        }

        Session::flash('pesan_cart', 'Produk berhasil ditambahkan ke keranjang!');

        return Redirect::route('pelanggan_data.cart');
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang', // perbaiki disini
            'jumlah_produk' => 'nullable|integer|min:1',
            'ukuran_produk' => 'nullable|string',
        ]);

        $id_keranjang = $request->input('id_keranjang');

        // Ambil data keranjang
        $keranjang = M_Keranjang::find($id_keranjang);
        if (!$keranjang) {
            return back()->with('error', 'Data keranjang tidak ditemukan.');
        }

        // Cek apakah ada perubahan jumlah produk
        $jumlah_baru = $request->input('jumlah_produk');
        if ($jumlah_baru && $jumlah_baru != $keranjang->jumlah_produk) {

            // Ambil stok berdasarkan id_stok
            $stok = M_Stok::find($keranjang->id_stok);
            if (!$stok) {
                return back()->with('error', 'Stok produk tidak ditemukan.');
            }

            if ($jumlah_baru > $stok->jumlah_stok_produk) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }

            // Hitung ulang total harga
            $total_harga_baru = $jumlah_baru * $keranjang->harga_produk;

            // Update jumlah produk
            $keranjang->update([
                'jumlah_produk' => $jumlah_baru,
                'total_harga' => $total_harga_baru
            ]);

            return back()->with('success', 'Jumlah produk berhasil diperbarui.');
        }

        // Cek apakah ada perubahan ukuran produk
        $ukuran_baru = $request->input('ukuran_produk');
        if ($ukuran_baru && $ukuran_baru != $keranjang->ukuran_produk) {

            // Ambil stok berdasarkan id_produk dan ukuran baru
            $stok_baru = M_Stok::where('id_stok', $keranjang->id_stok)->first();

            if (!$stok_baru) {
                return back()->with('error', 'Ukuran produk tidak tersedia.');
            }

            // Ambil jumlah produk saat ini
            $jumlah_sekarang = $keranjang->jumlah_produk;

            // Validasi stok ukuran baru
            if ($jumlah_sekarang > $stok_baru->jumlah_stok_produk) {
                return back()->with('error', 'Jumlah pesanan melebihi stok ukuran baru.');
            }

            // Hitung ulang harga
            $total_harga_baru = $jumlah_sekarang * $stok_baru->harga_produk;

            // Update ukuran dan harga
            $keranjang->update([
                'id_stok' => $stok_baru->id_stok, // jika menggunakan id_stok
                'ukuran_produk' => $ukuran_baru,
                'harga_produk' => $stok_baru->harga_produk,
                'total_harga' => $total_harga_baru
            ]);

            return back()->with('success', 'Ukuran produk berhasil diperbarui.');
        }

        // Tidak ada perubahan
        return back();
    }

    public function deleteCart($id_keranjang)
    {
        // Hapus data keranjang (pastikan metode ini bekerja)
        $this->M_Keranjang->delete_data($id_keranjang);

        // Set flash message
        Session::flash('hapus_success', 'Produk berhasil dihapus dari keranjang.');

        // Redirect ke route bernama
        return Redirect::route('pelanggan_data.cart');
    }

    public function getCart(Request $request)
    {
        $search = $request->input('search');

        // Ambil query builder dan paginate
        $keranjang = M_Keranjang::get_searchKeranjang()
            ->when($search, function ($query, $search) {
                return $query->where('produk.nama_produk', 'like', "%{$search}%")
                    ->orWhere('keranjang.ukuran_produk', 'like', "%{$search}%");
            })
            ->paginate(10);

        // Ambil data produk untuk mapping
        $produkData = $this->M_Home_toko->getProduk($request->input('keyword', ''));

        // Buat produk_map
        $produk_map = [];
        foreach ($produkData as $produk) {
            $ukuran_produk = $produk['ukuran_produk'] ?? '';
            if (strpos($ukuran_produk, '-') !== false) {
                $ukuran_list = array_map('trim', explode('-', $ukuran_produk));
            } elseif (strpos($ukuran_produk, ',') !== false) {
                $ukuran_list = array_map('trim', explode(',', $ukuran_produk));
            } else {
                $ukuran_list = [$ukuran_produk];
            }

            $produk_map[$produk['nama_produk']] = [
                'ukuran_list' => $ukuran_list,
                'nama_produk' => $produk['nama_produk'],
                'foto_produk' => $produk['foto_produk'],
            ];
        }

        // Tambahkan ukuran_list ke setiap item DI DALAM PAGINATOR
        foreach ($keranjang as $item) {
            $nama = $item->nama_produk;
            if (isset($produk_map[$nama])) {
                $item->ukuran_list = $produk_map[$nama]['ukuran_list'];
                $item->foto_produk = $produk_map[$nama]['foto_produk'];
            } else {
                $item->ukuran_list = [];
            }
        }

        // Total seluruh item di keranjang (bukan hanya halaman pagination)
        $semuaKeranjang = M_Keranjang::get_searchKeranjang()->get();
        $totalSemua = $semuaKeranjang->sum('total_harga');
        $jumlahItem = $semuaKeranjang->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $keranjang,
                'total_semua' => $totalSemua,
                'jumlah_item' => $jumlahItem,
            ]);
        }

        // If not AJAX, return view (fallback to cart view)
        $data = [
            'title' => 'Keranjang',
            'title2' => 'Keranjang',
            'keranjang' => $keranjang,
            'produk_data' => $produkData,
            'total_semua' => $totalSemua,
            'jumlah_item' => $jumlahItem,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $request->session()->get('user_logged_in', false),
        ];

        return view('pelanggan.data_keranjang.v_keranjang', $data);
    }

    public function update_cart(Request $request, $id_keranjang)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'jumlah_produk' => 'nullable|integer|min:1',
            'ukuran_produk' => 'nullable|string',
        ]);

        $isAjax = $request->ajax() || $request->wantsJson();

        $keranjang = M_Keranjang::find($id_keranjang);
        if (!$keranjang) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Data keranjang tidak ditemukan.'], 404);
            }
            Session::flash('hapus_error', 'Data keranjang tidak ditemukan.');
            return Redirect::route('pelanggan_data.cart');
        }

        $jumlah_baru = $request->input('jumlah_produk');
        if ($jumlah_baru && $jumlah_baru != $keranjang->jumlah_produk) {
            $stok = M_Stok::find($keranjang->id_stok);
            if (!$stok) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Stok produk tidak ditemukan.'], 404);
                }
                Session::flash('hapus_error', 'Stok produk tidak ditemukan.');
                return Redirect::route('pelanggan_data.cart');
            }

            if ($jumlah_baru > $stok->jumlah_stok_produk) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Jumlah melebihi stok yang tersedia.'], 400);
                }
                Session::flash('update_error', 'Jumlah melebihi stok yang tersedia.');
                return Redirect::route('pelanggan_data.cart');
            }

            $total_harga_baru = $jumlah_baru * $keranjang->harga_produk;
            $keranjang->update([
                'jumlah_produk' => $jumlah_baru,
                'total_harga' => $total_harga_baru
            ]);

            if ($isAjax) {
                return response()->json(['success' => true, 'message' => 'Jumlah produk berhasil diperbarui.']);
            }
            Session::flash('update_success', 'Jumlah produk berhasil diperbarui.');
            return Redirect::route('pelanggan_data.cart');
        }

        $ukuran_baru = $request->input('ukuran_produk');
        if ($ukuran_baru && $ukuran_baru != $keranjang->ukuran_produk) {
            $stok_baru = M_Stok::where('id_stok', $keranjang->id_stok)->first();
            if (!$stok_baru) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Ukuran produk tidak tersedia.'], 404);
                }
                Session::flash('hapus_error', 'Ukuran produk tidak tersedia.');
                return Redirect::route('pelanggan_data.cart');
            }

            $jumlah_sekarang = $keranjang->jumlah_produk;
            if ($jumlah_sekarang > $stok_baru->jumlah_stok_produk) {
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => 'Jumlah pesanan melebihi stok ukuran baru.'], 400);
                }
                Session::flash('update_error', 'Jumlah pesanan melebihi stok ukuran baru.');
                return Redirect::route('pelanggan_data.cart');
            }

            $total_harga_baru = $jumlah_sekarang * $stok_baru->harga_produk;
            $keranjang->update([
                'id_stok' => $stok_baru->id_stok,
                'ukuran_produk' => $ukuran_baru,
                'harga_produk' => $stok_baru->harga_produk,
                'total_harga' => $total_harga_baru
            ]);

            if ($isAjax) {
                return response()->json(['success' => true, 'message' => 'Ukuran produk berhasil diperbarui.']);
            }
            Session::flash('update_success', 'Ukuran produk berhasil diperbarui.');
            return Redirect::route('pelanggan_data.cart');
        }

        if ($isAjax) {
            return response()->json(['success' => false, 'message' => 'Tidak ada perubahan.']);
        }
        return Redirect::route('pelanggan_data.cart');
    }

    public function remove_from_cart($id_keranjang)
    {
        $this->M_Keranjang->delete_data($id_keranjang);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus dari keranjang.']);
        }

        Session::flash('hapus_success', 'Produk berhasil dihapus dari keranjang.');
        return Redirect::route('pelanggan_data.cart');
    }

    public function checkout(Request $request)
    {
        $session = session();
        $idPelanggan = $session->get('id_pelanggan');
        $dataPelanggan = M_Pelanggan::where('id_pelanggan', $idPelanggan)->first();

        if (!$dataPelanggan) {
            Session::flash('error', 'Data pelanggan tidak ditemukan. Silakan login kembali.');
            return redirect()->route('pelanggan_data.cart');
        }

        $selectedProducts = $request->input('selected_products', []);

        if (empty($selectedProducts)) {
            Session::flash('error', 'Silakan pilih produk untuk checkout terlebih dahulu.');
            return redirect()->route('pelanggan_data.cart');
        }

        $keranjangTerpilih = M_Keranjang::whereIn('id_keranjang', $selectedProducts)->get()->toArray();

        if (empty($keranjangTerpilih)) {
            Session::flash('error', 'Tidak ada item yang valid dalam keranjang.');
            return redirect()->route('pelanggan_data.cart');
        }

        foreach ($keranjangTerpilih as &$item) {
            $produk = DB::table('produk')
                ->select('foto_produk')
                ->where('nama_produk', $item['nama_produk'])
                ->first();

            $item['foto_produk'] = $produk ? $produk->foto_produk : null;

            $hargaAktif = $this->hargaAktif($item['id_stok'], $item['harga_produk']);
            $item['harga_produk'] = $hargaAktif['harga'];
            $item['harga_flash'] = $hargaAktif['flash'];

            $ukuranList = DB::table('stok_produk')
                ->where('nama_produk', $item['nama_produk'])
                ->pluck('ukuran_produk')
                ->toArray();

            $item['ukuran_list'] = $ukuranList;
        }
        unset($item);

        $totalHarga = array_sum(array_map(function ($item) {
            return $item['harga_produk'] * $item['jumlah_produk'];
        }, $keranjangTerpilih));

        // Ambil lapak/toko asal dari item keranjang (tiap item membawa sesi_user & nama_toko)
        $namaTokoLapak = $keranjangTerpilih[0]['nama_toko'] ?? '';
        $websiteLapak = null;
        if (!empty($namaTokoLapak)) {
            $websiteLapak = DB::table('website')
                ->where('nama_toko', $namaTokoLapak)
                ->orderByDesc('id_website')
                ->first();
        }

        // Bila item tidak membawa lapak, fallback ke website pertama
        $dataToko = $websiteLapak ?: M_Website::first();
        $latitudeToko = $dataToko->latitude_pusat ?? -6.9175;
        $longitudeToko = $dataToko->longitude_pusat ?? 107.6191;

        $kodeKotaToko = $dataToko->kode_kota ?? '';
        $namaKotaToko = $dataToko->nama_kota ?? '';

        if (empty($kodeKotaToko) && $dataToko && !empty($dataToko->alamat_pusat)) {
            try {
                $ongkirModel = new M_OngkirApi();
                $hasilCari = $ongkirModel->searchDestination(
                    $dataToko->alamat_pusat,
                    (float) $latitudeToko,
                    (float) $longitudeToko
                );
                if (!isset($hasilCari['error']) && !empty($hasilCari)) {
                    $kodeKotaToko = $hasilCari[0]['id'] ?? '';
                    $namaKotaToko = $hasilCari[0]['subdistrict_name'] ?? ($hasilCari[0]['city_name'] ?? '');
                }
            } catch (\Throwable $e) {
            }
        }

        $kodeKotaPelanggan = Session::get('kode_kota') ?? ($dataPelanggan->kode_kota ?? '');
        $namaKotaPelanggan = Session::get('nama_kota') ?? ($dataPelanggan->nama_kota ?? '');
        $alamatPelanggan = Session::get('alamat') ?? ($dataPelanggan->alamat ?? '');
        $latitudePelanggan = Session::get('latitude') ?? ($dataPelanggan->latitude ?? '');
        $longitudePelanggan = Session::get('longitude') ?? ($dataPelanggan->longitude ?? '');

        $data = [
            'title' => 'Checkout',
            'title2' => 'Checkout',
            'pelanggan' => $dataPelanggan,
            'bank' => M_Bank::all(),
            'kurir' => M_Kurir::all(),
            'keranjang_terpilih' => $keranjangTerpilih,
            'selected_products' => $selectedProducts,
            'latitude_pusat' => $latitudeToko,
            'longitude_pusat' => $longitudeToko,
            'nama_toko' => $dataToko->nama_toko ?? '',
            'sesi_user_toko' => $dataToko->sesi_user ?? '',
            'alamat_toko' => $dataToko->alamat_pusat ?? '',
            'kode_kota_toko' => $kodeKotaToko,
            'nama_kota_toko' => $namaKotaToko,
            'kode_kota_pelanggan' => $kodeKotaPelanggan,
            'nama_kota_pelanggan' => $namaKotaPelanggan,
            'alamat_pelanggan' => $alamatPelanggan,
            'latitude_pelanggan' => $latitudePelanggan,
            'longitude_pelanggan' => $longitudePelanggan,
            'totalSemua' => $totalHarga,
            'totalHarga' => $totalHarga,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in', false),
        ];

        return view('pelanggan.data_beli.v_beli', $data);
    }

    public function generateKodeTransaksi(Request $request)
    {
        // Ambil parameter dari query string
        $namaProduk = $request->query('nama_produk'); // atau $request->input('nama_produk')

        // Ambil inisial dari setiap kata di nama produk
        $words = explode(' ', $namaProduk);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        // Format prefix: TYYYYMMDD-INISIAL
        $date = now()->format('Ymd');
        $prefix = 'T' . $date . '-' . $initials;

        // Coba generate kode hingga ditemukan yang unik
        $found = false;
        $tries = 0;
        $maxTries = 10;

        do {
            $randomString = strtoupper(Str::random(4)); // 4 karakter acak kapital
            $kode = $prefix . '-' . $randomString;

            // Cek apakah kode sudah ada di tabel pembelian
            $exists = DB::table('pembelian')
                ->where('kode_beli', $kode)
                ->exists();

            if (!$exists) {
                $found = true;
            } else {
                $tries++;
            }
        } while (!$found && $tries < $maxTries);

        if ($found) {
            return response()->json(['kode_beli' => $kode]);
        } else {
            return response()->json(['error' => 'Gagal menghasilkan kode beli yang unik'], 500);
        }
    }

    public function search_destination(Request $request)
    {
        $ongkir = new M_OngkirApi();
        $lat = (float) $request->input('lat', 0);
        $lng = (float) $request->input('lng', 0);
        $keyword = trim($request->input('keyword', ''));

        if ($keyword === '') {
            return response()->json(['error' => 'Keyword pencarian kosong.']);
        }

        $result = $ongkir->searchDestination($keyword, $lat, $lng);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']]);
        }

        return response()->json(['data' => $result]);
    }

    public function refine_alamat(Request $request)
    {
        $ongkir = new M_OngkirApi();
        $result = $ongkir->refineAddress(
            (string) $request->input('desa', ''),
            (string) $request->input('kota', ''),
            (string) $request->input('provinsi', ''),
            (float) $request->input('lat', 0),
            (float) $request->input('lng', 0)
        );

        return response()->json($result);
    }

    public function hitung_ongkir(Request $request)
    {
        $ongkir = new M_OngkirApi();

        $originId = (int) $request->input('origin');
        $destinationId = (int) $request->input('destination');
        $weightGram = (int) $request->input('weight');

        if ($originId <= 0 || $destinationId <= 0 || $weightGram <= 0) {
            return response()->json(['error' => 'Parameter origin, destination, dan weight wajib diisi.']);
        }

        $courier = $request->input('courier', 'jne:jnt:sicepat');
        $result = $ongkir->calculateCost($originId, $destinationId, $weightGram, $courier);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']]);
        }

        return response()->json(['data' => $result]);
    }

    public function hitung_ongkir_lokal(Request $request)
    {
        $jarakKm = (float) $request->input('jarak', 0);

        if ($jarakKm <= 0) {
            return response()->json(['data' => []]);
        }

        // Sesuai lapak pembeli (dikirim dari halaman checkout)
        $sesiUserToko = $request->input('sesi_user', '');
        if (empty($sesiUserToko)) {
            $dataToko = M_Website::first();
            $sesiUserToko = $dataToko->sesi_user ?? '';
            if (!$dataToko) {
                return response()->json(['error' => 'Data toko tidak ditemukan.']);
            }
        }

        // Kurir milik lapak tersebut (kurir toko)
        $kurirList = M_Kurir::where('deleted_at', 0)->where('sesi_user', $sesiUserToko)->get();

        // Bila lapak belum punya kurir, fallback ke kurir internal default
        if ($kurirList->isEmpty()) {
            $kurirList = M_Kurir::where('deleted_at', 0)->where('jenis_kurir', 'Kurir Internal')->get();
        }

        if ($kurirList->isEmpty()) {
            $kurirList = M_Kurir::where('deleted_at', 0)->get();
        }

        if ($kurirList->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $hasil = [];
        foreach ($kurirList as $kurir) {
            $biaya = round($kurir->ongkir * $jarakKm);
            $estimasiMenit = ceil($jarakKm * 3);
            $estimasi = $estimasiMenit < 60
                ? $estimasiMenit . ' menit'
                : round($estimasiMenit / 60, 1) . ' jam';

            $hasil[] = [
                'name'    => $kurir->jenis_kurir,
                'service' => 'Lokal',
                'cost'    => $biaya,
                'etd'     => $estimasi,
            ];
        }

        return response()->json(['data' => $hasil]);
    }

    public function beli(Request $request)
    {
        $session = session();
        $idPelanggan = $session->get('id_pelanggan');
        $dataPelanggan = M_Pelanggan::where('id_pelanggan', $idPelanggan)->first();

        if (!$dataPelanggan) {
            Session::flash('error', 'Data pelanggan tidak ditemukan. Silakan login kembali.');
            return redirect()->route('pelanggan_data.cart');
        }

        $selectedProducts = $request->input('selected_products', []);

        if (empty($selectedProducts)) {
            Session::flash('error', 'Silakan pilih produk untuk checkout terlebih dahulu.');
            return redirect()->route('pelanggan_data.cart');
        }

        $keranjangTerpilih = M_Keranjang::whereIn('id_keranjang', $selectedProducts)->get()->toArray();

        if (empty($keranjangTerpilih)) {
            Session::flash('error', 'Tidak ada item yang valid dalam keranjang.');
            return redirect()->route('pelanggan_data.cart');
        }

        // ✅ Ambil detail produk: foto & ukuran (+ harga flash sale bila aktif)
        foreach ($keranjangTerpilih as &$item) {
            // Ambil foto_produk dari tabel 'produk'
            $produk = DB::table('produk')
                ->select('foto_produk')
                ->where('nama_produk', $item['nama_produk'])
                ->first();

            $item['foto_produk'] = $produk ? $produk->foto_produk : null;

            // Harga flash sale (bila sedang berlaku)
            $hargaAktif = $this->hargaAktif($item['id_stok'], $item['harga_produk']);
            $item['harga_produk'] = $hargaAktif['harga'];
            $item['harga_flash'] = $hargaAktif['flash'];

            // Ambil daftar ukuran dari tabel 'stok_produk'
            $ukuranList = DB::table('stok_produk')
                ->where('nama_produk', $item['nama_produk'])
                ->pluck('ukuran_produk') // asumsi kolom ukuran_produk ada di stok_produk
                ->toArray();

            $item['ukuran_list'] = $ukuranList;
        }
        unset($item);

        // Hitung total harga
        $totalHarga = array_sum(array_map(function ($item) {
            return $item['harga_produk'] * $item['jumlah_produk'];
        }, $keranjangTerpilih));

        // Ambil lapak/toko asal dari item keranjang (tiap item membawa sesi_user & nama_toko)
        $namaTokoLapak = $keranjangTerpilih[0]['nama_toko'] ?? '';
        $websiteLapak = null;
        if (!empty($namaTokoLapak)) {
            $websiteLapak = DB::table('website')
                ->where('nama_toko', $namaTokoLapak)
                ->orderByDesc('id_website')
                ->first();
        }

        // Bila item tidak membawa lapak, fallback ke website pertama
        $dataToko = $websiteLapak ?: M_Website::first();
        $latitudeToko = $dataToko->latitude_pusat ?? -6.9175;
        $longitudeToko = $dataToko->longitude_pusat ?? 107.6191;

        // Kota asal toko untuk hitung ongkir (auto-detect bila belum diisi)
        $kodeKotaToko = $dataToko->kode_kota ?? '';
        $namaKotaToko = $dataToko->nama_kota ?? '';

        if (empty($kodeKotaToko) && $dataToko && !empty($dataToko->alamat_pusat)) {
            try {
                $ongkirModel = new M_OngkirApi();
                $hasilCari = $ongkirModel->searchDestination(
                    $dataToko->alamat_pusat,
                    (float) $latitudeToko,
                    (float) $longitudeToko
                );
                if (!isset($hasilCari['error']) && !empty($hasilCari)) {
                    $kodeKotaToko = $hasilCari[0]['id'] ?? '';
                    $namaKotaToko = $hasilCari[0]['subdistrict_name'] ?? ($hasilCari[0]['city_name'] ?? '');
                }
            } catch (\Throwable $e) {
                // abaikan, biarkan kosong bila API gagal
            }
        }

        // Data alamat pelanggan (utamakan dari session yang sudah terisi di profil)
        $kodeKotaPelanggan = Session::get('kode_kota') ?? ($dataPelanggan->kode_kota ?? '');
        $namaKotaPelanggan = Session::get('nama_kota') ?? ($dataPelanggan->nama_kota ?? '');
        $alamatPelanggan = Session::get('alamat') ?? ($dataPelanggan->alamat ?? '');
        $latitudePelanggan = Session::get('latitude') ?? ($dataPelanggan->latitude ?? '');
        $longitudePelanggan = Session::get('longitude') ?? ($dataPelanggan->longitude ?? '');

        // Siapkan data untuk view
        $data = [
            'title' => 'Checkout',
            'title2' => 'Checkout',
            'pelanggan' => $dataPelanggan,
            'bank' => M_Bank::all(),
            'kurir' => M_Kurir::all(),
            'keranjang_terpilih' => $keranjangTerpilih,
            'selected_products' => $selectedProducts,
            'latitude_pusat' => $latitudeToko,
            'longitude_pusat' => $longitudeToko,
            'nama_toko' => $dataToko->nama_toko ?? '',
            'sesi_user_toko' => $dataToko->sesi_user ?? '',
            'alamat_toko' => $dataToko->alamat_pusat ?? '',
            'kode_kota_toko' => $kodeKotaToko,
            'nama_kota_toko' => $namaKotaToko,
            'kode_kota_pelanggan' => $kodeKotaPelanggan,
            'nama_kota_pelanggan' => $namaKotaPelanggan,
            'alamat_pelanggan' => $alamatPelanggan,
            'latitude_pelanggan' => $latitudePelanggan,
            'longitude_pelanggan' => $longitudePelanggan,
            'totalSemua' => $totalHarga,
            'totalHarga' => $totalHarga,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in', false),
        ];

        return view('pelanggan.data_beli.v_beli', $data);
    }
    
    public function saveBeli(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'alamat' => 'required|string',
            'bank_tujuan' => 'required|string',
            'no_rek' => 'required|numeric',
            'selected_products' => 'required|array|min:1',
            'selected_products.*' => 'exists:keranjang,id_keranjang',
        ]);

        $waktu_sekarang = now();
        $batas_waktu_bayar = $waktu_sekarang->copy()->addMinutes(10);

        $kode_beli = $request->input('kode_beli');
        $alamat = $request->input('alamat');
        $bank_tujuan = $request->input('bank_tujuan');
        $no_rek = $request->input('no_rek');
        $a_n = $request->input('a_n');
        $jenis_kurir = $request->input('jenis_kurir');
        $ongkir = (float) $request->input('ongkir', 0);
        $estimasi_waktu = $request->input('estimasi_waktu');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $selectedProducts = $request->input('selected_products');

        $id_pelanggan = Session::get('id_pelanggan');
        $nama_pelanggan = Session::get('nama_pelanggan');

        $keranjangItems = M_Keranjang::whereIn('id_keranjang', $selectedProducts)->get();

        if ($keranjangItems->isEmpty()) {
            Session::flash('error', 'Tidak ada produk yang valid untuk dibeli.');
            return redirect()->route('pelanggan_data.cart');
        }

        // Validasi kupon awal
        $kode_kupon = strtoupper(trim($request->input('kode_kupon', '')));
        if ($kode_kupon !== '') {
            $totalAwal = $keranjangItems->sum(fn($i) => (float) $i->harga_produk * (int) $i->jumlah_produk);
            $hasilKuponAwal = M_Kupon::validasiKupon($kode_kupon, $totalAwal);
            if (!$hasilKuponAwal['valid']) {
                Session::flash('error', 'Kupon tidak dapat digunakan: ' . $hasilKuponAwal['message']);
                return redirect()->route('pelanggan_data.cart');
            }
        }

        // Kelompokkan per toko
        $itemsByToko = $keranjangItems->groupBy('nama_toko');

        // ===== PHASE 1: VALIDASI & HITUNG TOTAL (tanpa simpan ke DB) =====
        $total_harga_all = 0;
        $total_berat_all = 0;
        $pesan_produk_all = "";
        $itemsToProcess = []; // Store processed data for phase 2
        $stokModel = new M_Stok();

        foreach ($itemsByToko as $nama_toko => $items) {
            $total_harga_toko = 0;
            $total_berat_toko = 0;
            $pesan_produk_toko = "";
            $tokoItems = [];

            foreach ($items as $item) {
                $stokData = DB::table('stok_produk')
                    ->select('stok_produk.*', 'produk.foto_produk', 'produk.nama_produk')
                    ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
                    ->where('stok_produk.id_stok', $item->id_stok)
                    ->first();

                if (!$stokData) {
                    Session::flash('error', "Stok tidak ditemukan untuk produk: {$item->nama_produk}");
                    return redirect()->route('pelanggan_data.cart');
                }

                if ($stokData->jumlah_stok_produk < $item->jumlah_produk) {
                    Session::flash('error', "Stok tidak cukup untuk: {$item->nama_produk} ({$item->ukuran_produk})");
                    return redirect()->route('pelanggan_data.cart');
                }

                $hargaAktif = $this->hargaAktif($item->id_stok, $item->harga_produk);
                $subtotal_harga = $hargaAktif['harga'] * $item->jumlah_produk;
                $subtotal_berat = $item->berat_produk * $item->jumlah_produk;

                $total_harga_toko += $subtotal_harga;
                $total_berat_toko += $subtotal_berat;

                $tokoItems[] = [
                    'item' => $item,
                    'stokData' => $stokData,
                    'hargaAktif' => $hargaAktif,
                    'subtotal_harga' => $subtotal_harga,
                    'subtotal_berat' => $subtotal_berat,
                ];

                $pesan_produk_toko .= "🛒 *Produk:* {$item->nama_produk}\n";
                $pesan_produk_toko .= "📏 *Ukuran:* {$item->ukuran_produk}\n";
                $pesan_produk_toko .= "🔢 *Jumlah:* {$item->jumlah_produk} {$item->satuan_produk}\n";
                $pesan_produk_toko .= "💰 *Subtotal:* Rp. " . number_format($subtotal_harga, 0, ',', '.') . "\n\n";
            }

            $total_harga_all += $total_harga_toko;
            $total_berat_all += $total_berat_toko;
            $pesan_produk_all .= "📦 *Dari Toko: {$nama_toko}*\n" . $pesan_produk_toko . "\n";
            $itemsToProcess[$nama_toko] = $tokoItems;
        }

        // Validasi kupon final
        $diskon = 0;
        $hasilKupon = null;
        if ($kode_kupon !== '') {
            $hasilKupon = M_Kupon::validasiKupon($kode_kupon, $total_harga_all);
            if ($hasilKupon['valid']) {
                $diskon = min((float) $hasilKupon['diskon'], $total_harga_all);
            }
        }

        $total_bayar_akhir = max(0, $total_harga_all + $ongkir - $diskon);

        // ===== PHASE 2: SIMPAN SEMUA DALAM TRANSAKSI =====
        try {
            DB::transaction(function () use (
                $itemsToProcess, $itemsByToko, $keranjangItems,
                $id_pelanggan, $nama_pelanggan, $kode_beli, $alamat,
                $bank_tujuan, $no_rek, $a_n, $jenis_kurir, $ongkir,
                $estimasi_waktu, $longitude, $latitude, $waktu_sekarang,
                $batas_waktu_bayar, $kode_kupon, $diskon, $total_harga_all,
                $total_bayar_akhir, $pesan_produk_all, $stokModel, $hasilKupon
            ) {
                // 1. Buat pembayaran dengan total yang sudah dihitung
                $pembayaran = M_Pembayaran::create([
                    'sesi_user' => $keranjangItems->first()->sesi_user,
                    'id_pelanggan' => $id_pelanggan,
                    'nama_pelanggan' => $nama_pelanggan,
                    'total_harga' => $total_harga_all,
                    'ongkir' => $ongkir,
                    'kode_kupon' => $kode_kupon !== '' ? $kode_kupon : null,
                    'diskon' => $diskon,
                    'total_bayar' => $total_bayar_akhir,
                    'bank_tujuan' => $bank_tujuan,
                    'no_rek' => $no_rek,
                    'a_n' => $a_n ?? '',
                    'batas_waktu_bayar' => $batas_waktu_bayar,
                ]);

                $id_bayar = $pembayaran->id_bayar;
                $sesi_user = $keranjangItems->first()->sesi_user;

                // 2. Proses per toko
                foreach ($itemsToProcess as $nama_toko => $tokoItems) {
                    foreach ($tokoItems as $data) {
                        $item = $data['item'];
                        $stokData = $data['stokData'];
                        $hargaAktif = $data['hargaAktif'];
                        $subtotal_harga = $data['subtotal_harga'];
                        $subtotal_berat = $data['subtotal_berat'];

                        if ($hargaAktif['flash']) {
                            $this->tambahTerjualFlashSale($item->id_stok, $item->jumlah_produk);
                        }

                        // Kurangi stok (sudah pakai lockForUpdate di model)
                        $result = $stokModel->kurangiStokFIFO($item->nama_produk, $item->jumlah_produk);
                        if (!$result['success']) {
                            throw new \Exception($result['message']);
                        }

                        // Simpan pembelian
                        $pembelian = M_Pembelian::create([
                            'kode_beli' => $kode_beli,
                            'id_bayar' => $id_bayar,
                            'sesi_user' => $item->sesi_user,
                            'nama_toko' => $nama_toko,
                            'id_pelanggan' => $id_pelanggan,
                            'nama_pelanggan' => $nama_pelanggan,
                            'id_stok' => $item->id_stok,
                            'nama_produk' => $item->nama_produk,
                            'ukuran_produk' => $item->ukuran_produk,
                            'jumlah_produk' => $item->jumlah_produk,
                            'satuan_produk' => $item->satuan_produk,
                            'satuan_berat' => $item->satuan_berat,
                            'total_harga' => $subtotal_harga,
                            'total_berat' => $subtotal_berat,
                            'waktu_pembelian' => $waktu_sekarang,
                            'status_beli' => 'Ditunda',
                        ]);

                        // Update status keranjang
                        M_Keranjang::where('id_keranjang', $item->id_keranjang)->update([
                            'status_keranjang' => 'Selesai'
                        ]);

                        // Generate kode resi
                        $inisial_toko = implode('', array_map(fn($part) => strtoupper(substr($part, 0, 1)), explode(' ', $nama_toko)));
                        $kode_resi = $inisial_toko . now()->format('YmdHis') . Str::random(6);

                        // Simpan ekspedisi
                        M_Ekspedisi::create([
                            'id_beli' => $pembelian->id_beli,
                            'id_bayar' => $id_bayar,
                            'kode_resi' => $kode_resi,
                            'nama_toko' => $nama_toko,
                            'id_pelanggan' => $id_pelanggan,
                            'nama_pelanggan' => $nama_pelanggan,
                            'sesi_user' => $sesi_user,
                            'longitude' => $longitude ?? '',
                            'latitude' => $latitude ?? '',
                            'alamat' => $alamat,
                            'jenis_kurir' => $jenis_kurir ?? '',
                            'ongkir' => $ongkir,
                            'estimasi_waktu' => $estimasi_waktu ?? '',
                            'status_kirim' => 'Pesanan dibuat',
                        ]);
                    }
                }

                // 3. Kupon dihitung terpakai setelah order sukses
                if ($diskon > 0 && !empty($hasilKupon['kupon'])) {
                    M_Kupon::gunakanKupon($hasilKupon['kupon']->id_kupon);
                }
            });

            // Buat pesan WhatsApp
            $pesan = "Assalamu'alaikum, Halo Admin 👋\n\n";
            $pesan .= "Saya atas nama *$nama_pelanggan* ingin menyampaikan rincian pembelian dengan kode transaksi *$kode_beli* sebagai berikut:\n\n";
            $pesan .= $pesan_produk_all;
            if ($diskon > 0) {
                $pesan .= "🎟️ *Kupon:* {$kode_kupon} (Diskon Rp. " . number_format($diskon, 0, ',', '.') . ")\n";
            }
            $pesan .= "💳 *Total Bayar:* Rp. " . number_format($total_bayar_akhir, 0, ',', '.') . "\n";
            $pesan .= "📌 *Status:* Belum Bayar\n";
            $pesan .= "🚚 *Kurir:* " . ($jenis_kurir ?? '-') . "\n";
            $pesan .= "📍 *Alamat:* $alamat\n";

            $wa_pusat = config('app.wa_pusat') ?? '081234567890';
            $wa_pusat = preg_replace('/[^0-9]/', '', $wa_pusat);
            $wa_pusat = ltrim($wa_pusat, '0');
            $wa_pusat = '62' . $wa_pusat;

            $pesan_encoded = rawurlencode($pesan);
            $link_wa = "https://wa.me/$wa_pusat?text=$pesan_encoded";

            Session::flash('pesan_beli', 'Pembelian berhasil! Silakan lakukan pembayaran.');
            Session::flash('link_wa', $link_wa);

            return redirect()->route('pelanggan_data.statusBayar');

        } catch (\Exception $e) {
            \Log::error('SaveBeli Error: ' . $e->getMessage());
            Session::flash('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
            return redirect()->route('pelanggan_data.cart');
        }
    }

    public function beliLangsung(Request $request, $id_stok)
    {
        $session = session();
        $idPelanggan = $session->get('id_pelanggan');
        $dataPelanggan = M_Pelanggan::where('id_pelanggan', $idPelanggan)->first();

        if (!$dataPelanggan) {
            Session::flash('error', 'Data pelanggan tidak ditemukan. Silakan login kembali.');
            return redirect()->route('login');
        }

        $result = DB::table('stok_produk')
            ->select(
                'stok_produk.*',
                'produk.nama_produk as nama_produk',
                'produk.foto_produk as foto_produk'
            )
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->where('stok_produk.id_stok', $id_stok)
            ->first();

        if (!$result) {
            Session::flash('error', 'Produk tidak ditemukan.');
            return redirect()->back();
        }

        $item = [
            'id_stok' => $result->id_stok,
            'nama_produk' => $result->nama_produk ?? '',
            'foto_produk' => $result->foto_produk ?? null,
            'ukuran_produk' => $request->filled('ukuran_produk') ? $request->input('ukuran_produk') : '',
            'jumlah_produk' => $request->filled('jumlah_produk') ? min((int)$request->input('jumlah_produk'), (int)$result->jumlah_stok_produk) : 1,
            'satuan_produk' => $result->satuan_produk ?? 'Pack',
            'harga_produk' => (float)($result->harga_produk ?? 0),
            'berat_produk' => (float)($result->berat_produk ?? 0),
            'satuan_berat' => $result->satuan_berat ?? '',
            'sesi_user' => $result->sesi_user ?? '',
            'nama_toko' => $request->input('nama_toko') ?? $result->nama_toko ?? 'Toko Tidak Diketahui',
            'jumlah_stok_produk' => (int)$result->jumlah_stok_produk,
        ];

        // Harga flash sale (bila sedang berlaku)
        $hargaAktif = $this->hargaAktif($item['id_stok'], $item['harga_produk']);
        $item['harga_normal'] = (float)($result->harga_produk ?? 0);
        $item['harga_produk'] = $hargaAktif['harga'];
        $item['harga_flash'] = $hargaAktif['flash'];

        if (empty($item['ukuran_produk'])) {
            Session::flash('error', 'Silakan pilih ukuran produk.');
            return redirect()->back();
        }

        if ($item['jumlah_produk'] < 1 || $item['jumlah_produk'] > $item['jumlah_stok_produk']) {
            Session::flash('error', 'Jumlah produk tidak valid.');
            return redirect()->back();
        }

        // Proses ukuran_list (opsional, untuk konsistensi)
        $ukuran_db = $result->ukuran_produk;
        if (strpos($ukuran_db, '-') !== false) {
            $ukuran_list = array_map('trim', explode('-', $ukuran_db));
        } elseif (strpos($ukuran_db, ',') !== false) {
            $ukuran_list = array_map('trim', explode(',', $ukuran_db));
        } else {
            $ukuran_list = [$ukuran_db];
        }
        $item['ukuran_list'] = $ukuran_list;

        session()->put('temp_purchase', $item);

        $totalSemua = $item['harga_produk'] * $item['jumlah_produk'];

        // Ambil lapak/toko asal dari item beli langsung (item membawa sesi_user & nama_toko)
        $websiteLapak = null;
        if (!empty($item['nama_toko'])) {
            $websiteLapak = DB::table('website')
                ->where('nama_toko', $item['nama_toko'])
                ->orderByDesc('id_website')
                ->first();
        }

        // Bila item tidak membawa lapak, fallback ke website pertama
        $dataToko = $websiteLapak ?: M_Website::first();
        $latitude_pusat = $dataToko->latitude_pusat ?? -6.9175;
        $longitude_pusat = $dataToko->longitude_pusat ?? 107.6191;

        $kode_kota_toko = $dataToko->kode_kota ?? '';
        $nama_kota_toko = $dataToko->nama_kota ?? '';

        if (empty($kode_kota_toko) && !empty($dataToko->alamat_pusat)) {
            try {
                $ongkirModel = new M_OngkirApi();
                $hasilCari = $ongkirModel->searchDestination(
                    $dataToko->alamat_pusat,
                    (float) $latitude_pusat,
                    (float) $longitude_pusat
                );
                if (!isset($hasilCari['error']) && !empty($hasilCari)) {
                    $kode_kota_toko = $hasilCari[0]['id'] ?? '';
                    $nama_kota_toko = $hasilCari[0]['subdistrict_name'] ?? ($hasilCari[0]['city_name'] ?? '');
                }
            } catch (\Throwable $e) {
                // abaikan
            }
        }

        $kode_kota_pelanggan = Session::get('kode_kota') ?? ($dataPelanggan->kode_kota ?? '');
        $nama_kota_pelanggan = Session::get('nama_kota') ?? ($dataPelanggan->nama_kota ?? '');
        $alamat_pelanggan = Session::get('alamat') ?? ($dataPelanggan->alamat ?? '');
        $latitude_pelanggan = Session::get('latitude') ?? ($dataPelanggan->latitude ?? '');
        $longitude_pelanggan = Session::get('longitude') ?? ($dataPelanggan->longitude ?? '');

        // ✅ KIRIM DATA UNTUK VIEW BELI LANGSUNG
        $data = [
            'title' => 'Checkout',
            'title2' => 'Checkout',
            'items' => [$item], // untuk @foreach di view
            'pelanggan' => $dataPelanggan,
            'bank' => M_Bank::all(),
            'kurir' => M_Kurir::all(),
            'latitude_pusat' => $latitude_pusat,
            'longitude_pusat' => $longitude_pusat,
            'nama_toko' => $dataToko->nama_toko ?? '',
            'sesi_user_toko' => $dataToko->sesi_user ?? '',
            'alamat_toko' => $dataToko->alamat_pusat ?? '',
            'kode_kota_toko' => $kode_kota_toko,
            'nama_kota_toko' => $nama_kota_toko,
            'kode_kota_pelanggan' => $kode_kota_pelanggan,
            'nama_kota_pelanggan' => $nama_kota_pelanggan,
            'alamat_pelanggan' => $alamat_pelanggan,
            'latitude_pelanggan' => $latitude_pelanggan,
            'longitude_pelanggan' => $longitude_pelanggan,
            'totalSemua' => $totalSemua,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in', false),
        ];

        return view('pelanggan.data_beli.v_beli_langsung', $data); // ✅ view khusus
    }

    public function saveBeliLangsung(Request $request)
    {
        // Validate input
        $request->validate([
            'alamat' => 'required',
            'bank_tujuan' => 'required',
            'no_rek' => 'required|numeric',
        ]);

        $waktu_sekarang = now();
        $batas_waktu_bayar = $waktu_sekarang->copy()->addMinutes(10); // 10 menit untuk bayar

        // Retrieve data from request
        $kode_beli = $request->input('kode_beli');
        $sesi_user = $request->input('sesi_user', '');
        $nama_toko = $request->input('nama_toko', '');
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $alamat = $request->input('alamat');
        $bank_tujuan = $request->input('bank_tujuan');
        $no_rek = $request->input('no_rek');
        $a_n = $request->input('a_n');
        $jenis_kurir = $request->input('jenis_kurir');
        $ongkir = (float) $request->input('ongkir', 0);
        $estimasi_waktu = $request->input('estimasi_waktu');
        $id_stok = $request->input('id_stok');
        $ukuran_produk = $request->input('ukuran_produk');
        $jumlah_produk = $request->input('jumlah_produk');

        // Ambil nama pelanggan dari session
        $id_pelanggan = Session::get('id_pelanggan');
        $nama_pelanggan = Session::get('nama_pelanggan');

        // Retrieve stock data
        $result = DB::table('stok_produk')
            ->select(
                'stok_produk.*',
                'produk.nama_produk as nama_produk',
                'produk.foto_produk as foto_produk'
            )
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->where('stok_produk.id_stok', $id_stok)
            ->first();

        if (!$result) {
            return response()->json(['error' => 'Data produk terkait tidak ditemukan.'], 404);
        }

        // Process ukuran_produk
        $ukuran_list = [];
        if ($result->ukuran_produk) {
            $ukuran_produk_db = $result->ukuran_produk;
            if (is_string($ukuran_produk_db)) {
                if (strpos($ukuran_produk_db, '-') !== false) {
                    $ukuran_list = explode('-', trim($ukuran_produk_db));
                } elseif (strpos($ukuran_produk_db, ',') !== false) {
                    $ukuran_list = array_map('trim', explode(',', $ukuran_produk_db));
                } else {
                    $ukuran_list = [trim($ukuran_produk_db)];
                }
            } elseif (is_array($ukuran_produk_db) || is_object($ukuran_produk_db)) {
                $ukuran_list = array_map('trim', (array)$ukuran_produk_db);
            }
        }

        // Validate ukuran_produk
        if (empty($ukuran_list) || !in_array($ukuran_produk, $ukuran_list)) {
            return response()->json(['error' => 'Ukuran produk tidak valid.'], 422);
        }

        // Validate stock
        if ($result->jumlah_stok_produk < $jumlah_produk) {
            return response()->json(['error' => 'Jumlah stok produk tidak mencukupi.'], 422);
        }

        // Calculate totals (harga flash sale bila berlaku)
        $hargaAktif = $this->hargaAktif($result->id_stok, $result->harga_produk);
        if ($hargaAktif['flash']) {
            $this->tambahTerjualFlashSale($result->id_stok, $jumlah_produk);
        }

        $subtotal_harga = $hargaAktif['harga'] * $jumlah_produk;
        $subtotal_berat = $result->berat_produk * $jumlah_produk;

        // Validasi kupon (server-side)
        $diskon = 0;
        $kode_kupon = strtoupper(trim($request->input('kode_kupon', '')));
        $hasilKupon = null;
        if ($kode_kupon !== '') {
            $hasilKupon = M_Kupon::validasiKupon($kode_kupon, $subtotal_harga);
            if (!$hasilKupon['valid']) {
                return response()->json(['error' => 'Kupon tidak dapat digunakan: ' . $hasilKupon['message']], 422);
            }
            $diskon = min((float) $hasilKupon['diskon'], $subtotal_harga);
        }

        $total_bayar = max(0, $subtotal_harga + $ongkir - $diskon);

        if (is_nan($total_bayar) || $total_bayar < 0) {
            return response()->json(['error' => 'Total bayar tidak valid.'], 422);
        }

        // Reduce stock
        $stokModel = new M_Stok();
        $stockResult = $stokModel->kurangiStokFIFO($result->nama_produk, $jumlah_produk);
        if (!$stockResult['success']) {
            return response()->json(['error' => $stockResult['message']], 422);
        }

        // ✅ 1. Simpan ke pembayaran DULU
        $pembayaran = M_Pembayaran::create([
            'sesi_user' => $sesi_user,
            'id_pelanggan' => $id_pelanggan,
            'nama_pelanggan' => $nama_pelanggan,
            'total_harga' => $subtotal_harga,
            'ongkir' => $ongkir,
            'kode_kupon' => $kode_kupon !== '' ? $kode_kupon : null,
            'diskon' => $diskon,
            'total_bayar' => $total_bayar,
            'bank_tujuan' => $bank_tujuan,
            'no_rek' => $no_rek,
            'a_n' => $a_n ?? '',
            'batas_waktu_bayar' => $batas_waktu_bayar,
        ]);

        // Kupon dihitung terpakai setelah order berhasil dibuat
        if ($diskon > 0 && !empty($hasilKupon['kupon'])) {
            M_Kupon::gunakanKupon($hasilKupon['kupon']->id_kupon);
        }

        // ✅ 2. Ambil id_bayar dari hasil create
        $id_bayar = $pembayaran->id_bayar; // Ini yang sebelumnya tidak ada!

        // ✅ 3. Simpan ke pembelian dengan id_bayar
        $pembelian = M_Pembelian::create([
            'id_bayar' => $id_bayar, // ✅ Sekarang variabel ini tersedia
            'kode_beli' => $kode_beli,
            'sesi_user' => $sesi_user,
            'nama_toko' => $nama_toko,
            'id_pelanggan' => $id_pelanggan,
            'nama_pelanggan' => $nama_pelanggan,
            'id_stok' => $id_stok,
            'nama_produk' => $result->nama_produk,
            'ukuran_produk' => $ukuran_produk,
            'jumlah_produk' => $jumlah_produk,
            'satuan_produk' => $result->satuan_produk,
            'satuan_berat' => $result->satuan_berat,
            'total_harga' => $subtotal_harga,
            'total_berat' => $subtotal_berat,
            'waktu_pembelian' => $waktu_sekarang,
            'status_beli' => 'Ditunda',
        ]);

        // ✅ Ambil nama_toko dari $result
        $nama_toko_asli = $result->nama_toko ?? $request->input('nama_toko');
        if (!$nama_toko_asli) {
            return response()->json(['error' => 'Nama toko tidak ditemukan.'], 422);
        }

        // ✅ Generate kode resi
        $inisial_toko = implode('', array_map(fn($part) => strtoupper(substr($part, 0, 1)), explode(' ', $nama_toko_asli)));
        $kode_resi = $inisial_toko . now()->format('YmdHis') . Str::random(5); // selalu unik

        // ✅ 2. Ambil id_bayar dari hasil create
        $id_beli = $pembelian->id_beli; // Ini yang sebelumnya tidak ada!
        
        // ✅ Simpan ke ekspedisi
        M_Ekspedisi::create([
            'kode_resi' => $kode_resi,
            'id_beli' => $id_beli, // ✅ Sekarang variabel ini tersedia
            'id_bayar' => $id_bayar, // ✅ Sekarang variabel ini tersedia
            'nama_toko' => $nama_toko_asli,
            'id_pelanggan' => $id_pelanggan,
            'nama_pelanggan' => $nama_pelanggan,
            'sesi_user' => $sesi_user,
            'longitude' => $longitude ?? '',
            'latitude' => $latitude ?? '',
            'alamat' => $alamat,
            'jenis_kurir' => $jenis_kurir ?? '',
            'ongkir' => $ongkir,
            'estimasi_waktu' => $estimasi_waktu ?? '',
            'status_kirim' =>'Pesanan dibuat',
        ]);

        // ✅ Buat pesan WhatsApp
        $pesan_produk = "🛒 *Produk:* {$result->nama_produk}\n";
        $pesan_produk .= "📏 *Ukuran:* {$ukuran_produk}\n";
        $pesan_produk .= "🔢 *Jumlah:* {$jumlah_produk} {$result->satuan_produk}\n";
        $pesan_produk .= "💰 *Subtotal:* Rp. " . number_format($subtotal_harga, 0, ',', '.') . "\n";
        $pesan_produk .= "🚚 *Ongkir:* Rp. " . number_format($ongkir, 0, ',', '.') . "\n";

        $pesan = "Assalamu'alaikum, Halo Admin 👋\n";
        $pesan .= "Saya atas nama *$nama_pelanggan* ingin menyampaikan rincian pembelian dengan kode transaksi *$kode_beli* sebagai berikut:\n\n";
        $pesan .= $pesan_produk;
        if ($diskon > 0) {
            $pesan .= "🎟️ *Kupon:* {$kode_kupon} (Diskon Rp. " . number_format($diskon, 0, ',', '.') . ")\n";
        }
        $pesan .= "💳 *Total Bayar:* Rp. " . number_format($total_bayar, 0, ',', '.') . "\n";
        $pesan .= "📌 *Status Pembayaran:* Sedang proses\n";
        $pesan .= "🚚 *Kurir:* " . ($jenis_kurir ?? '-') . "\n";
        $pesan .= "📍 *Alamat:* $alamat\n";

        // Format nomor WA
        $wa_pusat = config('app.wa_pusat') ?? '081234567890';
        $wa_pusat = preg_replace('/[^0-9]/', '', $wa_pusat);
        $wa_pusat = ltrim($wa_pusat, '0');
        $wa_pusat = '62' . $wa_pusat;

        $pesan_encoded = rawurlencode($pesan);
        $link_wa = "https://wa.me/$wa_pusat?text=$pesan_encoded";

        Session::flash('pesan_beli', 'Pembelian berhasil! Silakan lakukan pembayaran.');
        Session::flash('link_wa', $link_wa);

        return redirect()->route('pelanggan_data.statusBayar');
    }

    public function statusBayar(Request $request)
    {
        $session = session();
        $produkModel = new \App\Models\M_Produk();
        $pembayaranModel = new \App\Models\M_Pembayaran();

        $keyword = $request->input('keyword', '');
        $produk_data = $produkModel->getProduk($keyword);

        $pembayaran = $pembayaranModel->get_bayar_by_status();

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        
        // Folder QR (dikelola otomatis oleh App\Support\Uploads)

        foreach ($pembayaran as $key => $value) {
            $orderId = $value->id_bayar;
            $total_harga = number_format($value->total_harga, 2, '.', '');

            // Data QR Manual
            $dataQR = "🔖 Informasi Pembayaran:\n"
                . "Nama Pelanggan : " . (isset($value->nama_pelanggan) ? $value->nama_pelanggan : 'Pelanggan') . "\n"
                . "Produk         : " . (isset($value->nama_produk) ? $value->nama_produk : '-') . "\n"
                . "Jumlah Produk  : " . (isset($value->jumlah_produk) ? $value->jumlah_produk : '-') . " " . (isset($value->satuan_produk) ? $value->satuan_produk : '-') . "\n"
                . "Total Bayar    : Rp. {$total_harga}\n"
                . "Order ID       : {$orderId}\n\n"
                . "🏦 Transfer ke Rekening:\n"
                . "Bank           : " . (isset($value->bank_tujuan) ? $value->bank_tujuan : '-') . "\n"
                . "No. Rekening   : " . (isset($value->no_rek) ? $value->no_rek : '-') . "\n"
                . "Atas Nama      : " . (isset($value->a_n) ? $value->a_n : '-') . "";

            // Hapus file lama jika ada
            Uploads::delete('qr', "qr_{$orderId}.png");

            // Generate QR Manual dengan GD
            $result = Builder::create()
                ->data($dataQR)
                ->size(200)
                ->margin(10)
                ->build();

            $qrTmp = tempnam(sys_get_temp_dir(), 'qr');
            $result->saveToFile($qrTmp);
            Uploads::put('qr', "qr_{$orderId}.png", (string) @file_get_contents($qrTmp));
            @unlink($qrTmp);

            $pembayaran[$key]->qr_url = asset("qr/qr_{$orderId}.png");
            $pembayaran[$key]->snap_url = null;
            $pembayaran[$key]->qris_url = null;

            if ($value->status_bayar == 'Belum Bayar' && empty($value->midtrans_order_id)) {
                try {
                    $midtransOrderId = 'ORDER-' . $orderId . '-' . time();
                    $grossAmount = (int) str_replace(['.', ','], '', $value->total_bayar);

                    $params = [
                        'payment_type' => 'qris',
                        'transaction_details' => [
                            'order_id' => $midtransOrderId,
                            'gross_amount' => $grossAmount,
                        ],
                        'callbacks' => [
                            'finish' => config('midtrans.finish_redirect_url'),
                        ],
                    ];

                    $response = \Midtrans\CoreApi::charge($params);

                    DB::table('pembayaran')
                        ->where('id_bayar', $orderId)
                        ->update([
                            'midtrans_order_id' => $midtransOrderId,
                            'midtrans_qr_string' => $response->qr_string ?? null,
                            'midtrans_qr_url' => $response->actions[0]->url ?? null,
                            'midtrans_status' => $response->transaction_status ?? null,
                            'midtrans_response' => json_encode($response),
                        ]);

                    $pembayaran[$key]->midtrans_order_id = $midtransOrderId;
                    $pembayaran[$key]->qris_url = $response->actions[0]->url ?? null;

                } catch (\Exception $e) {
                    Log::error('Midtrans QRIS Error for order ' . $orderId . ': ' . $e->getMessage());
                    $pembayaran[$key]->midtrans_order_id = null;
                    $pembayaran[$key]->qris_url = null;
                }
            } elseif ($value->status_bayar == 'Belum Bayar' && !empty($value->midtrans_qr_url)) {
                $pembayaran[$key]->qris_url = $value->midtrans_qr_url;
            }
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

        $data = [
            'title' => 'Status Pembayaran',
            'title2' => 'Status Pembayaran',
            'produk_data' => $produk_data,
            'pembayaran' => $pembayaran,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.data_bayar.v_status_bayar', $data);
    }

    public function addBayar($id_bayar)
    {
        $session = session();
        $keyword = request('keyword');

        // Default tampilkan semua produk
        $jenis_produk = ''; // kosong berarti tidak filter jenis

        // Ambil data produk dari model
        $produkData = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        // Ambil data pembayaran utama (berdasarkan id_bayar)
        $pembayaran = M_Pembayaran::findOrFail($id_bayar);

        // Kirim semua data ke view
        $data = [
            'title2' => 'Pembayaran',
            'produk_data' => $produkData,
            'pembayaran' => $pembayaran, // ✅ tetap sebagai data pembayaran utama
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'id_bayar'         => $pembayaran->id_bayar,
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.data_bayar.v_bayar', $data);
    }

    public function saveBayar(Request $request, $id_bayar)
    {
        // ✅ Validasi input
        $request->validate([
            'foto_bayar' => 'required|image|mimes:png,jpg,jpeg,gif,ico|max:1792',
            'nama_pelanggan' => 'required|string|max:255',
        ], [
            'foto_bayar.required' => 'Foto Bayar wajib diisi!',
            'foto_bayar.image' => 'File harus berupa gambar.',
            'foto_bayar.mimes' => 'Format foto harus PNG, JPG, JPEG, GIF, atau ICO!',
            'foto_bayar.max' => 'Ukuran foto maksimal 1792 KB!',
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
        ]);

        // 🔍 Ambil pembayaran
        $pembayaran = M_Pembayaran::find($id_bayar);
        if (!$pembayaran) {
            Session::flash('error', 'Data pembayaran tidak ditemukan.');
            return back();
        }

        if ($pembayaran->status_bayar == 'Dibayar') {
            Session::flash('error', 'Pembayaran sudah diproses sebelumnya.');
            return back();
        }

        $sesi_user = $pembayaran->sesi_user;
        $nama_pelanggan = $pembayaran->nama_pelanggan;

        try {
            // 🔄 Mulai transaksi database
            DB::transaction(function () use ($request, $id_bayar, $sesi_user, $nama_pelanggan) {
                // 🔍 Ambil pembelian
                $pembelian = M_Pembelian::where('id_bayar', $id_bayar)->first();
                if (!$pembelian) {
                    throw new \Exception('Data pembelian tidak ditemukan.');
                }

                $id_beli = $pembelian->id_beli;

                // 🔍 Ambil biaya & website
                $biaya = DB::table('biaya_platform')->where('deleted_at', 0)->first();
                if (!$biaya) {
                    throw new \Exception('Biaya platform belum tersedia.');
                }

                $website = DB::table('website')->where('sesi_user', $sesi_user)->first();
                if (!$website) {
                    throw new \Exception('Data website belum tersedia.');
                }

                // 📁 Upload foto bayar
                $foto = $request->file('foto_bayar');
                $nama_file = time() . '_' . $foto->hashName();
                Uploads::store('fotobayar', $foto, $nama_file);

                // 🔄 Update pembayaran
                DB::table('pembayaran')
                    ->where('id_bayar', $id_bayar)
                    ->update([
                        'nama_pelanggan' => $request->nama_pelanggan,
                        'foto_bayar' => $nama_file,
                        'status_bayar' => 'Dibayar',
                    ]);

                // ✅ Simpan keuntungan toko
                DB::table('keuntungan_toko')->insert([
                    'sesi_user' => $sesi_user,
                    'id_bayar' => $id_bayar,
                    'id_website' => $website->id_website,
                    'id_biaya' => $biaya->id_biaya,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // ✅ Konfirmasi pembayaran (bisa jadi fungsi internal)
                M_Pembayaran::konfirmasi_pembayaran($id_bayar, $nama_pelanggan);
            });

            // ✅ Sukses: Redirect
            Session::flash('success', 'Pembayaran berhasil diproses!');
            return redirect()->route('home_toko.index');
        } catch (\Exception $e) {
            // ❌ Gagal: Batalkan semua perubahan otomatis oleh transaction
            Session::flash('error', 'Terjadi kesalahan saat memproses pembayaran. Transaksi dibatalkan: ' . $e->getMessage());
            return back();
        }
    }

    public function lihatBayar($id_bayar)
    {
        $session = session();

        $keyword = request('keyword');

        // Default tampilkan semua produk atau bisa diganti "semua"
        $jenis_produk = ''; // kosong berarti tidak filter jenis

        // Ambil data produk dari model
        $produk_data = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        $pembayaran = M_Pembayaran::detailBayar($id_bayar);

        $data = [
            'title2' => 'Pembayaran',
            'produk_data' => $produk_data,
            'pembayaran' => $pembayaran,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.data_bayar.v_lihat_bayar', $data);
    }

    public function statusKirim(Request $request)
    {
        $session = session();
        // Ambil nama pelanggan dari session
        $id_pelanggan = Session::get('id_pelanggan');
        $keyword = $request->input('keyword');
        $keyword_resi = $request->input('keyword_resi');

        $M_Ekspedisi = new \App\Models\M_Ekspedisi();
        $jenis_produk = '';

        $id_pelanggan = Session::get('id_pelanggan');

        // Pengiriman Aktif: semua status KECUALI "Sampai tujuan" dan "Pesanan diterima"
        $pengiriman_aktif = DB::table('ekspedisi')
            ->leftJoin('pembelian', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            ->leftJoin('produk', 'pembelian.nama_produk', '=', 'produk.nama_produk')
            ->select(
                'ekspedisi.*',
                'pembelian.nama_produk',
                'produk.foto_produk'
            )
            ->where('ekspedisi.id_pelanggan', $id_pelanggan)
            ->whereNotIn('ekspedisi.status_kirim', ['Pesanan dibuat','Sampai tujuan', 'Pesanan diterima'])
            ->orderByDesc('ekspedisi.id_bayar')
            ->get();

        // Pengiriman Selesai: hanya "Sampai tujuan" dan "Pesanan diterima"
        $pengiriman_selesai = DB::table('ekspedisi')
            ->leftJoin('pembelian', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            ->leftJoin('produk', 'pembelian.nama_produk', '=', 'produk.nama_produk')
            ->select(
                'ekspedisi.*',
                'pembelian.nama_produk',
                'produk.foto_produk'
            )
            ->where('ekspedisi.id_pelanggan', $id_pelanggan)
            ->whereIn('ekspedisi.status_kirim', ['Sampai tujuan','Pesanan diterima'])
            ->orderByDesc('ekspedisi.id_bayar')
            ->get();

        // Ambil data produk (tidak terpengaruh status kirim, boleh di awal)
        $produk_data = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        // 🔁 1. Lakukan pembaruan status OTOMATIS terlebih dahulu
        $ekspedisi = $M_Ekspedisi->updateStatusOtomatis($id_pelanggan);

        // 🔁 2. BARU ambil data ekspedisi SETELAH update
        $ekspedisi = $M_Ekspedisi->get_ekspedisi_by_pelanggan($keyword_resi);

        $data = [
            'title' => 'Status Pengiriman',
            'title2' => 'Status Pengiriman Produk',
            'produk_data' => $produk_data,
            'ekspedisi' => $ekspedisi, // <-- ini sekarang data TERBARU
            'pengiriman_aktif' => $pengiriman_aktif,      // ← tambahkan ini
            'pengiriman_selesai' => $pengiriman_selesai,  // ← tambahkan ini
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.ekspedisi.v_status_kirim', $data);
    }

    // Endpoint AJAX: perbarui status pengiriman otomatis (route sebelumnya mati/500)
    public function updateStatusOtomatis(Request $request)
    {
        $id_pelanggan = session('id_pelanggan');

        M_Ekspedisi::updateStatusOtomatis($id_pelanggan, true);

        return response()->json([
            'success' => true,
            'message' => 'Status pengiriman diperbarui secara otomatis.',
        ]);
    }

    public function tracking($id_beli)
    {
        $session = session();
        $pembelian = M_Ekspedisi::detailBeli($id_beli);

        if (!$pembelian) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        // Ambil data ekspedisi (opsional, untuk tampilan)
        $ekspedisi = null;
        if (!empty($pembelian->id_ekspedisi)) {
            $ekspedisi = DB::table('ekspedisi')
                ->where('id_ekspedisi', $pembelian->id_ekspedisi)
                ->first();
        }

        // Cek apakah sudah ada data lacak berdasarkan id_ekspedisi
        $lacak = DB::table('lacak_pesanan')
            ->where('id_ekspedisi', $pembelian->id_ekspedisi)
            ->first();

        // Jika belum ada, buat entri baru
        if (!$lacak && !empty($pembelian->id_ekspedisi)) {
          $insertData = [
    'id_ekspedisi' => $pembelian->id_ekspedisi,
    'waktu_dikemas' => null, // Biarkan null — akan diisi saat status benar-benar "Dikemas"
    'waktu_dikirim_toko' => null,
    'waktu_disortir' => null,
    'waktu_dikirim_gudang' => null,
    'waktu_sampai_gudang_tujuan' => null,
    'waktu_diantar_kurir' => null,
    'waktu_tiba_tujuan' => null,
    'waktu_pesanan_diterima' => null,
    'created_at' => now(),
    'updated_at' => now(),
];

            DB::table('lacak_pesanan')->insert($insertData);

            // Ambil ulang data setelah insert
            $lacak = DB::table('lacak_pesanan')
                ->where('id_ekspedisi', $pembelian->id_ekspedisi)
                ->first();
        }

        // Timeline status
        $timelineOrder = [
            'Pesanan dibuat',
            'Dibayar',
            'Dikemas',
            'Dikirim dari toko',
            'Disortir',
            'Dikirim dari gudang',
            'Sampai gudang tujuan',
            'Diantar kurir',
            'Sampai tujuan',
            'Pesanan diterima'
        ];

        $currentStatus = $pembelian->status_kirim ?? 'Pesanan dibuat';
        $currentIndex = array_search($currentStatus, $timelineOrder);

        // Bangun timeline
        $statuses = [
            'Pesanan dibuat' => [
                'label' => 'Pesanan Dibuat',
                'desc'  => 'Pesanan dibuat',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $pembelian->waktu_pembelian
                    ? \Carbon\Carbon::parse($pembelian->waktu_pembelian)->format('d-m-Y H:i')
                    : 'Pesanan belum dibuat'
            ],
            'Dibayar' => [
                'label' => 'Pembayaran Dikonfirmasi',
                'desc'  => 'Pembayaran berhasil diverifikasi',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $pembelian->waktu_pembayaran
                    ? \Carbon\Carbon::parse($pembelian->waktu_pembayaran)->format('d-m-Y H:i')
                    : 'Belum dibayar'
            ],
            'Dikemas' => [
                'label' => 'Pesanan Dikemas',
                'desc'  => 'Barang sedang dipersiapkan untuk dikirim',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $lacak && $lacak->waktu_dikemas
                    ? \Carbon\Carbon::parse($lacak->waktu_dikemas)->format('d-m-Y H:i')
                    : 'Belum dikemas'
            ],
            'Dikirim dari toko' => [
                'label' => 'Dikirim dari Toko',
                'desc'  => 'Barang dalam perjalanan ke gudang sortir',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $lacak && $lacak->waktu_dikirim_toko
                    ? \Carbon\Carbon::parse($lacak->waktu_dikirim_toko)->format('d-m-Y H:i')
                    : 'Belum dikirim dari toko'
            ],
            'Disortir' => [
                'label' => 'Pesanan Disortir di Gudang Sortir',
                'desc'  => 'Pesanan dalam proses sortir',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $lacak && $lacak->waktu_disortir
                    ? \Carbon\Carbon::parse($lacak->waktu_disortir)->format('d-m-Y H:i')
                    : 'Belum disortir'
            ],
            'Dikirim dari gudang' => [
                'label' => 'Dikirim dari Gudang Sortir',
                'desc'  => 'Barang dalam perjalanan ke gudang tujuan',
                'lat'   => -6.2088,
                'lng'   => 106.8456,
                'time'  => $lacak && $lacak->waktu_dikirim_gudang
                    ? \Carbon\Carbon::parse($lacak->waktu_dikirim_gudang)->format('d-m-Y H:i')
                    : 'Belum dikirim'
            ],
            'Sampai gudang tujuan' => [
                'label' => 'Tiba di Gudang Tujuan',
                'desc'  => 'Sedang diproses untuk pengiriman lokal',
                'lat'   => -6.9175,
                'lng'   => 107.6191,
                'time'  => $lacak && $lacak->waktu_sampai_gudang_tujuan
                    ? \Carbon\Carbon::parse($lacak->waktu_sampai_gudang_tujuan)->format('d-m-Y H:i')
                    : 'Belum sampai gudang tujuan'
            ],
            'Diantar kurir' => [
                'label' => 'Diantar Kurir',
                'desc'  => 'Kurir sedang mengantar ke alamat pemesan',
                'lat'   => -6.9000,
                'lng'   => 107.6100,
                'time'  => $lacak && $lacak->waktu_diantar_kurir
                    ? \Carbon\Carbon::parse($lacak->waktu_diantar_kurir)->format('d-m-Y H:i')
                    : 'Belum diantar kurir'
            ],
            'Sampai tujuan' => [
                'label' => 'Pesanan Sampai Tujuan',
                'desc'  => 'Pesanan telah tiba di alamat penerima',
                'lat'   => -6.8990,
                'lng'   => 107.6090,
                'time'  => $lacak && $lacak->waktu_tiba_tujuan
                    ? \Carbon\Carbon::parse($lacak->waktu_tiba_tujuan)->format('d-m-Y H:i')
                    : 'Belum sampai tujuan'
            ],
            'Pesanan diterima' => [
                'label' => 'Pesanan Diterima',
                'desc'  => 'Penerima telah menerima pesanan',
                'lat'   => -6.8990,
                'lng'   => 107.6090,
                'time'  => $lacak && $lacak->waktu_pesanan_diterima
                    ? \Carbon\Carbon::parse($lacak->waktu_pesanan_diterima)->format('d-m-Y H:i')
                    : 'Belum diterima'
            ],
        ];

        return view('pelanggan.ekspedisi.v_detail_kirim', [
            'title' => 'Status Pengiriman',
            'title2' => 'Status Pengiriman Produk',
            'order_id' => $id_beli,
            'timeline' => $statuses,
            'current_status' => $currentStatus,
            'current_index' => $currentIndex,
            'pembelian' => $pembelian,
            'ekspedisi' => $ekspedisi,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ]);
    }

    public function getTrackingStatus($id_beli)
    {
        $pembelian = M_Pembelian::detailBeli($id_beli);
        if (!$pembelian) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        // Ambil status LANGSUNG dari kolom status_kirim di database
        $currentStatus = $pembelian->status_kirim ?? 'Pesanan dibuat';

        $locations = [
            'Pesanan dibuat' => [-6.2088, 106.8456],
            'Dibayar' => [-6.2088, 106.8456],
            'Dikemas' => [-6.2088, 106.8456],
            'Dikirim dari toko' => [-6.2088, 106.8456],
            'Disortir' => [-6.2088, 106.8456],
            'Dikirim dari gudang' => [-6.2088, 106.8456],
            'Sampai gudang tujuan' => [-6.9175, 107.6191],
            'Diantar kurir' => [-6.9000, 107.6100],
            'Sampai tujuan' => [-6.8990, 107.6090],
        ];

        // Pastikan status valid
        if (!isset($locations[$currentStatus])) {
            $currentStatus = 'Pesanan dibuat';
        }

        $label = match ($currentStatus) {
            'Pesanan dibuat' => 'Pesanan Dibuat',
            'Dibayar' => 'Pembayaran Dikonfirmasi',
            'Dikemas' => 'Pesanan Dikemas',
            'Dikirim dari toko' => 'Dikirim Dari Toko',
            'Disortir' => 'Pesanan Disortir',
            'Dikirim dari gudang' => 'Dikirim Dari Gudang Sortir',
            'Sampai gudang tujuan' => 'Tiba Di Gudang Tujuan',
            'Diantar kurir' => 'Diantar Oleh Kurir',
            'Sampai tujuan' => 'Pesanan Sampai Tujuan',
            default => 'Status Tidak Dikenal'
        };

        $statuses = array_keys($locations);
        $currentIndex = array_search($currentStatus, $statuses);

        return response()->json([
            'status' => $currentStatus,
            'label' => $label,
            'index' => $currentIndex,
            'lat' => $locations[$currentStatus][0],
            'lng' => $locations[$currentStatus][1],
        ]);
    }

    public function riwayatBeli(Request $request)
    {
        $session = session();
        $id_pelanggan = Session::get('id_pelanggan');

        // === Ambil data riwayat pembelian
        $search = $request->input('search');
        $pembelian = M_Ekspedisi::get_status_beli_by_status()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('produk.nama_produk', 'like', "%{$search}%")
                        ->orWhere('ekspedisi.status_kirim', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        $produkData = $this->M_Home_toko->getProduk($request->input('keyword', ''));

        $idUlasanSelesai = DB::table('ulasan')
            ->where('id_pelanggan', $id_pelanggan)
            ->pluck('id_stok')
            ->toArray();

        $data = [
            'title' => 'Riwayat Pembelian',
            'title2' => 'Riwayat Pembelian',
            'produk_data' => $produkData,
            'pembelian' => $pembelian,
            'id_ulasan_selesai' => $idUlasanSelesai,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.riwayat_transaksi.v_riwayat_beli', $data);
    }

    public function simpanUlasan(Request $request)
    {
        $id_pelanggan = Session::get('id_pelanggan');
        $nama_pelanggan = Session::get('nama_pelanggan');

        $request->validate([
            'id_beli' => 'required|integer',
            'id_stok' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        $pembelian = DB::table('pembelian')
            ->leftJoin('ekspedisi', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            ->where('pembelian.id_beli', $request->id_beli)
            ->where('pembelian.id_pelanggan', $id_pelanggan)
            ->whereIn('ekspedisi.status_kirim', ['Sampai tujuan', 'Pesanan diterima'])
            ->first();

        if (!$pembelian || $pembelian->id_stok != $request->id_stok) {
            Session::flash('error', 'Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai sampai tujuan.');
            return redirect()->route('pelanggan_data.riwayatBeli');
        }

        $sudahAda = DB::table('ulasan')
            ->where('id_stok', $request->id_stok)
            ->where('id_pelanggan', $id_pelanggan)
            ->exists();

        if ($sudahAda) {
            Session::flash('error', 'Anda sudah memberikan ulasan untuk produk ini.');
            return redirect()->route('pelanggan_data.riwayatBeli');
        }

        DB::table('ulasan')->insert([
            'id_stok' => $request->id_stok,
            'id_pelanggan' => $id_pelanggan,
            'nama_user' => $nama_pelanggan,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
            'terverifikasi' => 0, // menunggu moderasi admin
            'tanggal_ulasan' => now(),
        ]);

        Session::flash('success', 'Terima kasih! Ulasan Anda akan tampil setelah diverifikasi admin.');
        return redirect()->route('pelanggan_data.riwayatBeli');
    }
    
    public function notaPembelian($id_beli)
    {
        $session = session();

        $keyword = request('keyword');

        // Default tampilkan semua produk atau bisa diganti "semua"
        $jenis_produk = ''; // kosong berarti tidak filter jenis

        // Ambil data produk dari model
        $produk_data = $this->M_Home_toko->getProdukByJenis($jenis_produk, $keyword);

        $pembelian = M_Pembelian::detailBeli($id_beli);

        $data = [
            'produk_data' => $produk_data,
            'title2' => 'Nota Pembelian',
            'pembelian' => $pembelian,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ];

        return view('pelanggan.riwayat_transaksi.v_nota_beli', $data);
    }

    public function unduhNotaPdf($id_beli)
    {
        $pembelian = M_Pembelian::detailBeli($id_beli);

        if (!$pembelian) {
            Session::flash('error', 'Data nota tidak ditemukan.');
            return redirect()->route('pelanggan_data.riwayatBeli');
        }

        $pdf = Pdf::loadView('pelanggan.riwayat_transaksi.v_nota_beli_pdf', [
            'pembelian' => $pembelian,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Nota-' . $pembelian->kode_beli . '.pdf');
    }

    public function chatKirim(Request $request)
    {
        $request->validate([
            'id_chat' => 'required|integer',
            'pesan' => 'required|string|max:1000',
        ]);

        $id_pelanggan = session('id_pelanggan');
        $chat = M_Chat::where('id_chat', $request->id_chat)
            ->where('id_pelanggan', $id_pelanggan)
            ->first();

        if (!$chat) {
            return response()->json(['success' => false, 'message' => 'Chat tidak ditemukan.'], 403);
        }

        M_Chat::kirimPesan($chat->id_chat, 'pelanggan', $id_pelanggan, $chat->nama_pelanggan, trim($request->pesan));

        return response()->json(['success' => true]);
    }

    public function chatPesan(Request $request, $id_chat)
    {
        $id_pelanggan = session('id_pelanggan');
        $chat = M_Chat::where('id_chat', $id_chat)
            ->where('id_pelanggan', $id_pelanggan)
            ->first();

        if (!$chat) {
            return response()->json(['success' => false, 'message' => 'Chat tidak ditemukan.'], 403);
        }

        DB::table('chat_transaksi')
            ->where('id_chat', $id_chat)
            ->update(['last_waktu' => now()]);

        $messages = M_Chat::pesanChat($id_chat)->map(function ($p) {
            return [
                'id' => $p->id_chat,
                'pengirim' => $p->pengirim,
                'nama' => $p->nama_pengirim,
                'pesan' => $p->pesan,
                'waktu' => Carbon::parse($p->created_at)->format('d M Y H:i'),
            ];
        });

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function chatDaftar(Request $request)
    {
        $id_pelanggan = session('id_pelanggan');

        if (empty($id_pelanggan)) {
            return response()->json(['success' => false, 'message' => 'Harus login terlebih dahulu.'], 401);
        }

        $belumDibaca = collect();

        $daftarChat = M_Chat::daftarChatPelanggan($id_pelanggan)
            ->map(function ($c) use ($belumDibaca) {
                return [
                    'id_chat' => $c->id_chat,
                    'nama_toko' => $c->nama_toko,
                    'nama_produk' => $c->nama_produk,
                    'last_pesan' => $c->pesan,
                    'pengirim' => $c->pengirim,
                    'nama_pengirim' => $c->nama_pengirim,
                    'last_waktu' => $c->last_waktu ? Carbon::parse($c->last_waktu)->format('d/m H:i') : '',
                    'belum_dibaca' => (int) ($belumDibaca[$c->id_chat] ?? 0),
                ];
            });

        return response()->json([
            'success' => true,
            'daftar_chat' => $daftarChat,
            'total_belum_dibaca' => $belumDibaca->sum(),
        ]);
    }

    public function chatBuka(Request $request)
    {
        $id_pelanggan = session('id_pelanggan');
        $nama_pelanggan = session('nama_pelanggan');

        if (empty($id_pelanggan)) {
            return response()->json(['success' => false, 'message' => 'Harus login terlebih dahulu.'], 401);
        }

        $namaToko = $request->input('nama_toko');
        $idStok = $request->input('id_stok');

        if (empty($namaToko)) {
            return response()->json(['success' => false, 'message' => 'Nama toko tidak diketahui.'], 422);
        }

        $website = DB::table('website')
            ->where('nama_toko', $namaToko)
            ->where('level', 'pemilik')
            ->first();

        if (!$website) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan.'], 404);
        }

        $namaProduk = null;
        if (!empty($idStok)) {
            $stok = DB::table('stok_produk')->where('id_stok', $idStok)->first();
            $namaProduk = $stok->nama_produk ?? null;
        }

        $chat = M_Chat::cariAtauBuat(
            $id_pelanggan,
            $nama_pelanggan,
            $website->id_user,
            $website->sesi_user,
            $website->nama_toko,
            $idStok ?: null,
            $namaProduk
        );

        return response()->json([
            'success' => true,
            'chat' => [
                'id_chat' => $chat->id_chat,
                'nama_toko' => $chat->nama_toko,
                'nama_produk' => $chat->nama_produk,
            ],
        ]);
    }

    public function applyKupon(Request $request)
    {
        $totalBelanja = (float) $request->input('total_belanja', 0);
        $result = M_Kupon::validasiKupon($request->input('kode_kupon'), $totalBelanja);

        if ($result['valid']) {
            return response()->json([
                'success' => true,
                'diskon' => (float) $result['diskon'],
                'total_akhir' => max(0, $totalBelanja - (float) $result['diskon']),
                'message' => 'Kupon berhasil diterapkan! Diskon Rp ' . number_format($result['diskon'], 0, ',', '.'),
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message']]);
    }

    public function flashSale()
    {
        $flashSales = M_FlashSale::getActive();
        $items = [];
        foreach ($flashSales as $fs) {
            $items[$fs->id_flash_sale] = [
                'info' => $fs,
                'items' => M_FlashSale::getItems($fs->id_flash_sale),
            ];
        }
        
        return view('pelanggan.flash_sale.v_flash_sale', [
            'title2' => 'Flash Sale',
            'flashSales' => $items,
            'user_logged_in' => session('user_logged_in') === true,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
        ]);
    }

    public function notifikasi()
    {
        $notifications = M_Notifikasi::getNotifikasi(Session::get('id_pelanggan'));
        return view('pelanggan.notifikasi.v_notifikasi', [
            'title2' => 'Notifikasi',
            'notifications' => $notifications,
            'user_logged_in' => true,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
        ]);
    }

    public function markReadNotifikasi($id)
    {
        M_Notifikasi::markAsRead($id);
        return back();
    }

    // ==================== WISHLIST ====================

    public function wishlist()
    {
        $session = session();
        $keyword = request('keyword');

        $produkData = $this->M_Home_toko->getProdukByJenis('', $keyword);

        $wishlist = M_Wishlist::getByPelanggan($session->get('id_pelanggan'));

        // Tandai produk yang punya harga flash sale aktif
        foreach ($wishlist as $item) {
            $hargaAktif = $this->hargaAktif($item->id_stok, $item->harga_produk);
            $item->harga_flash = $hargaAktif['flash'];
            $item->harga_aktif = $hargaAktif['harga'];
        }

        return view('pelanggan.wishlist.v_wishlist', [
            'title' => 'Wishlist',
            'title2' => 'Wishlist Saya',
            'produk_data' => $produkData,
            'wishlist' => $wishlist,
            'jenis_produk_dropdown' => $this->M_Home_toko->getJenisProdukDropdown(),
            'user_logged_in' => $session->get('user_logged_in') === true,
        ]);
    }

    public function addWishlist(Request $request)
    {
        $request->validate([
            'id_stok' => 'required|integer',
        ]);

        $idPelanggan = session('id_pelanggan');

        if (empty($idPelanggan)) {
            return response()->json([
                'success' => false,
                'login_required' => true,
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }

        $hasil = M_Wishlist::tambah($idPelanggan, $request->input('id_stok'));

        return response()->json([
            'success' => true,
            'status' => $hasil['status'],
            'total' => M_Wishlist::jumlahByPelanggan($idPelanggan),
            'message' => $hasil['status'] === 'added'
                ? 'Produk ditambahkan ke wishlist.'
                : 'Produk dihapus dari wishlist.',
        ]);
    }

    public function deleteWishlist($id_wishlist)
    {
        $idPelanggan = session('id_pelanggan');

        M_Wishlist::where('id_wishlist', $id_wishlist)
            ->where('id_pelanggan', $idPelanggan)
            ->delete();

        Session::flash('pesan', 'Produk dihapus dari wishlist.');
        return redirect()->route('pelanggan_data.wishlist');
    }
}

