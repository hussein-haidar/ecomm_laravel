@extends('layouts.template') <!-- Sesuaikan layout jika ada -->

@section('content')
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header"></div>
                <div class="box-body">
                    <!-- Alert Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Open -->
                    <form action="{{ route('pemilik_data.save_website') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Sesi User</label>
                            <input type="text" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                                readonly>
                        </div>

                        <input type="hidden" name="level" value="{{ session('level') }}" class="form-control" readonly>

                        <input type="hidden" name="id_user" value="{{ session('id_user') }}" class="form-control" readonly>

                        <div class="form-group">
                            <label>Nama Toko</label>
                            <input name="nama_toko" class="form-control" placeholder="Masukkan Nama Toko" required>
                        </div>

                        <!-- Lokasi Toko Pusat -->
                        <label><strong>Pilih Lokasi Toko Pusat</strong></label>
                        <div id="map_pusat" style="height: 300px; width: 100%; margin-bottom: 15px;"></div>
                        <div class="form-group">
                            <label for="latitude_pusat">Latitude Toko Pusat:</label>
                            <input type="text" id="latitude_pusat" name="latitude_pusat" class="form-control" readonly
                                required>
                        </div>
                        <div class="form-group">
                            <label for="longitude_pusat">Longitude Toko Pusat:</label>
                            <input type="text" id="longitude_pusat" name="longitude_pusat" class="form-control" readonly
                                required>
                        </div>
                        <div class="form-group">
                            <label>Alamat Toko Pusat</label>
                            <input name="alamat_pusat" class="form-control" placeholder="Masukkan Alamat Toko" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon Toko Pusat</label>
                            <input type="number" name="wa_pusat" class="form-control"
                                placeholder="Masukkan No Telepon Toko" required>
                        </div>

                        <!-- Lokasi Toko Cabang -->
                        <label><strong>Pilih Lokasi Toko Cabang</strong></label>
                        <div id="map_cabang" style="height: 300px; width: 100%; margin-bottom: 15px;"></div>
                        <div class="form-group">
                            <label for="latitude_cabang">Latitude Toko Cabang:</label>
                            <input type="text" id="latitude_cabang" name="latitude_cabang" class="form-control" readonly
                                required>
                        </div>
                        <div class="form-group">
                            <label for="longitude_cabang">Longitude Toko Cabang:</label>
                            <input type="text" id="longitude_cabang" name="longitude_cabang" class="form-control"
                                readonly required>
                        </div>
                        <div class="form-group">
                            <label>Alamat Toko Cabang</label>
                            <input name="alamat_cabang" class="form-control" placeholder="Masukkan Alamat Toko" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon Toko Cabang</label>
                            <input type="number" name="wa_cabang" class="form-control"
                                placeholder="Masukkan No Telepon Toko" required>
                        </div>

                        <div class="form-group">
                            <label>Footer Toko</label>
                            <input type="text" name="footer_title" class="form-control"
                                placeholder="Masukkan Footer Toko" required>
                        </div>

                        <div class="form-group">
                            <label>Link FB</label>
                            <input type="text" name="link_FB" class="form-control" placeholder="Masukkan Link Instagram"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Link IG</label>
                            <input type="text" name="link_IG" class="form-control" placeholder="Masukkan Link Instagram"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Link Tiktok</label>
                            <input type="text" name="link_Tiktok" class="form-control" placeholder="Masukkan Link TikTok"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="logo_website">Unggah Logo Website (Opsional)</label>
                            <input type="file" name="logo_website" id="logo_input" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                        </div>
                        <div class="form-group">
                            <label for="bgd_web">Unggah Background Website (Opsional)</label>
                            <input type="file" name="bgd_web" id="bg_input" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('pemilik_data.website') }}" class="btn btn-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css " />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js "></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const defaultPusatLat = parseFloat(
                "{{ old('latitude_pusat', $data_website['latitude_pusat'] ?? '-6.9175') }}");
            const defaultPusatLng = parseFloat(
                "{{ old('longitude_pusat', $data_website['longitude_pusat'] ?? '107.6191') }}");
            const defaultCabangLat = parseFloat(
                "{{ old('latitude_cabang', $data_website['latitude_cabang'] ?? '-6.9175') }}");
            const defaultCabangLng = parseFloat(
                "{{ old('longitude_cabang', $data_website['longitude_cabang'] ?? '107.6191') }}");

            function initMap(id, defaultLat, defaultLng, latInputId, lngInputId) {
                if (!document.getElementById(id)) return;
                let map = L.map(id).setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                let marker = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(map);
                document.getElementById(latInputId).value = defaultLat;
                document.getElementById(lngInputId).value = defaultLng;

                // Deteksi lokasi
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                        document.getElementById(latInputId).value = lat;
                        document.getElementById(lngInputId).value = lng;
                    });
                }

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    document.getElementById(latInputId).value = e.latlng.lat;
                    document.getElementById(lngInputId).value = e.latlng.lng;
                });

                marker.on('moveend', function(e) {
                    const pos = e.target.getLatLng();
                    document.getElementById(latInputId).value = pos.lat;
                    document.getElementById(lngInputId).value = pos.lng;
                });

                setTimeout(() => map.invalidateSize(), 500);
            }

            initMap('map_pusat', defaultPusatLat, defaultPusatLng, 'latitude_pusat', 'longitude_pusat');
            initMap('map_cabang', defaultCabangLat, defaultCabangLng, 'latitude_cabang', 'longitude_cabang');
        });
    </script>


@endsection
