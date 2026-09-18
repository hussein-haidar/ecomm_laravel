@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg" data-aos="fade-up">
                <div class="card-body p-4 p-lg-5">
                    {{-- Header --}}
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                            @if($dataWebsite['logo_website'])
                                <img src="{{ asset('logo_website/' . $dataWebsite['logo_website']) }}" alt="{{ $dataWebsite['nama_toko'] }}" width="40" height="40" class="rounded-circle">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                            <span class="fw-bold fs-4">{{ $dataWebsite['nama_toko'] }}</span>
                        </a>
                        <h3 class="fw-bold mb-1">Buat Akun Baru</h3>
                        <p class="text-muted">Daftar sekarang untuk mulai belanja & nikmati promo eksklusif</p>
                    </div>

                    {{-- Alerts --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('pesan_warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('pesan_warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('pesan_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('pesan_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('pesan'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('pesan') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Register Form --}}
                    <form action="{{ route('auth.save_pelanggan') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- Row 1: Email & Password --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required autocomplete="new-password" minlength="6">
                                    <button type="button" class="input-group-text bg-transparent border-start-0" onclick="togglePassword('password', this)" aria-label="Tampilkan/sembunyikan password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 2: Nama & Jenis Kelamin --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nama_pelanggan" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" placeholder="Nama lengkap" value="{{ old('nama_pelanggan') }}" required autocomplete="name">
                                </div>
                                @error('nama_pelanggan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_kelamin" class="form-label fw-medium">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 3: Tanggal Lahir & No Telepon --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label fw-medium">Tanggal Lahir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" required max="{{ date('Y-m-d', strtotime('-13 years')) }}" value="{{ old('tanggal_lahir') }}">
                                </div>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="no_telpon" class="form-label fw-medium">No Telepon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" name="no_telpon" id="no_telpon" class="form-control @error('no_telpon') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('no_telpon') }}" required autocomplete="tel" pattern="[0-9]+" inputmode="numeric">
                                </div>
                                @error('no_telpon')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 4: Alamat --}}
                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-medium">Alamat Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos" required autocomplete="street-address">{{ old('alamat') }}</textarea>
                            </div>
                            @error('alamat')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Alamat lengkap untuk pengiriman & perhitungan ongkir otomatis.</div>
                        </div>

                        {{-- Row 5: Foto Profil --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Foto Profil <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img id="gambar_load" src="{{ asset('assets/images/logo.png') }}" alt="Preview Foto" class="rounded border" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="foto_pelanggan" id="preview_gambar" class="form-control @error('foto_pelanggan') is-invalid @enderror" accept="image/png,image/jpg,image/jpeg" required>
                                    @error('foto_pelanggan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: PNG/JPG/JPEG, Maksimal 1MB. Foto akan ditampilkan di profil & ulasan.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-semibold rounded-pill fs-6">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </button>
                            <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i>Sudah punya akun? Login
                            </a>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <div class="text-center my-4">
                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="px-3 text-muted small">ATAU</span>
                            <hr class="flex-grow-1">
                        </div>
                    </div>

                    {{-- Google Register --}}
                    <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2 rounded-pill">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Daftar dengan Google</span>
                    </a>

                    {{-- Seller Link --}}
                    <div class="text-center mt-3">
                        <p class="text-muted mb-0 small">Ingin jualan? <a href="{{ route('auth.register_user') }}" class="fw-semibold text-decoration-none">Daftar sebagai Penjual</a></p>
                    </div>
                </div>
            </div>

            {{-- Benefits --}}
            <div class="row text-center mt-4 g-3">
                <div class="col-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-gift"></i>
                        </div>
                        <small class="fw-medium d-block">Promo Eksklusif</small>
                        <small class="text-muted">Dapatkan diskon member</small>
                    </div>
                </div>
                <div class="col-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <small class="fw-medium d-block">Notifikasi Real-time</small>
                        <small class="text-muted">Status pesan via WA/Email</small>
                    </div>
                </div>
                <div class="col-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <small class="fw-medium d-block">Wishlist</small>
                        <small class="text-muted">Simpan produk favorit</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        window.togglePassword = function(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        };

        // Image preview
        document.getElementById('preview_gambar')?.addEventListener('change', function() {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('gambar_load').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Set max date for tanggal_lahir (min 13 years old)
        const today = new Date();
        const minAgeDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
        document.getElementById('tanggal_lahir').max = minAgeDate.toISOString().split('T')[0];

        // Auto-focus email
        document.getElementById('email')?.focus();
    });
</script>
@endpush