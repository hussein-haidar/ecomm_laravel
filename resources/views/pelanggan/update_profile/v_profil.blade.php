@extends('layouts.app')
@push('scripts')
<script>
    window.togglePass = function(inputId, btn) {
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
</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-user me-2 text-primary"></i>Profil Saya</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    {{-- Profile Picture --}}
                    @php
                        $fotoPelanggan = session('foto_pelanggan');
                        $fotoSrc = (!empty($fotoPelanggan) && str_starts_with($fotoPelanggan, 'http'))
                            ? $fotoPelanggan
                            : asset('fotopelanggan/' . $fotoPelanggan);
                    @endphp
                    <div class="text-center mb-4">
                        <img src="{{ $fotoSrc }}" class="rounded-circle border border-3 shadow-sm" 
                             id="gambar_load" style="width: 120px; height: 120px; object-fit: cover;" 
                             alt="Foto Profil {{ session('nama_pelanggan') }}">
                    </div>

                    <div class="row g-3">
                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" class="form-control" value="{{ session('email') }}" readonly>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <input type="password" id="ShowPassProfil" value="{{ session('password') ?: '••••••••' }}" class="form-control" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('ShowPassProfil', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ session('nama_pelanggan') }}" readonly>
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tanggal Lahir</label>
                            @php
                                $tanggal = strtotime(session('tanggal_lahir'));
                                $bulan = [
                                    1 => 'Januari', 'Februari', 'Maret', 'April',
                                    'Mei', 'Juni', 'Juli', 'Agustus',
                                    'September', 'Oktober', 'November', 'Desember'
                                ];
                                $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
                            @endphp
                            <input type="text" class="form-control" value="{{ $formattedTanggal }}" readonly>
                        </div>

                        {{-- No Telepon --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">No Telepon</label>
                            <input type="tel" class="form-control" value="{{ session('no_telpon') }}" readonly>
                        </div>

                        {{-- Terakhir Login --}}
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Terakhir Login</label>
                            @php
                                $tanggal_login = strtotime($dataProfil->last_login);
                                $bulan = [
                                    1 => 'Januari', 'Februari', 'Maret', 'April',
                                    'Mei', 'Juni', 'Juli', 'Agustus',
                                    'September', 'Oktober', 'November', 'Desember'
                                ];
                                $formattedLogin = date('d', $tanggal_login) . ' ' . $bulan[date('n', $tanggal_login)] . ' ' . date('Y H:i:s', $tanggal_login);
                            @endphp
                            <input type="text" class="form-control" value="{{ $formattedLogin }}" readonly>
                        </div>
                    </div>

                    {{-- Edit Button --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('pelanggan_data.edit', ['id' => $dataProfil->id_pelanggan]) }}" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection