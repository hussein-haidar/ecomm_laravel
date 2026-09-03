@extends('layouts.app')

@section('content')

<main class="product-section">
    <div class="container my-4">
        <h3>Profil Saya</h3>

        @php
            $foto = session('foto_pelanggan');
            $isGoogle = !empty($foto) && filter_var($foto, FILTER_VALIDATE_URL);
            $isDefaultTgl = in_array(session('tanggal_lahir'), ['0000-00-00', '2000-01-01', '', null]);
            $isEmptyNoTelp = empty(session('no_telpon'));
            $isEmptyPass = empty(session('password'));
            $isEmptyKota = empty(session('nama_kota')) && empty($dataProfil['nama_kota'] ?? '');
        @endphp

        @if ($isGoogle || $isDefaultTgl || $isEmptyNoTelp || $isEmptyPass || $isEmptyKota)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Lengkapi data berikut agar akun Anda sempurna:</strong>
                <ul style="margin-bottom: 0; margin-top: 8px;">
                    @if ($isEmptyPass)<li>Password (untuk login manual)</li>@endif
                    @if ($isEmptyNoTelp)<li>No Telepon</li>@endif
                    @if ($isDefaultTgl)<li>Tanggal Lahir</li>@endif
                    @if ($isEmptyKota)<li>Kota Asal (untuk ongkir)</li>@endif
                </ul>
            </div>
        @endif

        <form action="{{ route('pelanggan_data.update_profile', ['id' => $dataProfil['id_pelanggan']]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="sesi_user" value="{{ session('sesi_user') ?? '' }}" class="form-control" readonly>

            <div class="form-group profile">
                <label>Email</label>
                <input type="email" name="email" value="{{ session('email') }}" class="form-control profile" placeholder="Masukkan Email" readonly>
            </div>

            <div class="form-group profile">
                <label>Kata Sandi @if ($isEmptyPass)<span class="text-danger">*</span>@endif</label>
                <input name="password" type="text" value="{{ session('password') }}" class="form-control profile"
                    placeholder="{{ $isEmptyPass ? 'Wajib diisi untuk login manual' : '' }}"
                    @if ($isEmptyPass) style="border-color: #dd4b39;" @endif>
            </div>

            <div class="form-group profile">
                <label>Nama Lengkap</label>
                <input name="nama_pelanggan" value="{{ session('nama_pelanggan') }}" class="form-control profile" placeholder="Masukkan Nama Lengkap" required>
            </div>

            <div class="form-group profile">
                <label>Tanggal Lahir @if ($isDefaultTgl)<span class="text-danger">*</span>@endif</label>
                <input type="date" name="tanggal_lahir" value="{{ session('tanggal_lahir') }}" class="form-control profile"
                    @if ($isDefaultTgl) style="border-color: #dd4b39;" @endif>
            </div>

            <div class="form-group profile">
                <label>No Telepon @if ($isEmptyNoTelp)<span class="text-danger">*</span>@endif</label>
                <input type="number" name="no_telpon" value="{{ session('no_telpon') }}" class="form-control profile"
                    placeholder="{{ $isEmptyNoTelp ? 'Wajib diisi' : 'Masukkan No Telepon User' }}"
                    @if ($isEmptyNoTelp) style="border-color: #dd4b39;" @endif>
            </div>

            <label><strong>Lokasi Peta</strong> <small class="text-muted">(Geser marker untuk menentukan lokasi Anda)</small></label>
            <div id="map_profil" style="height: 350px; width: 100%; margin-bottom: 15px;"></div>

            <div class="form-group">
                <label>Longitude & Latitude:</label>
                <input type="text" id="longitude_profil" name="longitude" class="form-control mb-2" readonly
                    value="{{ $dataProfil['longitude'] ?? '' }}">
                <input type="text" id="latitude_profil" name="latitude" class="form-control mb-2" readonly
                    value="{{ $dataProfil['latitude'] ?? '' }}">
                <input type="hidden" name="alamat" id="alamat_profil" value="{{ $dataProfil['alamat'] ?? '' }}">
                <div id="alamatLengkap_profil" class="alamat-info-box"
                    style="min-height: 80px; padding: 10px; border: 1px solid #d1ecf1; background: #d1ecf1; border-radius: 4px; color: #0c5460;">
                    <i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-truck"></i> Kota Asal (untuk hitung ongkir) <small class="text-muted">(Otomatis dari peta)</small></label>
                <input type="text" id="search_kota" class="form-control" readonly placeholder="Otomatis dari lokasi peta..."
                    value="{{ $dataProfil['nama_kota'] ?? '' }}" style="background:#f0f0f0;">
                <small class="text-muted" id="kota_hint">Kota asal otomatis terisi dari lokasi marker peta.</small>
                <input type="hidden" name="kode_kota" id="kode_kota_profil" value="{{ $dataProfil['kode_kota'] ?? '' }}">
                <input type="hidden" name="nama_kota" id="nama_kota_profil" value="{{ $dataProfil['nama_kota'] ?? '' }}">
            </div>

            <div class="form-group profile">
                <label>Foto Profil Terkini</label>
                <p></p>
                @php
                    $fotoSrc = $isGoogle
                        ? $foto
                        : (!empty($foto) ? asset('fotopelanggan/' . $foto) : asset('fotodefault/profil_default.png'));
                @endphp
                <img src="{{ $fotoSrc }}" class="img-circle" id="gambar_load" width="100px" height="100px">
            </div>

            <div class="form-group profile">
                <label>Upload Foto Profil Baru</label>
                <p></p>
                <input type="file" class="form-control profile" name="foto_pelanggan" id="preview_gambar">
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ url('pelanggan_data/profil') }}" class="btn btn-primary">Kembali</a>
            </div>

        </form>
    </div>
</main>

@endsection

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var latVal = '{{ $dataProfil['latitude'] ?? '' }}';
    var lngVal = '{{ $dataProfil['longitude'] ?? '' }}';
    var hasCoords = latVal && lngVal;
    var defaultLat = hasCoords ? parseFloat(latVal) : -6.9175;
    var defaultLng = hasCoords ? parseFloat(lngVal) : 107.6191;

    var map = L.map('map_profil').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
    document.getElementById('latitude_profil').value = defaultLat;
    document.getElementById('longitude_profil').value = defaultLng;

    var debounceTimer = null;
    function onMapMove(lat, lng) {
        document.getElementById('latitude_profil').value = lat;
        document.getElementById('longitude_profil').value = lng;
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            geocodeAndDetectKota(lat, lng);
        }, 500);
    }

    map.on('click', function(e) { marker.setLatLng(e.latlng); onMapMove(e.latlng.lat, e.latlng.lng); });
    marker.on('moveend', function(e) { var pos = e.target.getLatLng(); onMapMove(pos.lat, pos.lng); });
    map.invalidateSize();

    if (hasCoords) {
        geocodeAndDetectKota(defaultLat, defaultLng);
    }

    function geocodeAndDetectKota(lat, lng) {
        var alamatBox = document.getElementById('alamatLengkap_profil');
        alamatBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...';

        var controller = new AbortController();
        var timeoutId = setTimeout(function() { controller.abort(); }, 10000);

        fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lng +
            "&zoom=18&addressdetails=1&accept-language=id",
            { headers: { 'User-Agent': 'TokoOnlineApp/1.0' }, signal: controller.signal })
        .then(function(r) { clearTimeout(timeoutId); return r.json(); })
        .then(function(data) {
            if (data.address) {
                var addr = data.address;
                var desa = addr.village || addr.hamlet || addr.suburb || addr.neighbourhood ||
                    addr.quarter || addr.residential || addr.locality || '-';
                var provinsi = addr.state || '-';
                var kota = '-';
                if (addr.county && /kabupaten|kota/i.test(addr.county)) kota = addr.county;
                else if (addr.city) kota = addr.city;
                else if (addr.town) kota = addr.town;
                else if (addr.county) kota = addr.county;
                var kecamatan = addr.subdistrict || addr.district || '-';
                if (kecamatan === '-' && addr.town && addr.town !== kota) kecamatan = addr.town;
                if (kecamatan === '-' && addr.county && !/kabupaten|kota/i.test(addr.county) && addr.county !== kota) kecamatan = addr.county;
                if (kota === '-' && kecamatan !== '-') { kota = kecamatan; kecamatan = '-'; }

                showAlamatProfil(alamatBox, desa, kecamatan, kota, provinsi);
            } else {
                alamatBox.innerHTML = 'Alamat tidak ditemukan.';
            }
        })
        .catch(function(err) {
            clearTimeout(timeoutId);
            if (err.name === 'AbortError') alamatBox.innerHTML = '<i class="fas fa-clock text-warning"></i> Timeout. Geser marker.';
            else alamatBox.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Gagal mengambil alamat.';
        });
    }

    function titleCase(s) {
        return String(s).toLowerCase().replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    }

    function showAlamatProfil(alamatBox, desa, kecamatan, kota, provinsi) {
        desa = titleCase(desa);
        kecamatan = titleCase(kecamatan);
        kota = titleCase(kota);
        provinsi = titleCase(provinsi);

        alamatBox.innerHTML =
            '<div><strong>Desa/Kelurahan:</strong> ' + desa + '</div>' +
            '<div><strong>Kecamatan:</strong> ' + kecamatan + '</div>' +
            '<div><strong>Kota/Kabupaten:</strong> ' + kota + '</div>' +
            '<div><strong>Provinsi:</strong> ' + provinsi + '</div>';

        var alamatText = [desa, kecamatan, kota, provinsi].filter(function(s) { return s !== '-'; }).join(', ');
        document.getElementById('alamat_profil').value = alamatText;

        detectKota(desa, kecamatan, kota, provinsi);
    }

    function detectKota(desa, kecamatan, kota, provinsi) {
        var fallbackName = [kota, provinsi].filter(function(s) { return s !== '-'; }).join(', ');
        if (fallbackName) {
            document.getElementById('search_kota').value = fallbackName;
            document.getElementById('nama_kota_profil').value = fallbackName;
            document.getElementById('kode_kota_profil').value = '';
            document.getElementById('search_kota').style.borderColor = '#ffc107';
            document.getElementById('kota_hint').innerHTML =
                '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Kota terdeteksi dari alamat (tanpa validasi API).</span>';
        } else {
            document.getElementById('kota_hint').innerHTML = 'Kota asal belum terdeteksi. Geser marker peta.';
        }
    }
});
</script>
