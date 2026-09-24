<?php

namespace App\Http\Controllers;

use App\Models\M_Home_superadmin;
use App\Models\M_Biaya_platform;
use App\Models\M_Event;
use App\Models\M_Website;
use Illuminate\Http\Request;
use App\Models\M_User;
use App\Models\M_Bank;
use App\Models\M_Kurir;
use App\Models\M_Benefit;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Support\Uploads;

class Superadmin_data extends Controller
{
    protected $M_Home_superadmin;
    protected $M_Biaya_platform;
    protected $M_Event;
    protected $M_Website;
    protected $M_User;
    protected $M_Bank;
    protected $M_Kurir;
    protected $M_Benefit;

    public function __construct()
    {
        $this->M_Home_superadmin = new M_Home_superadmin();
        $this->M_Biaya_platform = new M_Biaya_platform();
        $this->M_Bank = new M_Bank();
        $this->M_Event = new M_Event();
        $this->M_Website = new M_Website();
        $this->M_User = new M_User();
        $this->M_Kurir = new M_Kurir();

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

        return view('superadmin.update_profile.profil', $data);
    }

    public function edit($id_user)
    {
        $data = [
            'title' => 'Data Profil',
            'title2' => 'Edit Profil',
            'profil' => (new M_User)->detailProfile($id_user),
        ];

        return view('superadmin.update_profile.edit_profil', $data);
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

        return redirect()->route('superadmin_data.profil')->with('pesan_profil', 'Profil berhasil diperbarui!');
    }

    public function view_totbenefit()
    {
        return view('superadmin.keuntungan.v_tot_untung', [
            'title' => 'Daftar Keuntungan Penjualan',
            'title2' => 'Data  Keuntungan Penjualan',
            'benefit' => $this->M_Benefit->getTotBenefit(),
        ]);
    }

    public function komisi()
    {
        $data = [
            'title' => 'Daftar komisi',
            'title2' => 'Data komisi',
            'biaya' => $this->M_Biaya_platform->getBiaya_ByUser(),
        ];
        return view('superadmin.komisi.v_komisi', $data);
    }

    public function add_komisi()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');

        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Tambah komisi',
            'title2' => 'Tambah komisi',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.komisi.v_add', $data);
    }

    public function save_komisi(Request $request)
    {
        $request->validate([
            'persentase' => 'required|numeric',
        ]);

        M_Biaya_platform::create([
            'sesi_user' => session('sesi_user'),
            'persentase' => $request->persentase,
            'deskripsi' => $request->deskripsi,
        ]);

        // Pesan sukses  
        session()->flash('pesan', 'Data komisi Ini Berhasil Ditambahkan !');
        return redirect()->route('superadmin_data.komisi');
    }

    public function edit_komisi($id_biaya)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');

        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $biaya = M_Biaya_platform::findOrFail($id_biaya);

        $data = [
            'title' => 'Edit komisi',
            'title2' => 'Edit komisi',
            'biaya' => $biaya,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.komisi.v_edit', $data);
    }

    public function update_komisi(Request $request, $id_biaya)
    {
        $request->validate([
            'persentase' => 'required|numeric',
        ]);

        // Ambil data dari request
        $data = $request->only([
            'sesi_user',
            'persentase',
            'deskripsi',
        ]);

        M_Biaya_platform::find($id_biaya)->update($data);

        // Pesan sukses  
        session()->flash('pesan', 'Data komisi Ini Berhasil Diupdate !');
        return redirect()->route('superadmin_data.komisi');
    }

    public function delete_komisi($id_biaya)
    {
        // Hapus data berdasarkan id_stok  
        M_Biaya_platform::destroy($id_biaya);

        // Set pesan sukses  
        session()->flash('pesan', 'Data komisi Ini Berhasil Di Hapus !');

        // Redirect kembali ke halaman daftar stok produk  
        return redirect()->route('superadmin_data.komisi');
    }

    public function bank()
    {
        $data = [
            'title' => 'List Bank',
            'title2' => 'List Bank',
            'bank' => $this->M_Bank->getBank(),
        ];
        return view('superadmin.bank.v_bank', $data);
    }

    public function add_bank()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Bank',
            'title2' => 'Add Bank',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.bank.create', $data);
    }

    public function save_bank(Request $request)
    {
        // Validasi input  
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'a_n' => 'required|string|max:255',
        ]);

        // Simpan data bank ke database  
        M_Bank::create([
            'sesi_user' => $request->sesi_user,
            'nama_bank' => $request->nama_bank,
            'no_rek' => $request->no_rek,
            'a_n' => $request->a_n,
        ]);

        // Redirect dengan pesan sukses  
        return redirect()->route('superadmin_data.bank')->with('success', 'Bank berhasil ditambahkan.');
    }

    public function edit_bank($id_bank)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $bank = M_Bank::findOrFail($id_bank);
        $data = [
            'title' => 'Edit Bank',
            'title2' => 'Edit Bank',
            'bank' => $bank,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.bank.edit', $data);
    }

    public function update_bank(Request $request, $id_bank)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'a_n' => 'required|string|max:255',
        ]);

        // Ambil data valid dari request
        $data = $request->only([
            'sesi_user',
            'nama_bank',
            'no_rek',
            'a_n',
        ]);

        // Update data ke database
        M_Bank::where('id_bank', $id_bank)->update($data);

        return redirect()->route('superadmin_data.bank')->with('success', 'Bank berhasil diperbarui.');
    }

    public function delete_bank($id_bank)
    {
        // Temukan pengguna berdasarkan ID  
        $bank = M_Bank::findOrFail($id_bank);

        // Hapus pengguna dari database  
        $bank->delete();

        // Redirect kembali dengan pesan sukses  
        return redirect()->route('superadmin_data.bank')->with('success', 'Bank deleted successfully.');
    }

    public function restore_bank($id_bank)
    {
        // Restore produk yang telah dihapus dengan SoftDelete  
        $bank = M_Bank::withTrashed()->findOrFail($id_bank);
        $bank->restore(); // Restore item yang telah dihapus secara soft delete  

        return redirect()->route('superadmin_data.bank')->with('pesan', 'Data Berhasil Di Restore!');
    }

    public function delete_hard_bank($id_bank)
    {
        // Hapus data bank secara permanen  
        $bank = M_Bank::withTrashed()->findOrFail($id_bank);
        $bank->forceDelete(); // Menghapus secara permanen  

        session()->flash('pesan', 'Data Bank Ini Berhasil Di Hapus!');
        return redirect()->route('superadmin.data_dihapus');
    }

    public function kurir()
    {
        $data = [
            'title' => 'Daftar Kurir',
            'title2' => 'Data Kurir',
            'kurir' => $this->M_Kurir->getKurir()
        ];
        return view('superadmin.kurir.index', $data);
    }

    public function add_kurir()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Tambah Kurir',
            'title2' => 'Tambah Kurir',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];

        return view('superadmin.kurir.create', $data);
    }

    public function save_kurir(Request $request)
    {
        $request->validate([
            'ongkir' => 'required',
        ]);

        M_Kurir::create([
            'jenis_kurir' => $request->jenis_kurir,
            'tipe_kurir' => $request->tipe_kurir,
            'ongkir' => $request->ongkir,
            'sesi_user' => $request->sesi_user,
        ]);

        Session::flash('pesan_kurir', 'Data Kurir berhasil ditambahkan!');
        return redirect()->route('superadmin_data.kurir');
    }

    public function edit_kurir($id_kurir)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $kurir = M_Kurir::findOrFail($id_kurir);

        $data = [
            'title' => 'Edit Kurir Produk',
            'title2' => 'Edit Kurir',
            'data_kurir' => $kurir,
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];

        return view('superadmin.kurir.edit', $data);
    }

    public function update_kurir(Request $request, $id_kurir)
    {
        $request->validate([
            'ongkir' => 'required',
        ]);

        // Ambil data valid dari request
        $data = $request->only([
            'jenis_kurir',
            'tipe_kurir',
            'ongkir',
            'sesi_user',
        ]);

        // Update data ke database
        M_Kurir::where('id_kurir', $id_kurir)->update($data);

        Session::flash('pesan_kurir', 'Data Kurir berhasil diubah!');
        return redirect()->route('superadmin_data.kurir');
    }

    public function delete_kurir($id_kurir)
    {
        $kurir = M_Kurir::findOrFail($id_kurir);
        $kurir->delete();

        Session::flash('pesan_kurir', 'Data Kurir berhasil dihapus!');
        return redirect()->route('superadmin_data.kurir');
    }

    public function view_website()
    {
        $data = [
            'title' => 'Daftar Website Toko',
            'title2' => 'Data Website',
            'website' =>  $this->M_Website->getWebsite_By_Level(),
        ];
        return view('superadmin.website.v_all_web', $data);
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

        return redirect()->route('superadmin_data.website')->with('success', 'Status Web berhasil diperbarui.');
    }

    // Konfirmasi Status
    public function konfirmStatusWebAll(Request $request)
    {
        $request->validate([
            'status_website' => 'required',
        ]);

        $status_web = M_Website::findOrFail($request->id_website);

        $dataUpdate = [
            'status_website' => $request->status_website,
            'alasan_nonaktif' => $request->status_website === 'Non-aktif' ? $request->alasan_nonaktif : null,

        ];

        $status_web->update($dataUpdate);

        return redirect()->route('superadmin_data.view_website')->with('success', 'Status Web berhasil diperbarui.');
    }

    // Konfirmasi Verifikasi Lapak
    public function konfirmVerifikasi(Request $request)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        $website = M_Website::findOrFail($request->id_website);

        $dataUpdate = [
            'status_verifikasi' => $request->status_verifikasi,
            'alasan_ditolak' => $request->status_verifikasi === 'Ditolak' ? $request->alasan_ditolak : null,
        ];

        $website->update($dataUpdate);

        // Kirim notifikasi ke pemilik
        $pesan = match($request->status_verifikasi) {
            'Disetujui' => 'Lapak ' . $website->nama_toko . ' telah DISETUJUI oleh admin. Silakan login untuk mulai beroperasi.',
            'Ditolak' => 'Lapak ' . $website->nama_toko . ' DITOLAK: ' . ($request->alasan_ditolak ?? 'Tidak ada alasan.'),
            'Menunggu' => 'Lapak ' . $website->nama_toko . ' dikembalikan ke status MENUNGGU review.',
            default => 'Status verifikasi lapak ' . $website->nama_toko . ' diperbarui.',
        };

        DB::table('notifikasi')->insert([
            'sesi_user' => $website->sesi_user,
            'judul' => 'Status Verifikasi Lapak',
            'pesan' => $pesan,
            'tipe' => $request->status_verifikasi === 'Disetujui' ? 'success' : ($request->status_verifikasi === 'Ditolak' ? 'danger' : 'info'),
            'link' => route('auth.login_user'),
            'dibaca' => 0,
            'waktu' => now(),
        ]);

        return redirect()->route('superadmin_data.view_website')->with('success', 'Status verifikasi berhasil diperbarui. Notifikasi terkirim ke pemilik.');
    }

    public function website()
    {
        $data = [
            'title' => 'Daftar Platform',
            'title2' => 'Data Platform',
            'website' =>  $this->M_Website->getWebsite_By_Sesi(),
        ];
        return view('superadmin.website.v_website', $data);
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
            'data_promo' => $this->M_Website->getEvent(),
            'id_user'         => $dataUser['id_user'] ?? 'ID Tidak Diketahui',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
            'level'         => $dataUser['level'] ?? 'Level Tidak Diketahui',
        ];
        return view('superadmin.website.v_create', $data);
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
            'id_promo' => $request->input('id_promo'),
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
            'email_toko' => $request->input('email_toko'),
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
        return redirect()->route('superadmin_data.website');
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
            'data_promo' => $this->M_Website->getEvent(),
            'id_user'         => $dataUser['id_user'] ?? 'ID Tidak Diketahui',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
            'level'         => $dataUser['level'] ?? 'Level Tidak Diketahui',
        ];
        return view('superadmin.website.v_edit', $data);
    }

    public function update_website(Request $request, $id_website)
    {
        // Validasi input form  
        $request->validate([
            'nama_toko' => 'required',
            'id_user' => 'required',
            'sesi_user' => 'required',
            'level' => 'required',
            'email_toko' => 'nullable|email|max:191',
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
            'id_promo',
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
            'email_toko',
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
        $website->email_toko = $request->email_toko;

        // Menyimpan perubahan ke database  
        $website->save();

        // Menampilkan pesan sukses  
        Session::flash('pesan_website', 'Data Website Berhasil Diupdate!');
        return redirect()->route('superadmin_data.website');
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
        return redirect()->route('superadmin_data.website');
    }

    public function event()
    {
        $data = [
            'title' => 'Daftar Event Platform',
            'title2' => 'Data Event Platform',
            'event' =>  $this->M_Event->getEvent_By_Sesi(),
        ];
        return view('superadmin.event.v_event', $data);
    }

    // Konfirmasi Status Event
    public function konfirmStatusEvent_By_Sesi(Request $request)
    {
        $request->validate([
            'status_event' => 'required',
            'id_promo' => 'required',
        ]);

        $event = M_Event::findOrFail($request->id_promo);

        $event->update([
            'status_event' => $request->status_event,
        ]);

        return redirect()->route('superadmin_data.event')->with('success', 'Status Event berhasil diperbarui.');
    }

    public function add_event()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');

        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Add Event',
            'title2' => 'Add Event',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.event.v_create', $data);
    }

    public function save_event(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_promo' => 'required',
            'sesi_user' => 'required',
            'persentase' => 'required|numeric',
            'waktu_mulai' => 'required|date',
            'waktu_berakhir' => 'required|date',
        ]);

        $eventPath = null;
        $eventPath2 = null;
        $eventPath3 = null;

        // Upload gambar_event (pertama)
        if ($request->hasFile('gambar_event') && $request->file('gambar_event')->isValid()) {
            $imageName = time() . '_' . Str::random(8) . '.' . $request->gambar_event->extension();
            Uploads::store('gambarevent', $request->gambar_event, $imageName);
            $eventPath = $imageName;
        }

        // Upload gambar_event2 (kedua)
        if ($request->hasFile('gambar_event2') && $request->file('gambar_event2')->isValid()) {
            $imageName = time() . '_' . Str::random(8) . '.' . $request->gambar_event2->extension(); // ✅ benar
            Uploads::store('gambarevent', $request->gambar_event2, $imageName); // ✅ benar
            $eventPath2 = $imageName; // ✅ simpan ke variabel yang benar
        }

        // Upload gambar_event3 (ketiga)
        if ($request->hasFile('gambar_event3') && $request->file('gambar_event3')->isValid()) {
            $imageName = time() . '_' . Str::random(8) . '.' . $request->gambar_event3->extension(); // ✅ benar
            Uploads::store('gambarevent', $request->gambar_event3, $imageName); // ✅ benar
            $eventPath3 = $imageName; // ✅ simpan ke variabel yang benar
        }

        // Siapkan data untuk disimpan
        $data = [
            'sesi_user' => $request->input('sesi_user'),
            'nama_promo' => $request->input('nama_promo'),
            'persentase' => $request->input('persentase'),
            'deskripsi' => $request->input('deskripsi'),
            'waktu_mulai' => $request->input('waktu_mulai'),
            'waktu_berakhir' => $request->input('waktu_berakhir'),
            'status_event' => $request->input('status_event'),
            'gambar_event' => $eventPath,
            'gambar_event2' => $eventPath2,
            'gambar_event3' => $eventPath3,
        ];

        // Simpan ke database
        $this->M_Event->create($data);

        // Set pesan sukses
        Session::flash('pesan_event', 'Data Event Berhasil Ditambahkan!');

        // Redirect
        return redirect()->route('superadmin_data.event');
    }

    public function edit_event($id_promo)
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Edit Data Event',
            'title2' => 'Edit Event',
            'event' => $this->M_Event->find($id_promo),
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.event.v_edit', $data);
    }

    public function update_event(Request $request, $id_promo)
    {
        // Validasi input
        $request->validate([
            'nama_promo' => 'required',
            'persentase' => 'required|numeric',
            'deskripsi' => 'nullable',
            'waktu_mulai' => 'required|date',
            'waktu_berakhir' => 'required|date',
        ]);

        $event = M_Event::findOrFail($id_promo);

        // Fungsi bantu: hapus file lama
        $hapusGambar = function ($namaKolom) {
            Uploads::delete('gambarevent', $namaKolom);
        };

        // Proses upload gambar_event
        if ($request->hasFile('gambar_event')) {
            $hapusGambar($event->gambar_event);
            $imageName = time() . '_' . uniqid() . '.' . $request->gambar_event->extension();
            Uploads::store('gambarevent', $request->gambar_event, $imageName);
            $event->gambar_event = $imageName; // Simpan nama file baru
        }

        // Proses upload gambar_event2
        if ($request->hasFile('gambar_event2')) {
            $hapusGambar($event->gambar_event2);
            $imageName = time() . '_' . uniqid() . '.' . $request->gambar_event2->extension();
            Uploads::store('gambarevent', $request->gambar_event2, $imageName);
            $event->gambar_event2 = $imageName; // Simpan nama file baru
        }

        // Proses upload gambar_event3
        if ($request->hasFile('gambar_event3')) {
            $hapusGambar($event->gambar_event3);
            $imageName = time() . '_' . uniqid() . '.' . $request->gambar_event3->extension();
            Uploads::store('gambarevent', $request->gambar_event3, $imageName);
            $event->gambar_event3 = $imageName; // Simpan nama file baru
        }

        // Update field lainnya
        $event->nama_promo = $request->nama_promo;
        $event->persentase = $request->persentase;
        $event->deskripsi = $request->deskripsi;
        $event->waktu_mulai = $request->waktu_mulai;
        $event->waktu_berakhir = $request->waktu_berakhir;
        $event->status_event = $request->status_event ?? $event->status_event;

        // Simpan semua perubahan ke database
        $event->save();

        Session::flash('pesan_event', 'Data Event Berhasil Diupdate!');
        return redirect()->route('superadmin_data.event');
    }

    public function delete_event($id_promo)
    {
        // Find the event by ID
        $event = M_Event::findOrFail($id_promo);

        // Hapus semua gambar event jika ada
        foreach (['gambar_event', 'gambar_event2', 'gambar_event3'] as $kolomGambar) {
            if (!empty($event->$kolomGambar)) {
                Uploads::delete('gambarevent', $event->$kolomGambar);
            }
        }

        $event->delete();

        Session::flash('pesan_event', 'Data Event Berhasil Dihapus!');
        return redirect()->route('superadmin_data.event');
    }

    public function backup_db()
    {
        $data = [
            'title' => 'Daftar Website Toko',
            'title2' => 'Data Website',
        ];
        return view('superadmin.website.v_backup', $data);
    }

    public function proses_db()
    {
        // Tentukan lokasi penyimpanan file backup  
        $backupPath = storage_path('app/backup');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0777, true); // Membuat folder backup jika belum ada  
        }

        $backupFile = $backupPath . '/backup_' . now()->format('Y-m-d_H-i-s') . '.sql'; // Nama file backup dengan timestamp  

        try {
            // Query untuk mengambil semua tabel  
            $tablesResult = DB::select('SHOW TABLES');

            if (empty($tablesResult)) {
                // Jika gagal mengambil tabel, tampilkan pesan error  
                Session::flash('error', 'Gagal mengambil tabel dari database.');
                return redirect()->route('superadmin_data.backup_db');
            }

            // Mulai menulis file backup  
            $backupData = "-- phpMyAdmin SQL Dump\n";
            $backupData .= "-- version 5.2.0\n";
            $backupData .= "-- https://www.phpmyadmin.net/\n\n";
            $backupData .= "-- Host: " . request()->server('SERVER_ADDR') . "\n";
            $backupData .= "-- Generation Time: " . now()->toDateTimeString() . "\n";
            $backupData .= "-- PHP Version: " . phpversion() . "\n\n";
            $backupData .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $backupData .= "START TRANSACTION;\n";
            $backupData .= "SET time_zone = \"+00:00\";\n\n";
            $backupData .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
            $backupData .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
            $backupData .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
            $backupData .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

            // Menentukan nama database  
            $backupData .= "-- Database: " . DB::getDatabaseName() . "\n\n";

            // Loop untuk mengambil data dari setiap tabel  
            foreach ($tablesResult as $row) {
                // Ambil nama tabel dari hasil query  
                $tableName = current($row);

                // Menambahkan query untuk menghapus tabel jika sudah ada  
                $backupData .= "DROP TABLE IF EXISTS `$tableName`;\n";

                // Menyertakan definisi tabel  
                $createTableResult = DB::select("SHOW CREATE TABLE `$tableName`");
                if ($createTableResult) {
                    $backupData .= $createTableResult[0]->{'Create Table'} . ";\n\n";
                }

                // Menambahkan data tabel  
                $dataResult = DB::table($tableName)->get();
                if ($dataResult->isNotEmpty()) {
                    $columns = (array) $dataResult->first(); // Ambil kolom tabel sebagai array  
                    $backupData .= "INSERT INTO `$tableName` (`" . implode('`, `', array_keys($columns)) . "`) VALUES\n";
                    $rowCount = 0;
                    foreach ($dataResult as $dataRow) {
                        $values = array_map(function ($value) {
                            return DB::getPdo()->quote($value); // Menggunakan quote untuk nilai  
                        }, array_values((array) $dataRow)); // Mengonversi objek ke array  
                        $backupData .= "    (" . implode(", ", $values) . ")";
                        $rowCount++;
                        if ($rowCount < $dataResult->count()) {
                            $backupData .= ",\n";
                        } else {
                            $backupData .= ";\n\n";
                        }
                    }
                }
            }

            // Akhirkan transaksi dan pengaturan SQL  
            $backupData .= "COMMIT;\n";

            // Simpan data ke dalam file backup  
            File::put($backupFile, $backupData);

            // Simpan pesan sukses  
            Session::flash('pesan', 'Backup database berhasil disimpan di folder backup.');
            return redirect()->route('superadmin_data.backup_db');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, tampilkan pesan error  
            Session::flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->route('superadmin_data.backup_db');
        }
    }
}
