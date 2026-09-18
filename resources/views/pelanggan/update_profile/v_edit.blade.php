@extends('layouts.app')
@push('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var latVal = '{{ $dataProfil["latitude"] ?? "" }}';
        var lngVal = '{{ $dataProfil["longitude"] ?? "" }}';
        var hasCoords = latVal && lngVal;
        var defaultLat = hasCoords ? parseFloat(latVal) : -6.9175;
        var defaultLng = hasCoords ? parseFloat(lngVal) : 107.6191;

        var map = L.map('map_profil').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
        window._mapProfil = map;
        window._markerProfil = marker;
        document.getElementById('latitude_profil').value = defaultLat;
        document.getElementById('longitude_profil').value = defaultLng;

        var debounceTimer = null;
        var isUserAction = false;
        function onMapMove(lat, lng) {
            document.getElementById('latitude_profil').value = lat;
            document.getElementById('longitude_profil').value = lng;
            if (debounceTimer) clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                geocodeAndDetectKota(lat, lng, isUserAction);
            }, 500);
            isUserAction = false;
        }

        map.on('click', function(e) {
            isUserAction = true;
            marker.setLatLng(e.latlng);
            onMapMove(e.latlng.lat, e.latlng.lng);
        });
        marker.on('moveend', function(e) {
            isUserAction = true;
            var pos = e.target.getLatLng();
            onMapMove(pos.lat, pos.lng);
        });
        map.invalidateSize();

        if (hasCoords) {
            geocodeAndDetectKota(defaultLat, defaultLng, false);
        } else {
            document.getElementById('alamatLengkap_profil').innerHTML =
                '<i class="fas fa-map-marker-alt text-info"></i> Klik peta atau gunakan tombol GPS untuk menentukan lokasi.';
        }

        window.detectGPS = function() {
            var statusEl = document.getElementById('gps_status');
            var btn = document.getElementById('btn_gps');
            
            if (!navigator.geolocation) {
                statusEl.innerHTML = '<span class="text-danger">Browser tidak mendukung GPS.</span>';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendapatkan lokasi...';
            statusEl.innerHTML = '';

            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                
                var map = window._mapProfil;
                var marker = window._markerProfil;
                if (map) map.setView([lat, lng], 16);
                if (marker) marker.setLatLng([lat, lng]);
                document.getElementById('latitude_profil').value = lat;
                document.getElementById('longitude_profil').value = lng;
                
                isUserAction = true;
                geocodeAndDetectKota(lat, lng, true);
                
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan GPS';
                statusEl.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Lokasi diperoleh dari GPS.</span>';
            }, function(err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-location-arrow"></i> Gunakan GPS';
                var msg = 'Gagal mendapatkan lokasi: ';
                if (err.code === 1) msg += 'Izin ditolak.';
                else if (err.code === 2) msg += 'Posisi tidak tersedia.';
                else if (err.code === 3) msg += 'Timeout.';
                else msg += err.message;
                statusEl.innerHTML = '<span class="text-danger">' + msg + '</span>';
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        }

        function geocodeAndDetectKota(lat, lng, userInitiated) {
            var alamatBox = document.getElementById('alamatLengkap_profil');
            alamatBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...';

            var urlBdc = "https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=" + lat + "&longitude=" + lng + "&localityLanguage=id";
            
            fetch(urlBdc)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && (data.city || data.district || data.subdistrict || data.locality || data.principalSubdivision)) {
                    var rawDesa = data.locality || data.neighbourhood || data.suburb || '-';
                    var rawKecamatan = data.subdistrict || data.district || '-';
                    var rawKota = data.city || '-';
                    var rawProvinsi = data.principalSubdivision || '-';
                    
                    var isCityActuallyKecamatan = (
                        rawKota !== '-' && 
                        rawDesa !== '-' && 
                        rawKota.toLowerCase() === rawDesa.toLowerCase() &&
                        rawKecamatan === '-' &&
                        (rawProvinsi === 'Jawa' || rawProvinsi === 'Jawa Barat' || rawProvinsi === 'Jawa Tengah' || rawProvinsi === 'Jawa Timur')
                    );
                    
                    var desa = '-';
                    var kecamatan = '-';
                    var kota = '-';
                    var provinsi = rawProvinsi;
                    
                    if (isCityActuallyKecamatan) {
                        kecamatan = rawKota;
                        kota = '-';
                    } else {
                        desa = rawDesa;
                        kecamatan = rawKecamatan;
                        kota = rawKota;
                    }
                    
                    if (kota !== '-' && desa !== '-' && kota.toLowerCase() === desa.toLowerCase()) {
                        desa = '-';
                    }
                    
                    if (provinsi === 'Jawa' && kota !== '-') {
                        var jawaMap = {
                            'pekalongan': 'Jawa Tengah', 'semarang': 'Jawa Tengah', 'solo': 'Jawa Tengah', 'jogja': 'Jawa Tengah', 'yogyakarta': 'Jawa Tengah',
                            'surabaya': 'Jawa Timur', 'malang': 'Jawa Timur', 'kediri': 'Jawa Timur', 'mojokerto': 'Jawa Timur',
                            'bandung': 'Jawa Barat', 'bogor': 'Jawa Barat', 'depok': 'Jawa Barat', 'bekasi': 'Jawa Barat', 'cirebon': 'Jawa Barat'
                        };
                        var kotaLower = kota.toLowerCase();
                        for (var k in jawaMap) {
                            if (kotaLower.includes(k)) { provinsi = jawaMap[k]; break; }
                        }
                    }
                    
                    if (desa !== '-') desa = desa.replace(/^(Desa|Kelurahan)\s+/i, '');
                    if (kecamatan !== '-') kecamatan = kecamatan.replace(/^Kecamatan\s+/i, '');
                    if (kota !== '-') kota = kota.replace(/^(Kabupaten|Kota)\s+/i, '');
                    if (provinsi !== '-') provinsi = provinsi.replace(/^Provinsi\s+/i, '');

                    showAlamatProfil(alamatBox, desa, kecamatan, kota, provinsi);
                    return;
                }
                throw new Error('BigDataCloud empty');
            })
            .catch(function() {
                var controller = new AbortController();
                var timeoutId = setTimeout(function() { controller.abort(); }, 10000);

                fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lng +
                    "&zoom=18&addressdetails=1&accept-language=id&countrycodes=id",
                    { headers: { 'User-Agent': 'TokoOnlineApp/1.0' }, signal: controller.signal })
                .then(function(r) { clearTimeout(timeoutId); return r.json(); })
                .then(function(data) {
                    if (data.address) {
                        var addr = data.address;
                        var desa = addr.village || addr.hamlet || addr.neighbourhood || addr.suburb || addr.quarter || addr.residential || addr.locality || addr.island || '-';
                        var kecamatan = addr.subdistrict || addr.district || addr.city_district || addr.borough || '-';
                        var kota = '-';
                        if (addr.county && /kabupaten|kota/i.test(addr.county)) kota = addr.county.replace(/^(Kabupaten|Kota)\s+/i, '');
                        else if (addr.city) kota = addr.city;
                        else if (addr.municipality) kota = addr.municipality;
                        else if (addr.town && !/kabupaten|kota/i.test(addr.town)) kota = addr.town;
                        var provinsi = addr.state || addr.province || '-';
                        
                        if (kecamatan !== '-') kecamatan = kecamatan.replace(/^Kecamatan\s+/i, '');
                        if (desa !== '-') desa = desa.replace(/^(Desa|Kelurahan)\s+/i, '');
                        
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

        window.togglePass = function(inputId, btn) {
            var input = document.getElementById(inputId);
            var icon = btn.querySelector('i');
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
    });
</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Profil</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
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
                            <ul class="mb-0 mt-2">
                                @if ($isEmptyPass)<li>Password (untuk login manual)</li>@endif
                                @if ($isEmptyNoTelp)<li>No Telepon</li>@endif
                                @if ($isDefaultTgl)<li>Tanggal Lahir</li>@endif
                                @if ($isEmptyKota)<li>Kota Asal (untuk ongkir)</li>@endif
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pelanggan_data.update_profile', ['id' => $dataProfil['id_pelanggan']]) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <input type="hidden" name="sesi_user" value="{{ session('sesi_user') ?? '' }}" class="form-control" readonly>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" name="email" value="{{ session('email') }}" class="form-control" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Kata Sandi @if ($isEmptyPass)<span class="text-danger">*</span>@endif</label>
                                <div class="input-group">
                                    <input name="password" type="password" id="ShowPassEdit" value="" class="form-control @if ($isEmptyPass) is-invalid @endif"
                                        placeholder="{{ $isEmptyPass ? 'Wajib diisi untuk login manual (kosongkan jika tidak mau ganti)' : 'Kosongkan jika tidak mau ganti' }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePass('ShowPassEdit', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @if ($isEmptyPass)
                                    <div class="invalid-feedback">Password wajib diisi untuk login manual</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Nama Lengkap</label>
                                <input name="nama_pelanggan" value="{{ session('nama_pelanggan') }}" class="form-control" placeholder="Masukkan Nama Lengkap" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Tanggal Lahir @if ($isDefaultTgl)<span class="text-danger">*</span>@endif</label>
                                <input type="date" name="tanggal_lahir" value="{{ session('tanggal_lahir') }}" class="form-control @if ($isDefaultTgl) is-invalid @endif"
                                    max="{{ date('Y-m-d', strtotime('-13 years')) }}">
                                @if ($isDefaultTgl)
                                    <div class="invalid-feedback">Tanggal lahir wajib diisi</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">No Telepon @if ($isEmptyNoTelp)<span class="text-danger">*</span>@endif</label>
                                <input type="tel" name="no_telpon" value="{{ session('no_telpon') }}" class="form-control @if ($isEmptyNoTelp) is-invalid @endif"
                                    placeholder="{{ $isEmptyNoTelp ? 'Wajib diisi' : 'Masukkan No Telepon' }}"
                                    pattern="[0-9]+" inputmode="numeric">
                                @if ($isEmptyNoTelp)
                                    <div class="invalid-feedback">No telepon wajib diisi</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium"><i class="fas fa-map-marker-alt me-1"></i> Lokasi Peta <small class="text-muted">(Geser marker untuk menentukan lokasi Anda)</small></label>
                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                    <button type="button" id="btn_gps" class="btn btn-outline-primary"
                                        onclick="detectGPS()"><i class="fas fa-location-arrow"></i> Gunakan GPS</button>
                                    <span id="gps_status" class="align-self-center text-muted small"></span>
                                </div>
                                <div id="map_profil" style="height: 350px; width: 100%; margin-bottom: 15px; border-radius: 8px; overflow: hidden;"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Longitude</label>
                                <input type="text" id="longitude_profil" name="longitude" class="form-control" readonly
                                    value="{{ $dataProfil['longitude'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Latitude</label>
                                <input type="text" id="latitude_profil" name="latitude" class="form-control" readonly
                                    value="{{ $dataProfil['latitude'] ?? '' }}">
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="alamat" id="alamat_profil" value="{{ $dataProfil['alamat'] ?? '' }}">
                                <div id="alamatLengkap_profil" class="alert alert-info p-3 mb-0" style="min-height: 100px;">
                                    <i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium"><i class="fas fa-truck me-1"></i> Kota Asal (untuk hitung ongkir) <small class="text-muted">(Otomatis dari peta)</small></label>
                                <input type="text" id="search_kota" class="form-control" readonly placeholder="Otomatis dari lokasi marker peta..."
                                    value="{{ $dataProfil['nama_kota'] ?? '' }}">
                                <small class="text-muted" id="kota_hint">Kota asal otomatis terisi dari lokasi marker peta.</small>
                                <input type="hidden" name="kode_kota" id="kode_kota_profil" value="{{ $dataProfil['kode_kota'] ?? '' }}">
                                <input type="hidden" name="nama_kota" id="nama_kota_profil" value="{{ $dataProfil['nama_kota'] ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Foto Profil Terkini</label>
                                @php
                                    $fotoSrc = $isGoogle
                                        ? $foto
                                        : (!empty($foto) ? asset('fotopelanggan/' . $foto) : asset('assets/images/logo.png'));
                                @endphp
                                <img src="{{ $fotoSrc }}" class="rounded-circle border border-3 shadow-sm mb-2" id="gambar_load" width="100" height="100" style="object-fit: cover;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Upload Foto Profil Baru</label>
                                <input type="file" class="form-control" name="foto_pelanggan" id="preview_gambar" accept="image/png,image/jpg,image/jpeg">
                                <div class="form-text">Format: PNG/JPG/JPEG, Maksimal 1MB</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('pelanggan_data.profil') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection