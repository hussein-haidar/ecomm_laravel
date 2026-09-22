@extends('layouts.app')
@section('content')
<div class="container pt-4 pt-lg-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 col-xl-6">
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
                        <h3 class="fw-bold mb-1">Buka Lapak Jualan Baru</h3>
                        <p class="text-muted">Daftar sebagai Penjual dan mulai jualan produk Anda</p>
                    </div>

                    {{-- Alerts --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                    @if(session('pesan_warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('pesan_warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                    @if(session('pesan_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('pesan_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                    @if(session('pesan'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('pesan') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    {{-- Register Form --}}
                    <form action="{{ route('auth.save_user') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="level" value="pemilik">

                        {{-- Row 1: Username & Password --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Nama untuk login" value="{{ old('username') }}" required autocomplete="username">
                                </div>
                                @error('username')
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

                        {{-- Row 2: Nama Lengkap & Email --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="fullname" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="fullname" id="fullname" class="form-control @error('fullname') is-invalid @enderror" placeholder="Nama pemilik lapak" value="{{ old('fullname') }}" required autocomplete="name">
                                </div>
                                @error('fullname')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email_user" class="form-label fw-medium">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email_user" id="email_user" class="form-control @error('email_user') is-invalid @enderror" placeholder="nama@email.com (opsional)" value="{{ old('email_user') }}" autocomplete="email">
                                </div>
                                @error('email_user')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 3: Nama Lapak & No HP/WhatsApp --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nama_toko" class="form-label fw-medium">Nama Lapak / Toko <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-store"></i></span>
                                    <input type="text" name="nama_toko" id="nama_toko" class="form-control @error('nama_toko') is-invalid @enderror" placeholder="contoh: Toko Berkah" value="{{ old('nama_toko') }}" required>
                                </div>
                                @error('nama_toko')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama ini tampil sebagai identitas lapak jualan Anda.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="wa_pusat" class="form-label fw-medium">No HP / WhatsApp Lapak</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" name="wa_pusat" id="wa_pusat" class="form-control @error('wa_pusat') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('wa_pusat') }}" pattern="[0-9+]+" inputmode="tel">
                                </div>
                                @error('wa_pusat')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Row 4: Alamat Lapak --}}
                        <div class="mb-3">
                            <label for="alamat_pusat" class="form-label fw-medium">Alamat Lapak</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="alamat_pusat" id="alamat_pusat" class="form-control @error('alamat_pusat') is-invalid @enderror" rows="2" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('alamat_pusat') }}</textarea>
                            </div>
                            @error('alamat_pusat')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Row 5: Title Dashboard & Foto Profil --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nama_title" class="form-label fw-medium">Title Dashboard</label>
                                <input type="text" name="nama_title" id="nama_title" class="form-control" placeholder="contoh: Toko Berkah (opsional)" value="{{ old('nama_title') }}">
                                <div class="form-text">Label yang ditampilkan di dashboard penjual.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Foto Profil</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="gambar_load" src="{{ asset('assets/images/logo.png') }}" alt="Preview Foto" class="rounded border" style="width: 64px; height: 64px; object-fit: cover; flex-shrink: 0;">
                                    <div class="flex-grow-1">
                                        <input type="file" name="foto_user" id="preview_gambar" class="form-control @error('foto_user') is-invalid @enderror" accept="image/png,image/jpg,image/jpeg" >
                                        @error('foto_user')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">PNG/JPG/JPEG, maks 1MB. Opsional.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-semibold rounded-pill fs-6">
                                <i class="fas fa-store me-2"></i>Daftar & Buka Lapak
                            </button>
                            <a href="{{ route('auth.login_user') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i>Sudah punya akun penjual? Login
                            </a>
                        </div>

                        <div class="text-center mt-3">
                            <p class="text-muted mb-0 small">Bukan penjual? <a href="{{ route('auth.login_pelanggan') }}" class="fw-semibold text-decoration-none">Kembali ke halaman masuk</a></p>
                        </div>
                    </form>
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

        // Auto-focus username
        document.getElementById('username')?.focus();
    });
</script>
@endpush