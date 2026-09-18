@extends('layouts.app')
@push('scripts')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const csrfToken = "{{ csrf_token() }}";
    const searchDestinationUrl = "{{ route('pelanggan_data.search_destination') }}";
    const refineAlamatUrl = "{{ route('pelanggan_data.refine_alamat') }}";
    const hitungOngkirUrl = "{{ route('pelanggan_data.hitung_ongkir') }}";
    const hitungOngkirLokalUrl = "{{ route('pelanggan_data.hitung_ongkir_lokal') }}";

    // Kode Transaksi
    (function() {
        const namaProdukPertama = "{{ $items[0]['nama_produk'] ?? 'PRODUK' }}";
        const encodedNamaProduk = encodeURIComponent(namaProdukPertama);
        const url = "{{ route('pelanggan_data.generateKodeTransaksi') }}?nama_produk=" + encodedNamaProduk;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat kode pembelian');
                return response.json();
            })
            .then(data => {
                document.getElementById('kode_beli').value = data.kode_beli || 'KODE-' + Date.now();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('kode_beli').value = 'AUTO-' + Math.random().toString(36).substr(2, 9).toUpperCase();
            });
    })();

    // MAP
    document.addEventListener("DOMContentLoaded", function() {
        const lat = parseFloat(document.getElementById('latitude_pelanggan').value) || -6.9175;
        const lng = parseFloat(document.getElementById('longitude_pelanggan').value) || 107.6191;

        const map = L.map('map_pusat').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        function onMarkerMoved(pos) {
            document.getElementById('latitude_pelanggan').value = pos.lat;
            document.getElementById('longitude_pelanggan').value = pos.lng;
            updateInfoJarak(pos.lat, pos.lng);
            debounceGeocodeCheckout(pos.lat, pos.lng);
        }

        marker.on('moveend', function(e) {
            onMarkerMoved(e.target.getLatLng());
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            onMarkerMoved(e.latlng);
        });

        if (lat !== -6.9175 && lng !== 107.6191) {
            updateInfoJarak(lat, lng);
            debounceGeocodeCheckout(lat, lng);
        }
    });

    let geocodeTimer = null;

    function debounceGeocodeCheckout(lat, lng) {
        if (geocodeTimer) clearTimeout(geocodeTimer);
        geocodeTimer = setTimeout(function() { reverseGeocodeCheckout(lat, lng); }, 500);
    }

    function reverseGeocodeCheckout(lat, lng) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lng +
                "&zoom=18&addressdetails=1&accept-language=id", {
                    headers: { 'User-Agent': 'TokoOnlineApp/1.0' },
                    signal: controller.signal
                })
            .then(r => { clearTimeout(timeoutId); return r.json(); })
            .then(data => {
                if (data.address) {
                    const addr = data.address;
                    let desa = addr.village || addr.hamlet || addr.suburb || addr.neighbourhood ||
                        addr.quarter || addr.residential || addr.locality || '';
                    let provinsi = addr.state || '';
                    let kota = '';
                    if (addr.county && /kabupaten|kota/i.test(addr.county)) kota = addr.county;
                    else if (addr.city) kota = addr.city;
                    else if (addr.town) kota = addr.town;
                    else if (addr.county) kota = addr.county;
                    let kecamatan = addr.subdistrict || addr.district || '';
                    if (kecamatan === '' && addr.town && addr.town !== kota) kecamatan = addr.town;
                    if (kota === '' && kecamatan !== '') { kota = kecamatan; kecamatan = ''; }
                    refineAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng);
                }
            })
            .catch(err => {
                clearTimeout(timeoutId);
                console.error("Geocode error:", err);
            });
    }

    function titleCase(s) {
        return String(s).toLowerCase().replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    }

    function setAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng) {
        const alamatParts = [desa, kecamatan, kota, provinsi].filter(s => s);
        if (alamatParts.length > 0) {
            document.getElementById('alamat').value = alamatParts.join(', ');
        }
        autoDetectKodeKotaCheckout(desa, kecamatan, kota, provinsi, lat, lng);
    }

    function refineAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng) {
        if (kecamatan !== '' || desa === '' || kota === '') {
            setAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng);
            return;
        }
        fetch(refineAlamatUrl + "?desa=" + encodeURIComponent(desa) +
                "&kota=" + encodeURIComponent(kota) + "&lat=" + lat + "&lng=" + lng)
            .then(r => r.json())
            .then(res => {
                if (res.match) {
                    desa = titleCase(res.match.desa);
                    kecamatan = titleCase(res.match.kecamatan);
                }
                setAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng);
            })
            .catch(() => setAlamatCheckout(desa, kecamatan, kota, provinsi, lat, lng));
    }

    function autoDetectKodeKotaCheckout(desa, kecamatan, kota, provinsi, lat, lng) {
        const queries = [
            [desa, kecamatan, kota, provinsi].filter(s => s).join(', '),
            [kecamatan, kota, provinsi].filter(s => s).join(', '),
            [kota, provinsi].filter(s => s).join(', ')
        ];
        trySearchCheckout(queries, 0, lat, lng);
    }

    function trySearchCheckout(queries, index, lat, lng) {
        if (index >= queries.length) return;
        fetch(searchDestinationUrl + "?search=" + encodeURIComponent(queries[index]) + "&lat=" + lat + "&lng=" + lng)
            .then(r => r.json())
            .then(res => {
                if (res.data && res.data.length > 0) {
                    const addr = queries[index].toLowerCase();
                    const scored = res.data.map(item => {
                        const label = item.label.toLowerCase();
                        let score = 0;
                        addr.split(',').map(s => s.trim()).forEach(part => {
                            if (label.includes(part)) score++;
                        });
                        return Object.assign({}, item, { score: score });
                    }).sort(function(a, b) { return b.score - a.score; });
                    const best = scored[0];
                    document.getElementById('destination_id').value = best.id;
                    document.getElementById('selected_destination').value = best.label;
                    hitungSemuaOngkir(best.id);
                } else {
                    trySearchCheckout(queries, index + 1, lat, lng);
                }
            })
            .catch(err => console.error("Search error:", err));
    }
</script>

<script>
    const kodeKotaToko = {{ json_encode((int) $kode_kota_toko ?? 0) }};
    const totalBelanja = {{ json_encode($totalSemua ?? 0) }};
    const latToko = {{ json_encode($latitude_pusat ?? -6.9175) }};
    const lngToko = {{ json_encode($longitude_pusat ?? 107.6191) }};

    function haversine(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
                + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
                * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function updateInfoJarak(latPelanggan, lngPelanggan) {
        const jarak = haversine(latToko, lngToko, latPelanggan, lngPelanggan);
        const jarakR = Math.round(jarak * 10) / 10;
        document.getElementById('jarak_km').value = jarakR;
        document.getElementById('jarak_text').textContent = jarakR + ' km dari toko';
        document.getElementById('info_jarak').style.display = 'block';
        return jarakR;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const bankSelect = document.getElementById("bank_tujuan");
        const noA_NInput = document.getElementById("a_n");
        const noRekInput = document.getElementById("no_rek");

        bankSelect.addEventListener("change", function() {
            const selectedOption = this.options[this.selectedIndex];
            noA_NInput.value = selectedOption.getAttribute("data-a_n") || "";
            noRekInput.value = selectedOption.getAttribute("data-rekening") || "";
        });

        const kodeKotaPelanggan = {{ json_encode($kode_kota_pelanggan ?? '') }};
        const namaKotaPelanggan = {{ json_encode($nama_kota_pelanggan ?? '') }};
        if (kodeKotaPelanggan && !document.getElementById("destination_id").value) {
            document.getElementById("destination_id").value = kodeKotaPelanggan;
            document.getElementById("selected_destination").value = namaKotaPelanggan;
            const latP = parseFloat(document.getElementById('latitude_pelanggan').value) || 0;
            const lngP = parseFloat(document.getElementById('longitude_pelanggan').value) || 0;
            if (latP && lngP) updateInfoJarak(latP, lngP);
            hitungSemuaOngkir(kodeKotaPelanggan);
        }
    });

    function hitungSemuaOngkir(destId) {
        const loading = document.getElementById("loading_ongkir");
        const hasil = document.getElementById("hasil_ongkir");
        loading.style.display = "block";
        hasil.style.display = "none";
        document.getElementById("daftar_ongkir_lokal").innerHTML = '';
        document.getElementById("daftar_ongkir_nasional").innerHTML = '';
        document.getElementById("section_ongkir_lokal").style.display = 'none';
        document.getElementById("section_ongkir_nasional").style.display = 'none';

        const jarakKm = parseFloat(document.getElementById('jarak_km').value) || 0;
        const promises = [];

        if (jarakKm > 0 && jarakKm <= 50) {
            const fdLokal = new FormData();
            fdLokal.append("jarak", jarakKm);
            fdLokal.append("_token", csrfToken);
            promises.push(
                fetch(hitungOngkirLokalUrl, { method: "POST", body: fdLokal })
                    .then(r => r.json())
                    .then(res => {
                        if (res.data && res.data.length > 0) {
                            renderOngkirLokal(res.data, jarakKm);
                        }
                    })
            );
        }

        const originId = kodeKotaToko;
        if (originId && destId) {
            let totalBerat = 0;
            @foreach ($items as $item)
                totalBerat += {{ ($item['berat_produk'] ?? 500) * ($item['jumlah_produk'] ?? 1) }};
            @endforeach
            if (totalBerat <= 0) totalBerat = 1000;

            const fdNasional = new FormData();
            fdNasional.append("origin", originId);
            fdNasional.append("destination", destId);
            fdNasional.append("weight", totalBerat);
            fdNasional.append("courier", "jne:jnt:sicepat");
            fdNasional.append("_token", csrfToken);
            promises.push(
                fetch(hitungOngkirUrl, { method: "POST", body: fdNasional })
                    .then(r => r.json())
                    .then(res => {
                        if (res.data && res.data.length > 0) {
                            renderOngkirNasional(res.data);
                        } else if (res.error) {
                            document.getElementById("daftar_ongkir_nasional").innerHTML =
                                '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> ' + res.error + '</div>';
                            document.getElementById("section_ongkir_nasional").style.display = 'block';
                        }
                    })
            );

            Promise.all(promises).then(() => {
                loading.style.display = "none";
                const hasLokal = document.getElementById("section_ongkir_lokal").style.display !== 'none';
                const hasNasional = document.getElementById("section_ongkir_nasional").style.display !== 'none';
                if (hasLokal || hasNasional) {
                    hasil.style.display = "block";
                } else {
                    document.getElementById("daftar_ongkir_nasional").innerHTML =
                        '<div class="alert alert-warning">Tidak ada layanan pengiriman tersedia untuk tujuan ini.</div>';
                    document.getElementById("section_ongkir_nasional").style.display = 'block';
                    hasil.style.display = "block";
                }
            }).catch(err => {
                loading.style.display = "none";
                document.getElementById("daftar_ongkir_nasional").innerHTML =
                    '<div class="alert alert-danger">Terjadi kesalahan saat menghitung ongkir.</div>';
                document.getElementById("section_ongkir_nasional").style.display = 'block';
                hasil.style.display = "block";
                console.error("Ongkir error:", err);
            });
    }

    function renderOngkirLokal(data, jarakKm) {
        let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
        html += '<thead class="table-light"><tr><th>Pilih</th><th>Kurir</th><th>Layanan</th><th>Biaya</th><th>Estimasi</th></tr></thead><tbody>';

        data.forEach((item, idx) => {
            const costRp = 'Rp. ' + Number(item.cost).toLocaleString('id-ID');
            html += '<tr style="cursor:pointer;" onclick="pilihOngkir(this)" '
                + 'data-kurir="' + item.name + '" '
                + 'data-service="' + item.service + '" '
                + 'data-cost="' + item.cost + '" '
                + 'data-etd="' + item.etd + '">'
                + '<td><input type="radio" name="pilih_ongkir" value="lokal_' + idx + '"></td>'
                + '<td>' + item.name + '</td>'
                + '<td>' + item.service + ' (' + jarakKm + ' km)</td>'
                + '<td>' + costRp + '</td>'
                + '<td>' + item.etd + '</td>'
                + '</tr>';
        });

        html += '</tbody></table></div>';
        document.getElementById("daftar_ongkir_lokal").innerHTML = html;
        document.getElementById("section_ongkir_lokal").style.display = 'block';
    }

    function renderOngkirNasional(data) {
        let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
        html += '<thead class="table-light"><tr><th>Pilih</th><th>Kurir</th><th>Layanan</th><th>Biaya</th><th>Estimasi</th></tr></thead><tbody>';

        data.forEach((item, idx) => {
            const costRp = 'Rp. ' + Number(item.cost).toLocaleString('id-ID');
            const etd = item.etd || '-';
            html += '<tr style="cursor:pointer;" onclick="pilihOngkir(this)" '
                + 'data-kurir="' + item.name + '" '
                + 'data-service="' + item.service + '" '
                + 'data-cost="' + item.cost + '" '
                + 'data-etd="' + etd + '">'
                + '<td><input type="radio" name="pilih_ongkir" value="nasional_' + idx + '"></td>'
                + '<td>' + item.name + '</td>'
                + '<td>' + item.service + '</td>'
                + '<td>' + costRp + '</td>'
                + '<td>' + etd + '</td>'
                + '</tr>';
        });

        html += '</tbody></table></div>';
        document.getElementById("daftar_ongkir_nasional").innerHTML = html;
        document.getElementById("section_ongkir_nasional").style.display = 'block';
    }

    function pilihOngkir(row) {
        row.querySelector('input[type="radio"]').checked = true;

        const kurir = row.dataset.kurir;
        const service = row.dataset.service;
        const cost = parseInt(row.dataset.cost);
        const etd = row.dataset.etd;

        document.getElementById("jenis_kurir").value = kurir + ' - ' + service;
        document.getElementById("ongkir").value = cost;
        document.getElementById("estimasi_waktu").value = etd;
        document.getElementById("ongkir_display").value = 'Rp. ' + cost.toLocaleString('id-ID') + ' (' + kurir + ' ' + service + ')';
        document.getElementById("estimasi_display").value = etd;

        const totalAkhir = totalBelanja + cost;
        document.getElementById("total_bayar_text").textContent = 'Rp. ' + totalAkhir.toLocaleString('id-ID');

        document.querySelectorAll('#daftar_ongkir_lokal tr, #daftar_ongkir_nasional tr').forEach(tr => tr.style.backgroundColor = '');
        row.style.backgroundColor = '#d4edda';
    }
</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Beli Langsung</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('pelanggan_data.saveBeliLangsung') }}" method="POST" id="beli-langsung-form" enctype="multipart/form-data" novalidate>
                        @csrf

                        <input type="hidden" name="sesi_user" value="{{ $items[0]['sesi_user'] ?? '' }}">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Kode Pembelian</label>
                                <input type="text" id="kode_beli" name="kode_beli" class="form-control" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control" readonly
                                    value="{{ session('nama_pelanggan', $pelanggan->nama_pelanggan ?? '') }}">
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3 fw-semibold">Rincian Produk Yang Dipilih:</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Ukuran</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                        <th>Toko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>
                                                @if (!empty($item['foto_produk']))
                                                    <img src="{{ asset('fotoproduk/' . $item['foto_produk']) }}" class="rounded" width="60" height="60" style="object-fit: cover;" alt="{{ $item['nama_produk'] ?? 'Produk' }}">
                                                @else
                                                    <span class="text-muted">Tidak ada foto</span>
                                                @endif
                                                <input type="hidden" name="id_stok" value="{{ $item['id_stok'] }}">
                                            </td>
                                            <td class="fw-medium">{{ $item['nama_produk'] ?? 'Nama Tidak Diketahui' }}
                                                <input type="hidden" name="nama_produk" value="{{ $item['nama_produk'] }}">
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $item['ukuran_produk'] ?? '-' }}</span>
                                                <input type="hidden" name="ukuran_produk" value="{{ $item['ukuran_produk'] }}">
                                            </td>
                                            <td class="text-center">{{ $item['jumlah_produk'] ?? 0 }} {{ $item['satuan_produk'] ?? '' }}
                                                <input type="hidden" name="jumlah_produk" value="{{ $item['jumlah_produk'] }}">
                                                <input type="hidden" name="satuan_produk" value="{{ $item['satuan_produk'] }}">
                                            </td>
                                            <td class="text-end">Rp{{ number_format($item['harga_produk'] ?? 0, 0, ',', '.') }}
                                                <input type="hidden" name="harga_produk" value="{{ $item['harga_produk'] }}">
                                            </td>
                                            <td class="text-end fw-semibold text-success">Rp{{ number_format(($item['harga_produk'] ?? 0) * ($item['jumlah_produk'] ?? 0), 0, ',', '.') }}</td>
                                            <td>{{ $item['nama_toko'] ?? 'Toko Tidak Diketahui' }}
                                                <input type="hidden" name="sesi_user" value="{{ $item['sesi_user'] }}">
                                                <input type="hidden" name="nama_toko" value="{{ $item['nama_toko'] }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="map_pusat" style="height: 350px; width: 100%; border-radius: 8px; overflow: hidden;" class="mb-4"></div>

                        <input type="hidden" id="latitude_pelanggan" name="latitude" value="{{ $latitude_pelanggan ?? '' }}">
                        <input type="hidden" id="longitude_pelanggan" name="longitude" value="{{ $longitude_pelanggan ?? '' }}">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Alamat Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                                    value="{{ $alamat_pelanggan ?? '' }}" placeholder="Isi alamat lengkap pengiriman (jalan, RT/RW, kelurahan, kecamatan, kota, provinsi, kode pos)" required>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Bank Tujuan <span class="text-danger">*</span></label>
                                <select id="bank_tujuan" name="bank_tujuan" class="form-select @error('bank_tujuan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach ($bank as $b)
                                        <option value="{{ $b->nama_bank }}" data-rekening="{{ $b->no_rek }}" data-a_n="{{ $b->a_n }}">
                                            {{ $b->nama_bank }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">A.N Rekening</label>
                                <input type="text" id="a_n" name="a_n" class="form-control" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">No Rekening</label>
                                <input type="text" id="no_rek" name="no_rek" class="form-control" readonly>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-semibold mb-3"><i class="fas fa-truck me-2"></i> Pengiriman</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="form-label fw-medium mb-1"><i class="fas fa-user me-1"></i> Pemesan</label>
                                    <div class="fw-bold">{{ session('nama_pelanggan', '') }}</div>
                                    <div class="text-muted small">{{ $alamat_pelanggan ?? '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="form-label fw-medium mb-1"><i class="fas fa-store me-1"></i> Pengirim</label>
                                    <div class="fw-bold">{{ $nama_toko ?? 'Toko' }}</div>
                                    <div class="text-muted small">{{ $alamat_toko ?? 'Alamat toko belum diatur' }}</div>
                                    <input type="hidden" id="origin_id" value="{{ $kode_kota_toko ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div id="info_jarak" class="alert alert-info d-none mb-3" style="margin-bottom:0;">
                            <i class="fas fa-map-marker-alt"></i> <strong>Jarak ke Toko:</strong> <span id="jarak_text"></span>
                        </div>

                        <input type="hidden" id="jarak_km" name="jarak_km">

                        <div id="loading_ongkir" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                            <div class="mt-2">Memuat data ongkir...</div>
                        </div>

                        <div id="hasil_ongkir" class="d-none">
                            <div id="section_ongkir_lokal" class="d-none mb-4">
                                <h6 class="fw-semibold mb-2"><i class="fas fa-motorcycle me-1"></i> Kurir Lokal</h6>
                                <div id="daftar_ongkir_lokal"></div>
                            </div>
                            <div id="section_ongkir_nasional" class="d-none mb-4">
                                <h6 class="fw-semibold mb-2"><i class="fas fa-truck me-1"></i> Kurir Nasional</h6>
                                <div id="daftar_ongkir_nasional"></div>
                            </div>
                        </div>

                        <input type="hidden" id="jenis_kurir" name="jenis_kurir">
                        <input type="hidden" id="ongkir" name="ongkir">
                        <input type="hidden" id="estimasi_waktu" name="estimasi_waktu">
                        <input type="hidden" id="destination_id" value="">
                        <input type="hidden" id="selected_destination" value="">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Ongkos Kirim</label>
                                <input type="text" id="ongkir_display" class="form-control" readonly placeholder="Pilih tujuan dan layanan pengiriman">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Estimasi Sampai Tujuan</label>
                                <input type="text" id="estimasi_display" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="alert alert-info p-3 mb-4">
                            <p class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Silahkan lakukan pembayaran sebesar:</strong>
                                <span id="total_bayar_text" class="fw-bold text-success fs-5">Rp{{ number_format($totalSemua ?? 0, 0, ',', '.') }}</span>
                                melalui bank beserta rekening yang tertera.
                            </p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pemesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection