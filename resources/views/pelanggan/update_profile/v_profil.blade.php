@extends('layouts.app')

@section('content')

<main class="product-section">
  <div class="container-product">
      <h3 class="text-title">Profil Saya</h3>
        <!-- Profile Picture -->
        @php
            $fotoPelanggan = session('foto_pelanggan');
            $fotoSrc = (!empty($fotoPelanggan) && str_starts_with($fotoPelanggan, 'http'))
                ? $fotoPelanggan
                : asset('fotopelanggan/' . $fotoPelanggan);
        @endphp
        <div class="form-group profile text-center">
            <img src="{{ $fotoSrc }}" class="profile-pic" id="gambar_load">
        </div>

        <!-- Email -->
        <div class="form-group profile">
            <label>Email</label>
            <input name="email" value="{{ session('email') }}" class="form-control profile" placeholder="Masukkan Email" readonly>
        </div>

        <!-- Password -->
        <div class="form-group profile">
            <label>Password</label>
            <input name="password" type="password" id="ShowPass" value="{{ session('password') }}" class="form-control profile" readonly>
            <input type="checkbox" onclick="myFunction()"> Show Password
        </div>

        <!-- Nama Pelanggan -->
        <div class="form-group profile">
            <label>Nama Lengkap</label>
            <input name="nama_pelanggan" value="{{ session('nama_pelanggan') }}" class="form-control profile" readonly>
        </div>

        <!-- Tanggal Lahir -->
        <div class="form-group profile">
            <label>Tanggal Lahir</label>
            @php
                $tanggal = strtotime(session('tanggal_lahir'));
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April',
                    'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];
                $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
            @endphp
            <input type="text" class="form-control profile" value="{{ $formattedTanggal }}" readonly>
        </div>

        <!-- No Telepon -->
        <div class="form-group profile">
            <label>No Telepon</label>
            <input type="number" value="{{ session('no_telpon') }}" class="form-control profile" readonly>
        </div>

        <!-- Terakhir Login -->
        <div class="form-group profile">
            <label>Terakhir Login</label>
            @php
                $tanggal_login = strtotime($dataProfil->last_login);
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April',
                    'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];
                $formattedLogin = date('d', $tanggal_login) . ' ' . $bulan[date('n', $tanggal_login)] . ' ' . date('Y H:i:s', $tanggal_login);
            @endphp
            <input type="text" value="{{ $formattedLogin }}" class="form-control profile" readonly>
        </div>

        <!-- Edit Button -->
        <div>
            <a href="{{ route('pelanggan_data.edit', ['id' => $dataProfil->id_pelanggan]) }}" class="btn btn-editprofil">
                <i class="fa fa-fw fa-edit"></i> Edit Profil
            </a>
        </div>
    </div>
</main>

@endsection
