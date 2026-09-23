<?php

namespace App\Http\Controllers;

use App\Models\M_Produk;
use App\Models\M_Satuan;
use App\Models\M_Varian;
use App\Models\M_Jenis;
use App\Models\M_User;
use App\Models\M_Pembelian;
use App\Models\M_Website;
use App\Models\M_Benefit;
use App\Models\M_Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\M_Kupon;
use App\Models\M_FlashSale;
use App\Models\M_LaporanPenjualan;
use App\Support\Uploads;
use App\Models\M_Retur;
use App\Models\M_Notifikasi;
use Illuminate\Support\Facades\Log;

class Pemilik_data extends Controller
{
    protected $M_Produk;
    protected $M_Satuan;
    protected $M_Varian;
    protected $M_Jenis;
    protected $M_User;
    protected $M_Pembelian;
    protected $M_Website;
    protected $M_Benefit;

    public function __construct()
    {
        $this->M_Produk = new M_Produk();
        $this->M_Satuan = new M_Satuan();
        $this->M_Varian = new M_Varian();
        $this->M_Jenis = new M_Jenis();
        $this->M_User = new M_User();
        $this->M_Pembelian = new M_Pembelian();
        $this->M_Website = new M_Website();
        $this->M_Benefit = new M_Benefit();
    }

    public function profil()
    {
        $profil = M_User::all();
        $data = [
            'title' => 'Profil Saya',
            'title2' => 'Profil Saya',
            'profil' => $profil,
        ];
        return view('pemilik.update_profile.profil', $data);
    }

    public function view_jual()
    {
        return view('pemilik.penjualan.v_jual', [
            'title' => 'Daftar Pembelian Produk',
            'title2' => 'Data Pembelian Produk',
            'pembelian' => $this->M_Pembelian->get_beli_by_sesi(),
        ]);
    }

    // Daftar Pembelian Produk
    public function view_totjual()
    {
        return view('pemilik.penjualan.v_tot_jual', [
            'title' => 'Daftar Penjualan Produk',
            'title2' => 'Data Penjualan Produk',
            'pembelian' => $this->M_Pembelian->getTotJual(),
        ]);
    }

    public function view_benefit()
    {
        return view('pemilik.keuntungan.v_untung', [
            'title' => 'Daftar Keuntungan Penjualan',
            'title2' => 'Data  Keuntungan Penjualan',
            'benefit' => $this->M_Benefit->getBenefit_By_User(),
        ]);
    }

    public function view_totbenefit()
    {
        return view('pemilik.keuntungan.v_tot_untung', [
            'title' => 'Daftar Keuntungan Penjualan',
            'title2' => 'Data  Keuntungan Penjualan',
            'benefit' => $this->M_Benefit->getTotBenefit_By_User(),
        ]);
    }
    
    // Produk methods  
    public function satuan()
    {
        $data = [
            'title' => 'List Satuan',
            'title2' => 'List Satuan',
            'satuan_produk' => $this->M_Satuan->getSatuan(),
        ];
        return view('pemilik.satuan.index', $data);
    }

    public function add_satuan()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Satuan',
            'title2' => 'Add Satuan',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.satuan.create', $data);
    }

    public function save_satuan(Request $request)
    {
        $request->validate([
            'satuan_produk' => 'required',
        ]);

        M_Satuan::create([
            'sesi_user' => $request->sesi_user,
            'satuan_produk' => $request->satuan_produk,
            'jenis_satuan' => $request->jenis_satuan,
        ]);

        // Pesan sukses  
        session()->flash('pesan', 'Data Satuan Produk Ini Berhasil Ditambahkan !');
        return redirect()->route('pemilik_data.satuan');
    }

    public function edit_satuan($id_satuan)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $satuan = M_Satuan::findOrFail($id_satuan);

        $data = [
            'title' => 'Edit Varian',
            'title2' => 'Edit Varian',
            'satuan_produk' => $satuan,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.satuan.edit', $data);
    }

    public function update_satuan(Request $request, $id_satuan)
    {
        $request->validate([
            'satuan_produk' => 'required',
        ]);

        // Mengambil data dari request  
        $data = $request->only([
            'sesi_user',
            'satuan_produk',
            'jenis_satuan',
        ]);

        // Update data ke database  
        M_Satuan::where('id_satuan', $id_satuan)->update($data);

        // Pesan sukses  
        session()->flash('pesan', 'Data Satuan Produk Ini Berhasil Di Ganti !');
        return redirect()->route('pemilik_data.satuan');
    }

    public function delete_satuan($id_satuan)
    {
        // Hapus data berdasarkan id_satuan
        M_Satuan::where('id_satuan', $id_satuan)->delete();
        // Set pesan sukses
        session()->flash('pesan', 'Data Satuan Produk Ini Berhasil Di Hapus !');

        // Redirect kembali ke halaman daftar satuan produk
        return redirect()->route('pemilik_data.satuan');
    }

    // Produk methods  
    public function varian()
    {
        $data = [
            'title' => 'List Varian',
            'title2' => 'List Varian',
            'varian' => $this->M_Varian->getVarian(),
        ];
        return view('pemilik.varian.index', $data);
    }

    public function add_varian()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Varian',
            'title2' => 'Add Varian',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.varian.create', $data);
    }

    public function save_varian(Request $request)
    {
        $request->validate([
            'varian_produk' => 'required',
        ]);

        M_Varian::create([
            'sesi_user' => $request->sesi_user,
            'varian_produk' => $request->varian_produk,
        ]);

        // Pesan sukses  
        session()->flash('pesan', 'Data Varian Produk Ini Berhasil Ditambahkan !');
        return redirect()->route('pemilik_data.varian');
    }

    public function edit_varian($id_varian)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $varian = M_Varian::findOrFail($id_varian);
        $data = [
            'title' => 'Edit Varian',
            'title2' => 'Edit Varian',
            'varian' => $varian,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.varian.edit', $data);
    }

    public function update_varian(Request $request, $id_varian)
    {
        $request->validate([
            'varian_produk' => 'required',
        ]);

        // Mengambil data dari request  
        $data = $request->only([
            'varian_produk',
            'sesi_user',
        ]);

        // Update data ke database  
        M_Varian::where('id_varian', $id_varian)->update($data);

        // Pesan sukses  
        session()->flash('pesan', 'Data Varian Produk Ini Berhasil Di Ganti !');
        return redirect()->route('pemilik_data.varian');
    }

    public function delete_varian($id_varian)
    {
        // Hapus data berdasarkan id_stok  
        M_Varian::destroy($id_varian);

        // Set pesan sukses  
        session()->flash('pesan', 'Data Varian Produk Ini Berhasil Di Hapus !');

        // Redirect kembali ke halaman daftar stok produk  
        return redirect()->route('pemilik_data.varian');
    }

    // Produk methods  
    public function jenis()
    {
        $data = [
            'title' => 'List Jenis',
            'title2' => 'List Jenis',
            'jenis' => $this->M_Jenis->getJenis(),
        ];
        return view('pemilik.jenis.index', $data);
    }

    public function add_jenis()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Jenis',
            'title2' => 'Add Jenis',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.jenis.create', $data);
    }

    public function save_jenis(Request $request)
    {
        $request->validate([
            'jenis_produk' => 'required',
        ]);

        M_Jenis::create([
            'sesi_user' => $request->sesi_user,
            'jenis_produk' => $request->jenis_produk,
        ]);

        // Pesan sukses  
        session()->flash('pesan', 'Data Jenis Produk Ini Berhasil Ditambahkan !');
        return redirect()->route('pemilik_data.jenis');
    }

    public function edit_jenis($id_jenis)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $jenis = M_Jenis::findOrFail($id_jenis);
        $data = [
            'title' => 'Edit Jenis',
            'title2' => 'Edit Jenis',
            'jenis' => $jenis,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pemilik.jenis.edit', $data);
    }

    public function update_jenis(Request $request, $id_jenis)
    {
        $request->validate([
            'jenis_produk' => 'required',
        ]);

        // Mengambil data dari request  
        $data = $request->only([
            'jenis_produk',
            'sesi_user',
        ]);

        // Update data ke database  
        M_Jenis::where('id_jenis', $id_jenis)->update($data);

        // Pesan sukses  
        session()->flash('pesan', 'Data Jenis Produk Ini Berhasil Di Ganti !');
        return redirect()->route('pemilik_data.jenis');
    }

    public function delete_jenis($id_jenis)
    {
        // Hapus data berdasarkan id_stok  
        M_Jenis::destroy($id_jenis);

        // Set pesan sukses  
        session()->flash('pesan', 'Data Jenis Produk Ini Berhasil Di Hapus !');

        // Redirect kembali ke halaman daftar stok produk  
        return redirect()->route('pemilik_data.jenis');
    }

    public function generateKodeProduk(Request $request)
    {
        $namaJenis = $request->input('jenis_produk');
        $namaVarian = $request->input('varian_produk');

        // Cari berdasarkan nama, bukan ID
        $jenis = M_Jenis::where('jenis_produk', $namaJenis)->first();
        $varian = M_Varian::where('varian_produk', $namaVarian)->first();

        $kodeJenis = $jenis ? strtoupper(substr($jenis->jenis_produk, 0, 2)) : '';
        $kodeVarian = $varian ? strtoupper(substr($varian->varian_produk, 0, 2)) : '';

        $prefix = $kodeJenis . '-' . $kodeVarian;

        $last = DB::table('produk')
            ->where('kode_produk', 'like', $prefix . '-%')
            ->count();

        $newNumber = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
        $kodeProduk = $prefix . '-' . $newNumber;

        return response()->json(['kode_produk' => $kodeProduk]);
    }

    public function generateNamaProduk(Request $request)
    {
        $namaJenis = $request->input('jenis_produk');
        $namaVarian = $request->input('varian_produk');

        // Cari berdasarkan nama
        $jenis = M_Jenis::where('jenis_produk', $namaJenis)->first();
        $varian = M_Varian::where('varian_produk', $namaVarian)->first();

        $namaProduk = '';
        if ($jenis && $varian) {
            $namaProduk = $jenis->jenis_produk . ' ' . $varian->varian_produk;
        }

        return response()->json(['nama_produk' => $namaProduk]);
    }

    // Produk methods  
    public function produk()
    {
        $data = [
            'title' => 'List Produk',
            'title2' => 'List Produk',
            'data_produk' => $this->M_Produk->getProduk(),
        ];
        return view('pemilik.produk.index', $data);
    }

    public function update_carousel(Request $request, $id_produk)
    {
        // Validasi input
        $request->validate([
            'checked' => 'sometimes|in:1',
        ]);

        // Ambil data produk
        $produk = M_Produk::findOrFail($id_produk);

        // Update kolom 'checked'
        $produk->carousel = $request->has('carousel') ? 1 : 0;
        $produk->save();

        // Redirect kembali ke halaman daftar produk
        return back()->with('success', 'Status carousel berhasil diperbarui.');
    }

    public function add_produk()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        // Ambil satuan berdasarkan jenis_satuan (langsung dari database)
        $data_satuan_produk = M_Satuan::where('deleted_at', 0)
            ->where('jenis_satuan', 'Produk')
            ->get();

        $data_satuan_berat = M_Satuan::where('deleted_at', 0)
            ->where('jenis_satuan', 'Berat')
            ->get();

        $data = [
            'title' => 'Add Produk',
            'title2' => 'Add Produk',
            'data_satuan_produk' => $data_satuan_produk,
            'data_satuan_berat' => $data_satuan_berat,
            'data_varian' => $this->M_Varian->getVarian(),
            'data_jenis' => $this->M_Jenis->getJenis(),
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];

        return view('pemilik.produk.create', $data);
    }

    public function save_produk(Request $request)
    {
        // Validasi form input
        $request->validate([
            'nama_produk' => 'required',
            'harga_produk' => 'required|numeric',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'size_guide_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
        ]);

        // Hitung harga baru
        $harga_produk = $request->input('harga_produk');

        // Upload foto produk utama
        $fileName = null;
        if ($request->hasFile('foto_produk') && $request->file('foto_produk')->isValid()) {
            $file = $request->file('foto_produk');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Uploads::store('fotoproduk', $file, $fileName);
        } else {
            return redirect()->back()->with('error', 'Gagal mengupload foto produk.');
        }

        // Upload gallery images
        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryFileName = uniqid() . '_gallery.' . $file->getClientOriginalExtension();
                    Uploads::store('fotoproduk', $file, $galleryFileName);
                    $galleryImages[] = $galleryFileName;
                }
            }
        }

        // Upload size guide image
        $sizeGuideImage = null;
        if ($request->hasFile('size_guide_image') && $request->file('size_guide_image')->isValid()) {
            $file = $request->file('size_guide_image');
            $sizeGuideImage = uniqid() . '_sizeguide.' . $file->getClientOriginalExtension();
            Uploads::store('fotoproduk', $file, $sizeGuideImage);
        }

        // Simpan ke database
        M_Produk::create([
            'kode_produk' => $request->input('kode_produk'),
            'sesi_user' => $request->input('sesi_user'),
            'nama_produk' => $request->input('nama_produk'),
            'ukuran_produk' => $request->input('ukuran_produk'),
            'berat_produk' => $request->input('berat_produk'),
            'satuan_berat' => $request->input('satuan_berat'),
            'harga_produk' => $harga_produk,
            'jenis_produk' => $request->input('jenis_produk'),
            'varian_produk' => $request->input('varian_produk'),
            'deskripsi_produk' => $request->input('deskripsi_produk'),
            'foto_produk' => $fileName,
            'gallery_images' => $galleryImages ? json_encode($galleryImages) : null,
            'size_guide_image' => $sizeGuideImage,
            'video_url' => $request->input('video_url'),
            'carousel' => 0,
            'deleted_at' => 0,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('pemilik_data.produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit_produk($id_produk)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        // Ambil satuan berdasarkan jenis_satuan (langsung dari database)
        $data_satuan_produk = M_Satuan::where('deleted_at', 0)
            ->where('jenis_satuan', 'Produk')
            ->get();

        $data_satuan_berat = M_Satuan::where('deleted_at', 0)
            ->where('jenis_satuan', 'Berat')
            ->get();

        // Ambil produk berdasarkan id_produk
        $produk = M_Produk::findOrFail($id_produk);
        // Kirim data ke view
        $data = [
            'title' => 'Edit Produk',
            'title2' => 'Edit Produk',
            'produk' => $produk,
            'data_satuan_produk' => $data_satuan_produk,
            'data_satuan_berat' => $data_satuan_berat,
            'data_varian' => $this->M_Varian->getVarian(),
            'data_jenis' => $this->M_Jenis->getJenis(),
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Tidak Ditemukan',
        ];

        return view('pemilik.produk.edit', $data);
    }

    public function update_produk(Request $request, $id_produk)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required',
            'harga_produk' => 'required|numeric',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'size_guide_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
        ]);

        // Cari produk berdasarkan ID
        $produk = M_Produk::findOrFail($id_produk);

        // Hitung harga baru
        $harga_produk = $request->input('harga_produk');

        // Update foto utama jika ada
        if ($request->hasFile('foto_produk')) {
            Uploads::delete('fotoproduk', $produk->foto_produk ?? '');

            $imageName = time() . '.' . $request->foto_produk->extension();
            Uploads::store('fotoproduk', $request->foto_produk, $imageName);
            $produk->foto_produk = $imageName;
        }

        // Update gallery images - tambahkan ke existing
        if ($request->hasFile('gallery_images')) {
            $existingGallery = json_decode($produk->gallery_images, true) ?? [];
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryFileName = uniqid() . '_gallery.' . $file->getClientOriginalExtension();
                    Uploads::store('fotoproduk', $file, $galleryFileName);
                    $existingGallery[] = $galleryFileName;
                }
            }
            $produk->gallery_images = json_encode($existingGallery);
        }

        // Update size guide image
        if ($request->hasFile('size_guide_image') && $request->file('size_guide_image')->isValid()) {
            Uploads::delete('fotoproduk', $produk->size_guide_image ?? '');
            $file = $request->file('size_guide_image');
            $sizeGuideImage = uniqid() . '_sizeguide.' . $file->getClientOriginalExtension();
            Uploads::store('fotoproduk', $file, $sizeGuideImage);
            $produk->size_guide_image = $sizeGuideImage;
        }

        // Update video URL
        $produk->video_url = $request->input('video_url');

        // Update data produk
        $produk->sesi_user = $request->input('sesi_user');
        $produk->kode_produk = $request->input('kode_produk');
        $produk->varian_produk = $request->input('varian_produk');
        $produk->jenis_produk = $request->input('jenis_produk');
        $produk->nama_produk = $request->input('nama_produk');
        $produk->ukuran_produk = $request->input('ukuran_produk');
        $produk->deskripsi_produk = $request->input('deskripsi_produk');
        $produk->harga_produk = $harga_produk;
        $produk->berat_produk = $request->input('berat_produk');
        $produk->satuan_berat = $request->input('satuan_berat');

        // Simpan perubahan
        $produk->save();

        return redirect()->route('pemilik_data.produk')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete_produk($id_produk)
    {
        $produk = M_Produk::findOrFail($id_produk);
        $produk->update(['deleted_at' => 1]); // ✅ Benar, set manual ke 1

        return redirect()->route('pemilik_data.produk')
            ->with('success', 'Produk berhasil dihapus sementara.');
    }

    // Fungsi delete untuk soft delete produk
    public function data_dihapus_produk()
    {
        $data = [
            'title' => 'Data Produk Dihapus',
            'title2' => 'Data Produk Dihapus',
            'data_produk_dihapus' => $this->M_Produk->get_produk_dihapus(), // Ambil data produk yang sudah dihapus
        ];
        return view('pemilik.produk.v_data_dihapus', $data);
    }

    public function restore($id_produk)
    {
        $produk = M_Produk::findOrFail($id_produk);

        // Kembalikan deleted_at ke 0
        $produk->update(['deleted_at' => 0]);

        return redirect()->route('pemilik_data.produk')
            ->with('success', 'Produk berhasil direstore.');
    }

    public function delete_hard_produk($id_produk)
    {
        $produk = M_Produk::findOrFail($id_produk);

        if ($produk->foto_produk) {
            Uploads::delete('fotoproduk', $produk->foto_produk);
        }

        $produk->forceDelete();

        return redirect()->route('pemilik_data.data_dihapus_produk')
            ->with('success', 'Produk berhasil dihapus permanen.');
    }

    // User methods  
    public function user()
    {
        $data = [
            'title' => 'List User',
            'title2' => 'List User',
            'user' => $this->M_User->getUser(),
        ];
        return view('pemilik.user.index', $data);
    }

    public function add_user()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        // Data untuk dikirim ke view
        $data = [
            'title' => 'Tambah Pengguna',
            'title2' => 'Tambah Pengguna',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];

        return view('pemilik.user.create', $data);
    }

    public function save_user(Request $request)
    {
        // Validasi input  
        $request->validate([
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'level' => 'required|max:100', // Pastikan level diterima  
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ]);

        // Handle the file upload  
        if ($request->hasFile('foto_user') && $request->file('foto_user')->isValid()) {
            // Get the uploaded file  
            $file = $request->file('foto_user');

            // Generate a random file name using Str::random() and the file's extension  
            $fileExtension = $file->getClientOriginalExtension();  // Get the original file extension  
            $fileName = Str::random(20) . '.' . $fileExtension;  // Generate random name and add the extension  

// Move the file to the 'fotouser' directory
            Uploads::store('fotouser', $file, $fileName);

            // Store the user data into the database
            M_User::create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'sesi_user' => $request->sesi_user,
                'nama_title' => $request->nama_title,
                'password' => $request->password,
                'level' => $request->level,  // Mengambil nilai level dari dropdown  
                'foto_user' => $fileName,  // Store the file name in the database  
            ]);

            // Redirect with success message  
            return redirect()->route('pemilik_data.user')->with('success', 'User berhasil ditambahkan.');
        } else {
            return redirect()->route('pemilik_data.add_user')->with('error', 'File upload failed.');
        }
    }

    public function edit_user($id_user)
    {
        // Ambil sesi user dari session Laravel
        $sesi_user = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesi_user);

        // Ambil data user berdasarkan ID untuk diedit
        $user = M_User::findOrFail($id_user);

        $data = [
            'title' => 'Edit User',
            'title2' => 'Edit Pengguna',
            'user' => $user,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];

        return view('pemilik.user.edit', $data);
    }

    public function update_user(Request $request, $id_user)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ]);

        $user = M_User::findOrFail($id_user);

        // Siapkan data dasar dari request
        $data = $request->only([
            'sesi_user',
            'username',
            'fullname',
            'nama_title',
            'password',
            'level',
        ]);

        // Handle upload foto_user jika ada
        if ($request->hasFile('foto_user')) {
            // Hapus foto lama jika ada
            Uploads::delete('fotouser', $user->foto_user ?? '');

            // Buat nama file unik, misal: 6899e0116636a.png
            $extension = $request->foto_user->getClientOriginalExtension();
            $imageName = Str::random(13) . '.' . $extension; // Contoh: 6899e0116636a.png

            // Pindahkan file ke folder fotouser
            Uploads::store('fotouser', $request->foto_user, $imageName);

            // Simpan hanya nama file ke database
            $data['foto_user'] = $imageName;
        }

        // Update data ke database
        M_User::where('id_user', $id_user)->update($data);

        // Update session meskipun tidak ada perubahan
        session()->put($data);
        if (isset($data['foto_user'])) {
            session()->put('foto_user', $data['foto_user']);
        }

        return redirect()->route('pemilik_data.user')->with('success', 'User berhasil diperbarui.');
    }

    public function delete_user($id_user)
    {
        // Temukan pengguna berdasarkan ID  
        $user = M_User::findOrFail($id_user);

        // Jika pengguna memiliki foto yang ada, hapus foto lama  
if ($user->foto_user != "") {
            // Cek apakah file foto ada
            Uploads::delete('fotouser', $user->foto_user);
        }

        // Hapus pengguna dari database  
        $user->delete();

        // Redirect kembali dengan pesan sukses  
        return redirect()->route('pemilik_data.user')->with('success', 'User deleted successfully.');
    }

    public function website()
    {
        $data = [
            'title' => 'Daftar Website Toko',
            'title2' => 'Data Website',
            'website' =>  $this->M_Website->getWebsite_By_Sesi(),
        ];
        return view('pemilik.website.v_website', $data);
    }

    // Konfirmasi Status
    public function konfirmStatusWeb_By_Sesi(Request $request)
    {
        $request->validate([
            'status_website' => 'required',
        ]);

        $status_web = M_Website::findOrFail($request->id_website);

        $dataUpdate = [
            'status_website' => $request->status_website,
        ];

        $status_web->update($dataUpdate);

        return redirect()->route('pemilik_data.website')->with('success', 'Status Web berhasil diperbarui.');
    }

    public function add_website()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');

        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Website',
            'title2' => 'Add Website',
            'id_user'         => $dataUser['id_user'] ?? 'ID Tidak Diketahui',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
            'level'         => $dataUser['level'] ?? 'Level Tidak Diketahui',
        ];
        return view('pemilik.website.v_create', $data);
    }
    
    public function save_website(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_toko' => 'required',
            'logo_website' => 'required|image|mimes:png,jpg,jpeg|max:1024',
            'bgd_web' => 'required|image|mimes:png,jpg,jpeg|max:1024',
            'id_user' => 'required',
            'sesi_user' => 'required',
            'level' => 'required',
        ]);

        $logoPath = null;
        $bgdPath = null;

        // Upload logo_website
        if ($request->hasFile('logo_website') && $request->file('logo_website')->isValid()) {
            // Simpan gambar baru  
            $imageName = time() . '_' . Str::random(10) . '.' . $request->logo_website->extension();
            Uploads::store('logowebsite', $request->logo_website, $imageName);

            // Simpan path untuk database
            $logoPath = $imageName;
        }

        // Upload bgd_web
        if ($request->hasFile('bgd_web') && $request->file('bgd_web')->isValid()) {
            // Simpan gambar baru  
            $imageName = time() . '_' . Str::random(10) . '.' . $request->bgd_web->extension();
            Uploads::store('bgdweb', $request->bgd_web, $imageName);

            // Simpan path untuk database
            $bgdPath = $imageName;
        }

        // Siapkan data untuk disimpan
        $data = [
            'id_user' => $request->input('id_user'),
            'sesi_user' => $request->input('sesi_user'),
            'level' => $request->input('level'),
            'nama_toko' => $request->input('nama_toko'),
            'latitude_pusat' => $request->input('latitude_pusat'),
            'longitude_pusat' => $request->input('longitude_pusat'),
            'alamat_pusat' => $request->input('alamat_pusat'),
            'latitude_cabang' => $request->input('latitude_cabang'),
            'longitude_cabang' => $request->input('longitude_cabang'),
            'alamat_cabang' => $request->input('alamat_cabang'),
            'wa_pusat' => $request->input('wa_pusat'),
            'wa_cabang' => $request->input('wa_cabang'),
            'footer_title' => $request->input('footer_title'),
            'link_IG' => $request->input('link_IG'),
            'link_FB' => $request->input('link_FB'),
            'link_Tiktok' => $request->input('link_Tiktok'),
            'logo_website' => $logoPath,
            'bgd_web' => $bgdPath,
        ];

        // Simpan ke database
        $this->M_Website->create($data);

        // Set pesan sukses
        Session::flash('pesan_website', 'Data Website Berhasil Ditambahkan!');

        // Redirect
        return redirect()->route('pemilik_data.website');
    }

    public function edit_website($id_website)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Edit Data Website',
            'title2' => 'Edit Website',
            'website' => $this->M_Website->find($id_website),
            'id_user'         => $dataUser['id_user'] ?? 'ID Tidak Diketahui',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
            'level'         => $dataUser['level'] ?? 'Level Tidak Diketahui',
        ];
        return view('pemilik.website.v_edit', $data);
    }

    public function update_website(Request $request, $id_website)
    {
        // Validasi input form  
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat_pusat' => 'required|string|max:255',
            'alamat_cabang' => 'required|string|max:255',
            'wa_pusat' => 'required|string|max:15', // Sesuaikan dengan format WA yang dibutuhkan  
            'wa_cabang' => 'required|string|max:15', // Sesuaikan dengan format WA yang dibutuhkan  
            'logo_website' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'bgd_web' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $website = M_Website::findOrFail($id_website);

        // Jika ada gambar baru yang diupload  
if ($request->hasFile('logo_website')) {
            // Hapus gambar lama jika ada
            Uploads::delete('logowebsite', $website->logo_website ?? '');

            // Simpan gambar baru
            $imageName = time() . '.' . $request->logo_website->extension();
            Uploads::store('logowebsite', $request->logo_website, $imageName);

            // Update nama gambar di database
            $website->logo_website = $imageName;
        }

        // Jika ada gambar baru yang diupload  
if ($request->hasFile('bgd_web')) {
            // Hapus gambar lama jika ada
            Uploads::delete('bgdweb', $website->bgd_web ?? '');

            // Simpan gambar baru
            $imageName = time() . '.' . $request->bgd_web->extension();
            Uploads::store('bgdweb', $request->bgd_web, $imageName);

            // Update nama gambar di database
            $website->bgd_web = $imageName;
        }

        // Mengambil data dari request  
        $data = $request->only([
            'id_user',
            'sesi_user',
            'level',
            'nama_toko',
            'latitude_pusat',
            'longitude_pusat',
            'alamat_pusat',
            'latitude_cabang',
            'longitude_cabang',
            'alamat_cabang',
            'wa_pusat',
            'wa_cabang',
            'footer_title',
            'link_IG',
            'link_FB',
            'link_Tiktok'
        ]);

        // Update data ke database  
        M_Website::where('id_website', $id_website)->update($data);

        // Update field data website lainnya  
        $website->nama_toko = $request->nama_toko;
        $website->alamat_pusat = $request->alamat_pusat;
        $website->alamat_cabang = $request->alamat_cabang;
        $website->wa_pusat = $request->wa_pusat;
        $website->wa_cabang = $request->wa_cabang;

        // Menyimpan perubahan ke database  
        $website->save();

        // Menampilkan pesan sukses  
        Session::flash('pesan_website', 'Data Website Berhasil Diupdate!');
        return redirect()->route('pemilik_data.website');
    }

    public function delete_website($id_website)
    {
        // Find the product by ID  
        $website = M_Website::findOrFail($id_website);

        // If the product has an existing photo, delete the old photo  
if ($website->logo_website != "") {
            Uploads::delete('logowebsite', $website->logo_website);
        }

        // If the product has an existing photo, delete the old photo
        if ($website->bgd_web != "") {
            Uploads::delete('bgdweb', $website->bgd_web);
        }

        $website->delete();

        Session::flash('pesan_website', 'Data Website Berhasil Dihapus!');
        return redirect()->route('pemilik_data.website');
    }

    // Laporan Penjualan
    public function laporan_penjualan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));
        $data = M_LaporanPenjualan::getLaporanPenjualan($bulan, $tahun);
        $total = M_LaporanPenjualan::getTotalPendapatan($bulan, $tahun);
        
        return view('pemilik.laporan_penjualan.v_laporan', [
            'title' => 'Laporan Penjualan',
            'title2' => 'Laporan Penjualan',
            'laporan' => $data,
            'total' => $total,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    public function grafikPenjualan(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $grafik = M_LaporanPenjualan::getGrafikPenjualan($tahun);
        return response()->json($grafik);
    }

    // Kupon
    public function kupon()
    {
        return view('pemilik.kupon.index', [
            'title' => 'Daftar Kupon',
            'title2' => 'Kupon & Diskon',
            'kupon' => M_Kupon::getBySesi(),
        ]);
    }

    public function add_kupon()
    {
        return view('pemilik.kupon.create', [
            'title' => 'Tambah Kupon',
            'title2' => 'Tambah Kupon',
        ]);
    }

    public function save_kupon(Request $request)
    {
        $request->validate([
            'kode_kupon' => 'required|unique:kupon,kode_kupon',
            'nama_kupon' => 'required',
            'nilai_diskon' => 'required|numeric|min:0',
        ]);
        
        DB::table('kupon')->insert([
            'kode_kupon' => strtoupper($request->kode_kupon),
            'nama_kupon' => $request->nama_kupon,
            'tipe_diskon' => $request->tipe_diskon ?? 'persen',
            'nilai_diskon' => $request->nilai_diskon,
            'min_pembelian' => $request->min_pembelian ?? 0,
            'max_diskon' => $request->max_diskon ?? 0,
            'kuota' => $request->kuota ?? 0,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'sesi_user' => session('sesi_user'),
            'status_aktif' => $request->status_aktif ?? 'Aktif',
        ]);
        
        session()->flash('pesan', 'Kupon berhasil ditambahkan!');
        return redirect()->route('pemilik_data.kupon');
    }

    public function edit_kupon($id_kupon)
    {
        $kupon = DB::table('kupon')->where('id_kupon', $id_kupon)->first();
        return view('pemilik.kupon.edit', [
            'title' => 'Edit Kupon',
            'title2' => 'Edit Kupon',
            'kupon' => $kupon,
        ]);
    }

    public function update_kupon(Request $request, $id_kupon)
    {
        $request->validate([
            'nama_kupon' => 'required',
            'nilai_diskon' => 'required|numeric|min:0',
        ]);
        
        DB::table('kupon')->where('id_kupon', $id_kupon)->update([
            'nama_kupon' => $request->nama_kupon,
            'tipe_diskon' => $request->tipe_diskon ?? 'persen',
            'nilai_diskon' => $request->nilai_diskon,
            'min_pembelian' => $request->min_pembelian ?? 0,
            'max_diskon' => $request->max_diskon ?? 0,
            'kuota' => $request->kuota ?? 0,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'status_aktif' => $request->status_aktif ?? 'Aktif',
        ]);
        
        session()->flash('pesan', 'Kupon berhasil diupdate!');
        return redirect()->route('pemilik_data.kupon');
    }

    public function delete_kupon($id_kupon)
    {
        DB::table('kupon')->where('id_kupon', $id_kupon)->update(['deleted_at' => 1]);
        session()->flash('pesan', 'Kupon berhasil dihapus!');
        return redirect()->route('pemilik_data.kupon');
    }

    // Flash Sale
    public function flash_sale()
    {
        $flashSales = M_FlashSale::getBySesi();

        // Lampirkan item produk per flash sale
        foreach ($flashSales as $fs) {
            $fs->items = DB::table('flash_sale_item')
                ->leftJoin('produk', 'produk.nama_produk', '=', 'flash_sale_item.nama_produk')
                ->leftJoin('stok_produk', 'stok_produk.id_stok', '=', 'flash_sale_item.id_stok')
                ->where('flash_sale_item.id_flash_sale', $fs->id_flash_sale)
                ->where('flash_sale_item.deleted_at', 0)
                ->select(
                    'flash_sale_item.*',
                    'produk.foto_produk',
                    'stok_produk.jumlah_stok_produk',
                    'stok_produk.ukuran_produk'
                )
                ->get();
        }

        return view('pemilik.flash_sale.index', [
            'title' => 'Flash Sale',
            'title2' => 'Flash Sale',
            'flash_sales' => $flashSales,
        ]);
    }

    public function add_flash_sale()
    {
        return view('pemilik.flash_sale.create', [
            'title' => 'Tambah Flash Sale',
            'title2' => 'Tambah Flash Sale',
            'data_stok' => $this->getStokPemilik(),
        ]);
    }

    public function save_flash_sale(Request $request)
    {
        $request->validate([
            'nama_flash_sale' => 'required',
            'id_stok' => 'required|integer',
            'harga_flash_sale' => 'required|numeric|min:1',
            'jumlah_stok' => 'required|integer|min:1',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        // Ambil nama toko dari tabel website milik pemilik
        $website = DB::table('website')
            ->where('sesi_user', session('sesi_user'))
            ->first();
        $namaToko = $website->nama_toko ?? session('sesi_user');

        $id = DB::table('flash_sale')->insertGetId([
            'nama_flash_sale' => $request->nama_flash_sale,
            'deskripsi' => $request->deskripsi,
            'sesi_user' => session('sesi_user'),
            'nama_toko' => $namaToko,
            'diskon_persen' => $request->diskon_persen ?? 0,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => $request->status ?? 'Aktif',
            'deleted_at' => 0,
        ]);

        $stok = DB::table('stok_produk')->where('id_stok', $request->id_stok)->first();

        DB::table('flash_sale_item')->insert([
            'id_flash_sale' => $id,
            'nama_produk' => $stok->nama_produk ?? '',
            'id_stok' => $stok->id_stok ?? 0,
            'harga_normal' => $stok->harga_produk ?? 0,
            'harga_flash_sale' => $request->harga_flash_sale,
            'kuota' => $request->jumlah_stok,
            'terjual' => 0,
            'deleted_at' => 0,
        ]);

        session()->flash('pesan', 'Flash sale berhasil ditambahkan!');
        return redirect()->route('pemilik_data.flash_sale');
    }

    public function edit_flash_sale($id_flash_sale)
    {
        $flashSale = DB::table('flash_sale')
            ->where('id_flash_sale', $id_flash_sale)
            ->where('sesi_user', session('sesi_user'))
            ->firstOrFail();

        $item = DB::table('flash_sale_item')
            ->where('id_flash_sale', $id_flash_sale)
            ->where('deleted_at', 0)
            ->first();

        return view('pemilik.flash_sale.edit', [
            'title' => 'Edit Flash Sale',
            'title2' => 'Edit Flash Sale',
            'flash_sale' => $flashSale,
            'item' => $item,
            'data_stok' => $this->getStokPemilik(),
        ]);
    }

    // Daftar stok milik pemilik untuk dropdown flash sale
    protected function getStokPemilik()
    {
        return DB::table('stok_produk')
            ->where('sesi_user', session('sesi_user'))
            ->orderByDesc('id_stok')
            ->get();
    }

    public function update_flash_sale(Request $request, $id_flash_sale)
    {
        $request->validate([
            'nama_flash_sale' => 'required',
            'id_stok' => 'required|integer',
            'harga_flash_sale' => 'required|numeric|min:1',
            'jumlah_stok' => 'required|integer|min:1',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        $flashSale = DB::table('flash_sale')
            ->where('id_flash_sale', $id_flash_sale)
            ->where('sesi_user', session('sesi_user'))
            ->firstOrFail();

        DB::table('flash_sale')->where('id_flash_sale', $id_flash_sale)->update([
            'nama_flash_sale' => $request->nama_flash_sale,
            'deskripsi' => $request->deskripsi,
            'diskon_persen' => $request->diskon_persen ?? 0,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => $request->status ?? 'Aktif',
        ]);

        $stok = DB::table('stok_produk')->where('id_stok', $request->id_stok)->first();

        // Ganti item lama dengan data terbaru
        DB::table('flash_sale_item')
            ->where('id_flash_sale', $id_flash_sale)
            ->where('deleted_at', 0)
            ->update(['deleted_at' => 1]);

        DB::table('flash_sale_item')->insert([
            'id_flash_sale' => $id_flash_sale,
            'nama_produk' => $stok->nama_produk ?? '',
            'id_stok' => $stok->id_stok ?? 0,
            'harga_normal' => $stok->harga_produk ?? 0,
            'harga_flash_sale' => $request->harga_flash_sale,
            'kuota' => $request->jumlah_stok,
            'terjual' => 0,
            'deleted_at' => 0,
        ]);

        session()->flash('pesan', 'Flash sale berhasil diupdate!');
        return redirect()->route('pemilik_data.flash_sale');
    }

    public function delete_flash_sale($id_flash_sale)
    {
        DB::table('flash_sale')
            ->where('id_flash_sale', $id_flash_sale)
            ->where('sesi_user', session('sesi_user'))
            ->update(['deleted_at' => 1]);

        DB::table('flash_sale_item')
            ->where('id_flash_sale', $id_flash_sale)
            ->update(['deleted_at' => 1]);

        session()->flash('pesan', 'Flash sale berhasil dihapus!');
        return redirect()->route('pemilik_data.flash_sale');
    }

    // ==================== RETUR / PENGEMBALIAN (PEMILIK) ====================

    public function view_retur(Request $request)
    {
        $sesiUser = Session::get('sesi_user');

        $keyword = $request->input('search', '');
        $statusFilter = $request->input('status', '');

        $retur = M_Retur::getReturAdmin($sesiUser, $keyword, $statusFilter);

        return view('pemilik.retur.v_retur', [
            'title' => 'Data Retur',
            'title2' => 'Data Retur & Pengembalian',
            'retur' => $retur,
        ]);
    }

    public function detail_retur($id_retur)
    {
        $sesiUser = Session::get('sesi_user');

        $detail = M_Retur::detailRetur($id_retur);

        if (!$detail || $detail->sesi_user != $sesiUser) {
            Session::flash('error', 'Data retur tidak ditemukan.');
            return redirect()->route('pemilik_data.view_retur');
        }

        return view('pemilik.retur.v_detail_retur', [
            'title' => 'Detail Retur',
            'title2' => 'Detail Retur',
            'retur' => $detail,
        ]);
    }

    public function verifikasi_retur(Request $request, $id_retur)
    {
        $sesiUser = Session::get('sesi_user');

        $request->validate([
            'keputusan' => 'required|in:setuju,tolak',
            'catatan_admin' => 'nullable|string|max:2000',
        ]);

        $retur = M_Retur::where('id_retur', $id_retur)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$retur) {
            Session::flash('error', 'Data retur tidak ditemukan.');
            return redirect()->route('pemilik_data.view_retur');
        }

        if ($retur->status !== 'Menunggu Verifikasi') {
            Session::flash('error', 'Retur hanya bisa diverifikasi pada status "Menunggu Verifikasi".');
            return redirect()->route('pemilik_data.detail_retur', $id_retur);
        }

        if ($request->keputusan === 'setuju') {
            M_Retur::where('id_retur', $id_retur)->update([
                'status' => 'Disetujui',
                'catatan_admin' => $request->catatan_admin,
                'waktu_verifikasi' => now(),
                'updated_at' => now(),
            ]);

            try {
                M_Notifikasi::kirim([
                    'id_pelanggan' => $retur->id_pelanggan,
                    'nama_pelanggan' => $retur->nama_pelanggan,
                    'judul' => 'Retur Disetujui',
                    'pesan' => "Retur {$retur->kode_retur} disetujui. Silakan kirim barang kembali sesuai instruksi.",
                    'tipe' => 'sukses',
                    'link' => route('pelanggan_data.detailRetur', $id_retur),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi retur setuju: ' . $e->getMessage());
            }

            Session::flash('success', "Retur {$retur->kode_retur} disetujui.");
        } else {
            M_Retur::where('id_retur', $id_retur)->update([
                'status' => 'Ditolak',
                'catatan_admin' => $request->catatan_admin,
                'waktu_verifikasi' => now(),
                'updated_at' => now(),
            ]);

            try {
                M_Notifikasi::kirim([
                    'id_pelanggan' => $retur->id_pelanggan,
                    'nama_pelanggan' => $retur->nama_pelanggan,
                    'judul' => 'Retur Ditolak',
                    'pesan' => "Retur {$retur->kode_retur} ditolak. " . ($request->catatan_admin ?: 'Saat ini tidak memenuhi syarat retur.'),
                    'tipe' => 'peringatan',
                    'link' => route('pelanggan_data.detailRetur', $id_retur),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi retur tolak: ' . $e->getMessage());
            }

            Session::flash('success', "Retur {$retur->kode_retur} ditolak.");
        }

        return redirect()->route('pemilik_data.detail_retur', $id_retur);
    }

    public function terima_retur($id_retur)
    {
        $sesiUser = Session::get('sesi_user');

        $retur = M_Retur::where('id_retur', $id_retur)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$retur) {
            Session::flash('error', 'Data retur tidak ditemukan.');
            return redirect()->route('pemilik_data.view_retur');
        }

        if ($retur->status !== 'Barang Dalam Perjalanan') {
            Session::flash('error', 'Barang retur hanya bisa diterima pada status "Barang Dalam Perjalanan".');
            return redirect()->route('pemilik_data.detail_retur', $id_retur);
        }

        M_Retur::where('id_retur', $id_retur)->update([
            'status' => 'Barang Diterima',
            'updated_at' => now(),
        ]);

        try {
            M_Notifikasi::kirim([
                'id_pelanggan' => $retur->id_pelanggan,
                'nama_pelanggan' => $retur->nama_pelanggan,
                'judul' => 'Barang Retur Diterima',
                'pesan' => "Barang retur {$retur->kode_retur} telah diterima toko dan sedang diproses.",
                'tipe' => 'sukses',
                'link' => route('pelanggan_data.detailRetur', $id_retur),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi retur terima: ' . $e->getMessage());
        }

        Session::flash('success', 'Barang retur diterima.');
        return redirect()->route('pemilik_data.detail_retur', $id_retur);
    }

    public function selesaikan_retur(Request $request, $id_retur)
    {
        $sesiUser = Session::get('sesi_user');

        $request->validate([
            'jumlah_refund' => 'nullable|numeric|min:0',
            'status_refund' => 'nullable|in:Belum Diproses,Refund Diproses,Refund Selesai',
        ]);

        $retur = M_Retur::where('id_retur', $id_retur)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$retur) {
            Session::flash('error', 'Data retur tidak ditemukan.');
            return redirect()->route('pemilik_data.view_retur');
        }

        if (!in_array($retur->status, ['Barang Diterima', 'Selesai'])) {
            Session::flash('error', 'Retur hanya bisa diselesaikan saat barang sudah diterima.');
            return redirect()->route('pemilik_data.detail_retur', $id_retur);
        }

        M_Retur::where('id_retur', $id_retur)->update([
            'status' => 'Selesai',
            'jumlah_refund' => $request->jumlah_refund ?: 0,
            'status_refund' => $request->status_refund ?: 'Belum Diproses',
            'catatan_admin' => $request->catatan_admin ?? $retur->catatan_admin,
            'waktu_selesai' => now(),
            'updated_at' => now(),
        ]);

        // Kirim notifikasi in-app + email auto-refund
        $retur = M_Retur::where('id_retur', $id_retur)->first();
        M_Retur::notifikasiSelesai($retur);

        Session::flash('success', 'Retur diselesaikan.');
        return redirect()->route('pemilik_data.detail_retur', $id_retur);
    }

}
