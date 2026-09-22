<?php

namespace App\Http\Controllers;

use App\Models\M_Ekspedisi;
use Illuminate\Http\Request;
use App\Models\M_User;
use App\Models\M_Pelanggan;
use App\Models\M_Pembayaran;
use Illuminate\Support\Str;  // Pastikan untuk mengimpor kelas Str
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Support\Uploads;

class Auth extends WebsiteController
{
    protected $M_User;
    protected $M_Pelanggan;
    protected $M_Pembayaran;

    public function __construct()
    {
        // ✅ WAJIB: Panggil dulu constructor parent
        parent::__construct();

        $this->M_User = new M_User();
        $this->M_Pelanggan = new M_Pelanggan();
        $this->M_Pembayaran = new M_Pembayaran();
    }

    /**
     * Verifikasi password dengan dukungan legacy:
     * - Password baru tersimpan sebagai hash bcrypt.
     * - Password lama (plaintext) masih diterima, lalu otomatis di-upgrade ke hash.
     */
    protected function verifikasiPassword($inputPassword, $storedPassword)
    {
        if (empty($storedPassword)) {
            return false;
        }

        // Password sudah di-hash (bcrypt dimulai dengan $2y$)
        if (Str::startsWith($storedPassword, ['$2y$', '$2a$', '$2b$'])) {
            return Hash::check($inputPassword, $storedPassword);
        }

        // Legacy plaintext
        return hash_equals($storedPassword, $inputPassword);
    }

    /**
     * Upgrade password plaintext menjadi hash (dipanggil setelah login legacy berhasil).
     */
    protected function upgradePassword(&$user, $plainPassword)
    {
        if (!Str::startsWith($user->password, ['$2y$', '$2a$', '$2b$'])) {
            $user->update(['password' => Hash::make($plainPassword)]);
            $user->password = $user->fresh()->password;
        }
    }

    public function register_superadmin()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Register',
            'title2' => 'Register',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('superadmin.auth.register', $data);
    }

    public function save_superadmin(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'email_user' => 'nullable|email|max:255',
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
                'sesi_user' => $request->fullname,
                'username' => $request->username,
                'fullname' => $request->fullname,
                'email_user' => $request->email_user,
                'nama_title' => $request->nama_title,
                'password' => Hash::make($request->password),
                'level' => $request->level,  // Mengambil nilai level dari dropdown
                'foto_user' => $fileName,  // Store the file name in the database
            ]);

            // Redirect with success message
            return redirect()->route('auth.login_superadmin')->with('pesan_success', 'User berhasil ditambahkan.');
        } else {
            return redirect()->route('auth.register_superadmin')->with('error', 'File upload failed.');
        }
    }

    // User methods
    public function login_superadmin()
    {
        // No need to send all users to the view. We can just load the login page.
        $data = [
            'title' => 'Login User',
        ];
        return view('superadmin.auth.login', $data);
    }

    public function cek_login_superadmin(Request $request)
    {
        // Validasi form inputs
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Cek apakah username ada
        $user = M_User::where('username', $username)->first();

        if ($user) {
            // Cek kecocokan password (mendukung hash & legacy plaintext)
            if ($this->verifikasiPassword($password, $user->password)) {
                $this->upgradePassword($user, $password);

                // Set session level
                session(['level' => $user->level]);
                session()->put('id_user', $user->id_user);
                session()->put('username', $user->username);
                session()->put('fullname', $user->fullname);
                session()->put('sesi_user', $user->sesi_user);
                session()->put('nama_title', $user->nama_title);
                session()->put('foto_user', $user->foto_user);
                session()->put('last_login', $user->last_login);
                session()->put('logged_in_at', now()); // Penanda waktu login untuk auto logout

                // Update timestamp last login
                $user->update(['last_login' => now()]);

                // Flash message
                session()->flash('pesan_welcome', 'Selamat Datang, ' . $user->fullname . '!');

                // Redirect sesuai level user
                switch ($user->level) {
                    case 'superadmin':
                        return redirect()->route('home_superadmin');
                    default:
                        return redirect()->route('auth.login_superadmin');
                }
            } else {
                // Password salah
                session()->flash('pesan_warning', 'Login Gagal, Password Salah!');
                return redirect()->route('auth.login_superadmin');
            }
        } else {
            // Username tidak ditemukan
            session()->flash('pesan_warning', 'Login Gagal, Username Tidak Ditemukan!');
            return redirect()->route('auth.login_superadmin');
        }
    }

    public function logout_superadmin()
    {
        // Hapus semua session
        session()->flush();

        // Flash pesan sukses
        session()->flash('pesan_success', 'Logout Berhasil!');

        return redirect()->route('auth.login_superadmin');
    }

    public function register_user()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Register',
            'title2' => 'Register',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('auth.register', $data);
    }

    public function save_user(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'email_user' => 'nullable|email|max:255',
            'password' => 'required|string|max:255',
            'level' => 'required|max:100', // Pastikan level diterima
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'nama_toko' => 'required|string|max:100',
            'wa_pusat' => 'nullable|string|max:20',
            'alamat_pusat' => 'nullable|string|max:1000',
        ]);

        // Cek nama lapak/toko agar unik
        if (DB::table('website')->where('nama_toko', $request->nama_toko)->exists()) {
            return redirect()->route('auth.register_user')
                ->withInput()
                ->withErrors(['nama_toko' => 'Nama lapak/toko sudah dipakai, gunakan nama lain.']);
        }

        // Handle the file upload (opsional)
        $fotoUser = null;
        if ($request->hasFile('foto_user') && $request->file('foto_user')->isValid()) {
            $file = $request->file('foto_user');

            // Generate a random file name using Str::random() and the file's extension
            $fileExtension = $file->getClientOriginalExtension();  // Get the original file extension
            $fileName = Str::random(20) . '.' . $fileExtension;  // Generate random name and add the extension

            // Move the file to the 'fotouser' directory
            Uploads::store('fotouser', $file, $fileName);

            $fotoUser = $fileName;
        }

        // Store the user data into the database
        $user = M_User::create([
            'sesi_user' => $request->fullname,
            'username' => $request->username,
            'fullname' => $request->fullname,
            'email_user' => $request->email_user,
            'nama_title' => $request->nama_title,
            'password' => Hash::make($request->password),
            'level' => $request->level,  // Mengambil nilai level dari dropdown
            'foto_user' => $fotoUser,  // Store the file name in the database
        ]);

        // Otomatis buat data lapak (website) untuk penjual baru
        if ($user && $request->filled('nama_toko')) {
            DB::table('website')->insert([
                'id_user' => $user->id_user,
                'sesi_user' => $user->sesi_user,
                'level' => 'pemilik',
                'nama_toko' => $request->nama_toko,
                'wa_pusat' => $request->wa_pusat,
                'alamat_pusat' => $request->alamat_pusat,
                'status_website' => 'Aktif',
            ]);

            // Otomatis buat kurir toko default untuk lapak baru
            DB::table('kurir')->insert([
                'sesi_user' => $user->sesi_user,
                'jenis_kurir' => 'Kurir Internal',
                'tipe_kurir' => 'instan',
                'ongkir' => 1700,
                'deleted_at' => 0,
            ]);
        }

        // Redirect with success message
        return redirect()->route('auth.login_user')->with('pesan_success', 'User berhasil ditambahkan.');
    }

    // User methods
    public function login_user()
    {
        // No need to send all users to the view. We can just load the login page.
        $data = [
            'title' => 'Login User',
        ];
        return view('auth.login', $data);
    }

    public function cek_login(Request $request)
    {
        // Validasi form inputs
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Cek apakah username ada
        $user = M_User::where('username', $username)->first();

        if ($user) {
            // Cek kecocokan password (mendukung hash & legacy plaintext)
            if ($this->verifikasiPassword($password, $user->password)) {
                $this->upgradePassword($user, $password);

                // Set session level
                session(['level' => $user->level]);
                session()->put('id_user', $user->id_user);
                session()->put('username', $user->username);
                session()->put('fullname', $user->fullname);
                session()->put('sesi_user', $user->sesi_user);
                session()->put('nama_title', $user->nama_title);
                session()->put('foto_user', $user->foto_user);
                session()->put('last_login', $user->last_login);
                session()->put('logged_in_at', now()); // Penanda waktu login untuk auto logout

                // Update timestamp last login
                $user->update(['last_login' => now()]);

                // Flash message
                session()->flash('pesan_welcome', 'Selamat Datang, ' . $user->fullname . '!');

                // Redirect sesuai level user
                switch ($user->level) {
                    case 'pemilik':
                        return redirect()->route('home_pemilik');
                    case 'admin':
                        return redirect()->route('home_admin');
                    default:
                        return redirect()->route('auth.login_user');
                }
            } else {
                // Password salah
                session()->flash('pesan_warning', 'Login Gagal, Password Salah!');
                return redirect()->route('auth.login_user');
            }
        } else {
            // Username tidak ditemukan
            session()->flash('pesan_warning', 'Login Gagal, Username Tidak Ditemukan!');
            return redirect()->route('auth.login_user');
        }
    }

    public function logout_user()
    {
        // Hapus semua session
        session()->flush();

        // Flash pesan sukses
        session()->flash('pesan_success', 'Logout Berhasil!');

        return redirect()->route('auth.login_user');
    }

    // Register View
    public function register_pelanggan()
    {
        // Ambil sesi user dari session Laravel
        $sesiUser = session('sesi_user');
        $M_User = new M_User();
        $dataUser = $M_User->getUserById($sesiUser);

        $data = [
            'title' => 'Register',
            'title2' => 'Register',
            'sesi_user' => $dataUser ? $dataUser->sesi_user : 'Nama Lengkap Tidak Ditemukan',
        ];
        return view('pelanggan.auth_login.v_register', $data);
    }

    // Save Register
    public function save_pelanggan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:pelanggan,email',
            'password' => 'required',
            'nama_pelanggan' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'no_telpon' => 'required',
            'alamat' => 'required',
            'foto_pelanggan' => 'required|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Handle the file upload
        if ($request->hasFile('foto_pelanggan') && $request->file('foto_pelanggan')->isValid()) {
            // Get the uploaded file
            $file = $request->file('foto_pelanggan');

            // Generate a random file name using Str::random() and the file's extension
            $fileExtension = $file->getClientOriginalExtension();  // Get the original file extension
            $fileName = Str::random(20) . '.' . $fileExtension;  // Generate random name and add the extension

            // Move the file to the 'fotopelanggan' directory
            Uploads::store('fotopelanggan', $file, $fileName);

            M_Pelanggan::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nama_pelanggan' => $request->nama_pelanggan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'level' => 'pelanggan',
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_telpon' => $request->no_telpon,
                'alamat' => $request->alamat,
                'foto_pelanggan' => $fileName,
                'last_login' => now(),
            ]);

            Session::flash('pesan_success', 'Data Pelanggan Berhasil Ditambahkan!');
            return redirect()->route('auth.login_pelanggan');
        }

        return Redirect::back()->with('error', 'File upload failed.');
    }

    // Login View
    public function login_pelanggan()
    {
        $data = [
            'title' => 'Login Pelanggan',
            'title2' => 'Login Pelanggan',
        ];
        return view('pelanggan.auth_login.v_login', $data);
    }

    public function cek_login_pelanggan(Request $request)
    {
        // Validasi form inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Cek apakah email ada
        $user = M_Pelanggan::where('email', $email)->first();

        if ($user) {
            // Cek kecocokan password (mendukung hash & legacy plaintext)
            if ($this->verifikasiPassword($password, $user->password)) {
                $this->upgradePassword($user, $password);

                // Set session level
                session(['level' => $user->level ?? 'pelanggan']);
                // Simpan session pelanggan
                session()->put('user_logged_in', true);
                session()->put('id_pelanggan', $user->id_pelanggan);
                session()->put('tanggal_lahir', $user->tanggal_lahir);
                session()->put('no_telpon', $user->no_telpon);
                session()->put('nama_pelanggan', $user->nama_pelanggan);
                session()->put('email', $user->email);
                session()->put('latitude', $user->latitude);
                session()->put('longitude', $user->longitude);
                session()->put('alamat', $user->alamat);
                session()->put('kode_kota', $user->kode_kota);
                session()->put('nama_kota', $user->nama_kota);
                session()->put('foto_pelanggan', $user->foto_pelanggan);
                session()->put('last_login', $user->last_login);
                session()->put('logged_in_at', now()); // Penanda waktu login untuk auto logout

                // 🔁 1. Lakukan pembaruan batalkan transaksi terlebih dahulu
                M_Pembayaran::batalkanTransaksiExpiredByUser($user->id_pelanggan);

                // 🔁 2. Lakukan pembaruan status OTOMATIS terlebih dahulu
                M_Ekspedisi::updateStatusOtomatis($user->id_pelanggan);

                // Update timestamp last login
                $user->update(['last_login' => now()]);

                // Flash message
                session()->flash('pesan_welcome', 'Selamat Datang, ' . $user->nama_pelanggan . '!');

                // Redirect sesuai level user
                switch ($user->level) {
                    case 'pelanggan':
                        return redirect()->route('home_toko.index');
                    default:
                        return redirect()->route('auth.login_pelanggan');
                }
            } else {
                // Password salah
                session()->flash('pesan_warning', 'Login Gagal, Password Salah!');
                return redirect()->route('auth.login_pelanggan');
            }
        } else {
            // Username tidak ditemukan
            session()->flash('pesan_warning', 'Login Gagal, Email Tidak Ditemukan!');
            return redirect()->route('auth.login_pelanggan');
        }
    }

    // Redirect ke Google OAuth
    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback dari Google
    public function callbackGoogle()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            session()->flash('pesan_warning', 'Login Google gagal: ' . $e->getMessage());
            return redirect()->route('auth.login_pelanggan');
        }

        // Cari pelanggan berdasarkan email
        $user = M_Pelanggan::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Buat akun baru jika belum terdaftar
            // Password sengaja dikosongkan (bukan random) agar bisa diisi manual via Edit Profil
            $user = M_Pelanggan::create([
                'email' => $googleUser->getEmail(),
                'password' => '',
                'nama_pelanggan' => $googleUser->getName() ?: $googleUser->getEmail(),
                'jenis_kelamin' => 'L',
                'level' => 'pelanggan',
                'tanggal_lahir' => now()->toDateString(),
                'no_telpon' => '',
                'alamat' => '',
                'foto_pelanggan' => $this->simpanFotoGoogle($googleUser->getAvatar()),
                'last_login' => now(),
            ]);
        }

        // Set session pelanggan
        session(['level' => $user->level ?? 'pelanggan']);
        session()->put('user_logged_in', true);
        session()->put('id_pelanggan', $user->id_pelanggan);
        session()->put('tanggal_lahir', $user->tanggal_lahir);
        session()->put('no_telpon', $user->no_telpon);
        session()->put('nama_pelanggan', $user->nama_pelanggan);
        session()->put('email', $user->email);
        session()->put('latitude', $user->latitude);
        session()->put('longitude', $user->longitude);
        session()->put('alamat', $user->alamat);
        session()->put('kode_kota', $user->kode_kota);
        session()->put('nama_kota', $user->nama_kota);
        session()->put('foto_pelanggan', $user->foto_pelanggan);
        session()->put('last_login', $user->last_login);
        session()->put('logged_in_at', now()); // Penanda waktu login untuk auto logout

        // Pembaruan transaksi & status otomatis
        M_Pembayaran::batalkanTransaksiExpiredByUser($user->id_pelanggan);
        M_Ekspedisi::updateStatusOtomatis($user->id_pelanggan);

        $user->update(['last_login' => now()]);

        session()->flash('pesan_welcome', 'Selamat Datang, ' . $user->nama_pelanggan . '!');
        return redirect()->route('home_toko.index');
    }

    // Unduh avatar Google & simpan sebagai file lokal di folder fotopelanggan
    private function simpanFotoGoogle($url)
    {
        if (empty($url)) {
            return '';
        }

        $contents = false;
        try {
            $client = new \GuzzleHttp\Client(['verify' => false, 'timeout' => 15]);
            $contents = (string) $client->get($url)->getBody();
        } catch (\Exception $e) {
            $contents = @file_get_contents($url);
        }

        if ($contents === false || empty($contents)) {
            return '';
        }

        $fileName = 'google_' . Str::random(20) . '.jpg';
        Uploads::put('fotopelanggan', $fileName, $contents);

        return $fileName;
    }

    // Lupa Password View
    public function lupa_password()
    {
        $data = [
            'title' => 'Lupa Password',
            'title2' => 'Lupa Password',
        ];
        return view('pelanggan.auth_login.v_lupa_password', $data);
    }

    // Cek Email + kirim link reset berbasis token
    public function cek_proses(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = M_Pelanggan::where('email', $request->email)->first();

        // Respons generik agar tidak membocorkan keberadaan akun
        if (!$user || empty($user->email)) {
            return back()->with('pesan', 'Jika email terdaftar, tautan reset password telah dikirim.');
        }

        // Simpan token reset (berlaku 60 menit)
        $token = Str::random(64);

        DB::table('reset_password_tokens')->where('email', $user->email)->delete();
        DB::table('reset_password_tokens')->insert([
            'email' => $user->email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $link = route('auth.reset_password', ['token' => $token]);

        try {
            Mail::raw(
                "Halo {$user->nama_pelanggan},\n\n"
                . "Kami menerima permintaan reset password untuk akun Anda.\n"
                . "Klik tautan berikut untuk mengatur password baru (berlaku 60 menit):\n\n"
                . $link . "\n\n"
                . "Jika Anda tidak meminta reset password, abaikan email ini.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Reset Password - ' . config('app.name', 'Toko Online'));
                }
            );
            $terkirim = true;
        } catch (\Throwable $e) {
            report($e);
            $terkirim = false;
        }

        // Pada mode development (MAIL_MAILER=log), tampilkan link agar tetap bisa diuji
        if (!$terkirim || config('mail.default') === 'log') {
            return back()->with('pesan', 'Email reset dikirim.')
                ->with('dev_reset_link', $link);
        }

        return back()->with('pesan', 'Jika email terdaftar, tautan reset password telah dikirim.');
    }

    // Reset Password View (validasi token)
    public function reset_password($token)
    {
        $row = DB::table('reset_password_tokens')
            ->where('token', hash('sha256', $token))
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$row) {
            return redirect()->route('auth.lupa_password')
                ->with('pesan_warning', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        $user = M_Pelanggan::where('email', $row->email)->first();

        $data = [
            'title' => 'Reset Password',
            'title2' => 'Reset Password',
            'token' => $token,
            'user' => $user,
        ];

        return view('pelanggan.auth_login.v_reset_password', $data);
    }

    public function ganti_password(Request $request, $token)
    {
        $request->validate([
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $row = DB::table('reset_password_tokens')
            ->where('token', hash('sha256', $token))
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$row) {
            return redirect()->route('auth.lupa_password')
                ->with('pesan_warning', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        $pelanggan = M_Pelanggan::where('email', $row->email)->first();

        if (!$pelanggan) {
            return redirect()->route('auth.lupa_password')->with('pesan_warning', 'Akun tidak ditemukan.');
        }

        // Simpan password ter-hash
        $pelanggan->password = Hash::make($request->new_password);
        $pelanggan->save();

        // Token sekali pakai
        DB::table('reset_password_tokens')->where('email', $row->email)->delete();

        return redirect()->route('auth.login_pelanggan')
            ->with('pesan_success', 'Password berhasil diperbarui. Silakan login.');
    }

    // ============================================================
    // FORGOT PASSWORD STAFF (superadmin / pemilik / admin)
    // ============================================================

    // Lupa Password View (staff)
    public function lupa_password_user()
    {
        $data = [
            'title' => 'Lupa Password',
            'title2' => 'Lupa Password',
        ];
        return view('auth.v_lupa_password_user', $data);
    }

    // Cek username/email + kirim link reset berbasis token (staff)
    public function cek_proses_user(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $username = trim($request->input('username'));

        // Boleh cari berdasarkan username ATAU email_user
        $user = M_User::where('username', $username)
            ->orWhere('email_user', $username)
            ->first();

        // Respons generik agar tidak membocorkan keberadaan akun
        if (!$user) {
            return back()->with('pesan', 'Jika username terdaftar, tautan reset password telah dikirim.');
        }

        // Simpan token reset (berlaku 60 menit)
        $token = Str::random(64);

        // Kunci penyimpanan: sesi key dari email_user (fallback: staff:username)
        $tokenKey = $user->email_user ?: ('staff:' . $user->username);

        DB::table('reset_password_tokens')->where('email', $tokenKey)->delete();
        DB::table('reset_password_tokens')->insert([
            'email' => $tokenKey,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $link = route('auth.reset_password_user', ['token' => $token]);

        try {
            Mail::raw(
                "Halo {$user->fullname},\n\n"
                . "Kami menerima permintaan reset password untuk akun staff Anda.\n"
                . "Klik tautan berikut untuk mengatur password baru (berlaku 60 menit):\n\n"
                . $link . "\n\n"
                . "Jika Anda tidak meminta reset password, abaikan email ini.",
                function ($message) use ($user) {
                    $message->to($user->email_user ?: config('mail.from.address', 'noreply@localhost'))
                        ->subject('Reset Password - ' . config('app.name', 'Toko Online'));
                }
            );
            $terkirim = true;
        } catch (\Throwable $e) {
            report($e);
            $terkirim = false;
        }

        // Pada mode development (MAIL_MAILER=log), tampilkan link agar tetap bisa diuji
        if (!$terkirim || config('mail.default') === 'log') {
            return back()->with('pesan', 'Email reset dikirim.')
                ->with('dev_reset_link', $link);
        }

        return back()->with('pesan', 'Jika username terdaftar, tautan reset password telah dikirim.');
    }

    // Reset Password View (validasi token) - staff
    public function reset_password_user($token)
    {
        $row = DB::table('reset_password_tokens')
            ->where('token', hash('sha256', $token))
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$row) {
            return redirect()->route('auth.lupa_password_user')
                ->with('pesan_warning', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        $user = $this->staffByTokenKey($row->email);

        if (!$user) {
            return redirect()->route('auth.lupa_password_user')
                ->with('pesan_warning', 'Akun tidak ditemukan. Silakan minta tautan baru.');
        }

        $data = [
            'title' => 'Reset Password',
            'title2' => 'Reset Password',
            'token' => $token,
            'user' => $user,
        ];

        return view('auth.v_reset_password_user', $data);
    }

    public function ganti_password_user(Request $request, $token)
    {
        $request->validate([
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $row = DB::table('reset_password_tokens')
            ->where('token', hash('sha256', $token))
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$row) {
            return redirect()->route('auth.lupa_password_user')
                ->with('pesan_warning', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        $user = $this->staffByTokenKey($row->email);

        if (!$user) {
            return redirect()->route('auth.lupa_password_user')->with('pesan_warning', 'Akun tidak ditemukan.');
        }

        // Simpan password ter-hash (mengikuti pola registrasi staff yang sudah ada)
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Token sekali pakai
        DB::table('reset_password_tokens')->where('email', $row->email)->delete();

        return redirect()->route('auth.login_user')
            ->with('pesan_success', 'Password berhasil diperbarui. Silakan login.');
    }

    // Cari staff berdasarkan key token (email_user atau staff:username)
    private function staffByTokenKey($tokenKey)
    {
        if (Str::startsWith($tokenKey, 'staff:')) {
            return M_User::where('username', substr($tokenKey, 6))->first();
        }

        return M_User::where('email_user', $tokenKey)->first();
    }

    // Logout
    public function logout_pelanggan()
    {
        Session::forget(['user_logged_in', 'email', 'id_pelanggan', 'level', 'foto_pelanggan']);

        Session::flash('pesan_logout', 'Logout Berhasil!');
        return redirect()->route('home_toko.index');
    }
}
