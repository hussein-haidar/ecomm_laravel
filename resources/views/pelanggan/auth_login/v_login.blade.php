@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
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
                        <h3 class="fw-bold mb-1">Selamat Datang Kembali</h3>
                        <p class="text-muted">Masukkan email & password untuk melanjutkan</p>
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

                    {{-- Login Form --}}
                    <form action="{{ route('auth.cek_login_pelanggan') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Masukkan password" required autocomplete="current-password">
                                <button type="button" class="input-group-text bg-transparent border-start-0" onclick="togglePassword('password', this)" aria-label="Tampilkan/sembunyikan password">
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
                                <label class="form-check-label small" for="remember">Ingat saya</label>
                            </div>
                            <a href="{{ route('auth.lupa_password') }}" class="small text-decoration-none">Lupa Password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold rounded-pill fs-6">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="row text-center my-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <hr class="flex-grow-1">
                                <span class="px-3 text-muted small">ATAU</span>
                                <hr class="flex-grow-1">
                            </div>
                        </div>
                    </div>

                    {{-- Google Login --}}
                    <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-danger w-100 py-3 d-flex align-items-center justify-content-center gap-2 rounded-pill">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>

                    {{-- Register Link --}}
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Belum punya akun? <a href="{{ route('auth.register_pelanggan') }}" class="fw-semibold text-decoration-none">Daftar Sekarang</a></p>
                    </div>
                    <div class="text-center mt-2">
                        <p class="text-muted mb-0 small">Ingin buka toko? <a href="{{ route('auth.register_user') }}" class="fw-semibold text-decoration-none">Daftar sebagai Penjual</a></p>
                    </div>
                </div>
            </div>

            {{-- Features --}}
            <div class="row text-center mt-4 g-3">
                <div class="col-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <small class="fw-medium d-block">Transaksi Aman</small>
                    </div>
                </div>
                <div class="col-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <small class="fw-medium d-block">Pengiriman Cepat</small>
                    </div>
                </div>
                <div class="col-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-rotate-left"></i>
                        </div>
                        <small class="fw-medium d-block">Retur Mudah</small>
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

        // Auto-focus email input
        document.getElementById('email')?.focus();
    });
</script>
@endpush