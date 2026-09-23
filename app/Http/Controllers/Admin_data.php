<?php

namespace App\Http\Controllers;

use App\Models\M_User;
use App\Models\M_Produk;
use App\Models\M_Satuan;
use App\Models\M_Stok;
use App\Models\M_Pembelian;
use App\Models\M_Pembayaran;
use App\Models\M_Benefit;
use App\Models\M_Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use App\Models\M_LaporanPenjualan;
use App\Support\Uploads;
use App\Models\M_Retur;
use App\Models\M_Notifikasi;

class Admin_data extends Controller
{
    protected $M_User;
    protected $M_Produk;
    protected $M_Satuan;
    protected $M_Stok;
    protected $M_Pembelian;
    protected $M_Pembayaran;
    protected $M_Benefit;

    public function __construct()
    {
        $this->M_User = new M_User();
        $this->M_Produk = new M_Produk();
        $this->M_Satuan = new M_Satuan();
        $this->M_Stok = new M_Stok();
        $this->M_Pembelian = new M_Pembelian();
        $this->M_Pembayaran = new M_Pembayaran();
        $this->M_Benefit = new M_Benefit();
    }

    public function profil()
    {
        $id_user = session('id_user');

        $data = [
            'title' => 'Data Profil',
            'title2' => 'Profil Saya',
            'profil' => (new M_User)->getProfile($id_user),
        ];

        return view('admin.update_profile.profil', $data);
    }

    public function edit($id_user)
    {
        $data = [
            'title' => 'Data Profil',
            'title2' => 'Edit Profil',
            'profil' => (new M_User)->detailProfile($id_user),
        ];

        return view('admin.update_profile.edit_profil', $data);
    }

    public function update_profile(Request $request)
    {
        $id_user = session('id_user');

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'fullname' => 'required',
            'foto_user' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024',
        ]);

        $data = [
            'username' => $request->username,
            'password' => $request->password,
            'fullname' => $request->fullname,
        ];

        // Upload foto jika ada
        if ($request->hasFile('foto_user')) {
            $file = $request->file('foto_user');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Hapus foto lama
            $old = (new M_User)->find($id_user);
            if (!empty($old->foto_user)) {
                Uploads::delete('fotouser', $old->foto_user);
            }

            Uploads::store('fotouser', $file, $filename);
            $data['foto_user'] = $filename;
        }

        // Simpan ke DB lewat model
        $model = new M_User();
        $model->edit($id_user, $data); // Tidak perlu cek sukses/tidak

        // Update session meskipun tidak ada perubahan
        session()->put($data);
        if (isset($data['foto_user'])) {
            session()->put('foto_user', $data['foto_user']);
        }

        return redirect()->route('admin_data.profil')->with('pesan_profil', 'Profil berhasil diperbarui!');
    }

    // Produk methods  
    public function view_produk()
    {
        $data = [
            'title' => 'List Produk',
            'title2' => 'List Produk',
            'data_produk' => $this->M_Produk->getProduk(),
        ];
        return view('admin.produk.index', $data);
    }

    public function generateKodeStok(Request $request)
    {
        // Mengambil data dari request
        $nama_produk = $request->input('nama_produk');

        if (!$nama_produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nama produk tidak diberikan'
            ]);
        }

        // Ambil data produk dari database
        $produk = M_Produk::where('nama_produk', $nama_produk)->first();

        if (!$produk) {
            Log::error('Nama produk tidak ditemukan: ' . $nama_produk);  // Menggunakan Log facade
            return response()->json([
                'status' => 'error',
                'message' => 'Nama produk tidak ditemukan'
            ]);
        }

        // Generate inisial nama produk
        $kata_produk = explode(' ', $nama_produk);
        $kode_produk = strtoupper(implode('', array_map(function ($kata) {
            return substr($kata, 0, 1);
        }, array_slice($kata_produk, 0, 2))));

        // Inisialisasi nomor urut
        $nextNumber = 1;

        do {
            $newKodeStok = $kode_produk . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Cek apakah kode stok sudah ada di tabel stok produk
            $exists = M_Stok::where('kode_stok', $newKodeStok)->exists();

            if (!$exists) {
                break; // Jika kode tidak ditemukan, gunakan kode ini
            }

            $nextNumber++;
        } while (true);

        // Kembalikan kode stok
        return response()->json([
            'status' => 'success',
            'kode_stok' => $newKodeStok
        ]);
    }

    public function stok()
    {
        $data = [
            'title' => 'Daftar Stok Produk',
            'title2' => 'Data Stok Produk',
            'data_stok' => $this->M_Stok->getStok(),
        ];
        return view('admin.stok_produk.v_stok', $data);
    }

    public function tot_stok()
    {
        $data = [
            'title' => 'Daftar Stok Produk',
            'title2' => 'Data Stok Produk',
            'data_stok' => $this->M_Stok->getTotStok()

        ];
        return view('admin.stok_produk.v_tot_stok', $data);
    }

    public function add_stok()
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
            'title' => 'Tambah Stok Produk',
            'title2' => 'Data Stok Produk',
            'data_produk' => $this->M_Stok->getProduk(),
            'data_satuan_produk' => $data_satuan_produk,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',

        ];
        return view('admin.stok_produk.v_add', $data);
    }

    public function save_stok(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jumlah_stok_produk' => 'required|integer|min:1',
            'harga_produk' => 'required|numeric',
        ]);

        // Ambil data dari request
        $data = $request->only([
            'sesi_user',
            'kode_stok',
            'jumlah_stok_produk',
            'nama_produk',
            'harga_produk',
            'jenis_produk',
            'satuan_produk',
            'ukuran_produk',
            'berat_produk',
            'satuan_berat',
            'total_berat',
        ]);

        $data['tanggal_masuk_produk'] = now();

        // Simpan data
        (new M_Stok())->add($data);

        // Redirect dengan pesan sukses
        session()->flash('pesan', 'Data Stok Produk Berhasil Ditambahkan!');
        return redirect()->route('admin_data.stok');
    }

    public function edit_stok($id_stok)
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
            'title' => 'Edit Stok Produk',
            'title2' => 'Data Stok Produk',
            'data_stok' => M_Stok::find($id_stok),
            'data_produk' => $this->M_Stok->getProduk(),
            'data_satuan_produk' => $data_satuan_produk,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('admin.stok_produk.v_edit', $data);
    }

    public function update_stok(Request $request, $id_stok)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jumlah_stok_produk' => 'required|integer|min:1',
            'harga_produk' => 'required|numeric'
        ]);

        // Ambil data dari request
        $data = $request->only([
            'sesi_user',
            'kode_stok',
            'jumlah_stok_produk',
            'nama_produk',
            'jenis_produk',
            'satuan_produk',
            'ukuran_produk',
            'satuan_berat',
            'tanggal_masuk_produk',
        ]);

        // Harga produk
        $harga_produk = (float) str_replace(['Rp. ', '.'], '', $request->harga_produk);

        // Tambahkan field tambahan
        $data['harga_produk'] = $harga_produk;

        // Panggil fungsi updateData dari model
        try {
            $model = new M_Stok();
            $model->updateData($id_stok, $data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Redirect dengan pesan sukses
        session()->flash('pesan', 'Data Stok Produk Berhasil Diupdate!');
        return redirect()->route('admin_data.stok');
    }

    public function delete_stok($id_stok)
    {
        // Hapus data berdasarkan id_stok  
        M_Stok::destroy($id_stok);

        // Set pesan sukses  
        session()->flash('pesan', 'Data Stok Produk Ini Berhasil Di Hapus !');

        // Redirect kembali ke halaman daftar stok produk  
        return redirect()->route('admin_data.stok');
    }
    
    // Daftar Pembelian Produk
    public function view_jual()
    {
        return view('admin.penjualan.v_jual', [
            'title' => 'Daftar Penjualan Produk',
            'title2' => 'Data Penjualan Produk',
            'pembelian' => $this->M_Pembelian->get_beli_by_sesi(),
        ]);
    }

    // Daftar Pembelian Produk
    public function view_totjual()
    {
        return view('admin.penjualan.v_tot_jual', [
            'title' => 'Daftar Penjualan Produk',
            'title2' => 'Data Penjualan Produk',
            'pembelian' => $this->M_Pembelian->getTotJual(),
        ]);
    }

    public function view_bayar()
    {
        // View Composer di AppServiceProvider sudah menghitung $jumlahBayar
        // dan $daftarNotifikasi secara otomatis untuk semua view yang memakai layouts.template
        $modelPembayaran = new \App\Models\M_Pembayaran();
        $dataPembayaran = $modelPembayaran->get_bayar_by_sesi();

        return view('admin.pembayaran.v_bayar', [
            'title' => 'Daftar Pembayaran Produk',
            'title2' => 'Data Pembayaran Produk',
            'pembayaran' => $dataPembayaran,
        ]);
}

    // View Foto Bayar
    public function viewFoto($id_bayar)
    {
        return view('admin.pembayaran.v_foto', [
            'title' => 'View Foto Bayar',
            'title2' => 'Data Foto',
            'pembayaran' => $this->M_Pembayaran->detailBayar($id_bayar),
        ]);
    }

    // Konfirmasi Status Bayar
    public function konfirmStatusBayar(Request $request)
    {
        $request->validate([
            'status_bayar' => 'required',
            'alasan_batal' => 'nullable|string',
        ]);

        $pembayaran = M_Pembayaran::findOrFail($request->id_bayar);

        $dataUpdate = [
            'status_bayar' => $request->status_bayar,
            'alasan_batal' => $request->status_bayar === 'Dibatalkan' ? $request->alasan_batal : null,
        ];

        $pembayaran->update($dataUpdate);

        return redirect()->route('admin_data.view_bayar')->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function view_benefit()
    {
        return view('admin.keuntungan.v_untung', [
            'title' => 'Daftar Keuntungan Penjualan',
            'title2' => 'Data  Keuntungan Penjualan',
            'benefit' => $this->M_Benefit->getBenefit_By_User(),
        ]);
    }

    public function view_totbenefit()
    {
        return view('admin.keuntungan.v_tot_untung', [
            'title' => 'Daftar Keuntungan Penjualan',
            'title2' => 'Data  Keuntungan Penjualan',
            'benefit' => $this->M_Benefit->getTotBenefit_By_User(),
        ]);
    }

    public function chat(Request $request)
    {
        $sesiUser = session('sesi_user');
        $daftarChat = M_Chat::daftarChatAdmin($sesiUser);

        $data = [
            'title' => 'Chat Pelanggan',
            'title2' => 'Chat Pelanggan',
            'daftar_chat' => $daftarChat,
            'chat_aktif' => null,
        ];

        return view('admin.chat.inbox', $data);
    }

    public function chatDetail($id_chat, Request $request)
    {
        $sesiUser = session('sesi_user');

        $chatAktif = M_Chat::where('id_chat', $id_chat)
            ->where('sesi_user', $sesiUser)
            ->firstOrFail();

        // Skip dibaca update since dibaca column is not available

        $daftarChat = M_Chat::daftarChatAdmin($sesiUser);

        $data = [
            'title' => 'Detail Chat Pelanggan',
            'title2' => 'Detail Chat Pelanggan',
            'daftar_chat' => $daftarChat,
            'chat_aktif' => $chatAktif,
        ];

        return view('admin.chat.chat', $data);
    }

    public function chatKirim(Request $request)
    {
        $request->validate([
            'id_chat' => 'required|integer',
            'pesan' => 'required|string|max:1000',
        ]);

        $sesiUser = session('sesi_user');
        $id_user = session('id_user');
        $fullname = session('fullname');

        $chat = M_Chat::where('id_chat', $request->id_chat)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$chat) {
            return response()->json(['success' => false, 'message' => 'Chat tidak ditemukan.'], 403);
        }

        M_Chat::kirimPesan($chat->id_chat, 'admin', $id_user, $fullname, trim($request->pesan));

        return response()->json(['success' => true]);
    }

    public function chatPesan(Request $request, $id_chat)
    {
        $sesiUser = session('sesi_user');
        $chat = M_Chat::where('id_chat', $id_chat)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$chat) {
            return response()->json(['success' => false, 'message' => 'Chat tidak ditemukan.'], 403);
        }

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

    public function laporan_penjualan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));
        $data = M_LaporanPenjualan::getLaporanPenjualan($bulan, $tahun);
        $total = M_LaporanPenjualan::getTotalPendapatan($bulan, $tahun);
        
        return view('admin.laporan_penjualan.v_laporan', [
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

    public function moderasi_ulasan()
    {
        $ulasan = DB::table('ulasan')
            ->leftJoin('stok_produk', 'stok_produk.id_stok', '=', 'ulasan.id_stok')
            ->select('ulasan.*', 'stok_produk.nama_produk', 'stok_produk.sesi_user')
            ->where('ulasan.terverifikasi', 0)
            ->orderByDesc('ulasan.tanggal_ulasan')
            ->get();
        
        return view('admin.ulasan.moderasi', [
            'title' => 'Moderasi Ulasan',
            'title2' => 'Moderasi Ulasan',
            'ulasan' => $ulasan,
        ]);
    }

    public function verifikasi_ulasan($id)
    {
        DB::table('ulasan')->where('id_ulasan', $id)->update(['terverifikasi' => 1]);
        return back()->with('success', 'Ulasan berhasil diverifikasi.');
    }

    public function tolak_ulasan($id)
    {
        DB::table('ulasan')->where('id_ulasan', $id)->update(['terverifikasi' => -1]);
        return back()->with('success', 'Ulasan ditolak.');
    }

    // ==================== RETUR / PENGEMBALIAN (ADMIN) ====================

    public function view_retur(Request $request)
    {
        $sesiUser = Session::get('sesi_user');

        $keyword = $request->input('search', '');
        $statusFilter = $request->input('status', '');

        $retur = M_Retur::getReturAdmin($sesiUser, $keyword, $statusFilter);

        return view('admin.retur.v_retur', [
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
            return redirect()->route('admin_data.view_retur');
        }

        return view('admin.retur.v_detail_retur', [
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
            return redirect()->route('admin_data.view_retur');
        }

        if ($retur->status !== 'Menunggu Verifikasi') {
            Session::flash('error', 'Retur hanya bisa diverifikasi pada status "Menunggu Verifikasi".');
            return redirect()->route('admin_data.detail_retur', $id_retur);
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

        return redirect()->route('admin_data.detail_retur', $id_retur);
    }

    public function terima_retur($id_retur)
    {
        $sesiUser = Session::get('sesi_user');

        $retur = M_Retur::where('id_retur', $id_retur)
            ->where('sesi_user', $sesiUser)
            ->first();

        if (!$retur) {
            Session::flash('error', 'Data retur tidak ditemukan.');
            return redirect()->route('admin_data.view_retur');
        }

        if ($retur->status !== 'Barang Dalam Perjalanan') {
            Session::flash('error', 'Barang retur hanya bisa diterima pada status "Barang Dalam Perjalanan".');
            return redirect()->route('admin_data.detail_retur', $id_retur);
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
        return redirect()->route('admin_data.detail_retur', $id_retur);
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
            return redirect()->route('admin_data.view_retur');
        }

        if (!in_array($retur->status, ['Barang Diterima', 'Selesai'])) {
            Session::flash('error', 'Retur hanya bisa diselesaikan saat barang sudah diterima.');
            return redirect()->route('admin_data.detail_retur', $id_retur);
        }

        M_Retur::where('id_retur', $id_retur)->update([
            'status' => 'Selesai',
            'jumlah_refund' => $request->jumlah_refund ?: 0,
            'status_refund' => $request->status_refund ?: 'Belum Diproses',
            'catatan_admin' => $request->catatan_admin ?? $retur->catatan_admin,
            'waktu_selesai' => now(),
            'updated_at' => now(),
        ]);

        try {
            M_Notifikasi::kirim([
                'id_pelanggan' => $retur->id_pelanggan,
                'nama_pelanggan' => $retur->nama_pelanggan,
                'judul' => 'Retur Selesai',
                'pesan' => "Retur {$retur->kode_retur} telah selesai diproses. Terima kasih.",
                'tipe' => 'sukses',
                'link' => route('pelanggan_data.detailRetur', $id_retur),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi retur selesai: ' . $e->getMessage());
        }

        Session::flash('success', 'Retur diselesaikan.');
        return redirect()->route('admin_data.detail_retur', $id_retur);
    }
}
