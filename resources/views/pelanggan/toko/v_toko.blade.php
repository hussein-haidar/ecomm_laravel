@extends('layouts.app')

@section('content')

    <!-- Detail Toko Section -->
    <main class="toko">
        <div class="detail_toko" id="toko">
            <div class="container mt-4">
                @if (!empty($website_data))
                    @php
                        $nama_toko = $website_data['nama_toko'] ?? '';
                        $logo_toko = !empty($website_data['logo_website']) ? asset('logowebsite/' . $website_data['logo_website']) : asset('images/logo-default.png');
                        $banner_toko = !empty($website_data['bgd_web']) ? asset('logowebsite/' . $website_data['bgd_web']) : null;
                        $rating_toko = (float) ($ringkasan_toko->rata ?? 0);
                        $total_ulasan = (int) ($ringkasan_toko->total ?? 0);
                        $rekomendasi = (int) ($ringkasan_toko->rekomendasi ?? 0);
                        $persen_rekomendasi = $total_ulasan > 0 ? round($rekomendasi / $total_ulasan * 100) : 0;
                        $no_wa = preg_replace('/[^0-9]/', '', $website_data['wa_pusat'] ?? '');
                        if (substr($no_wa, 0, 1) == '0') {
                            $no_wa = '62' . substr($no_wa, 1);
                        }
                    @endphp

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                        <!-- Banner / Cover -->
                        @if ($banner_toko)
                            <div class="w-100 d-flex align-items-center justify-content-center"
                                style="max-height: 320px; overflow: hidden; background: linear-gradient(135deg, #6f42c1, #d63384);">
                                <img src="{{ $banner_toko }}" alt="Banner {{ $nama_toko }}"
                                    style="max-width: 100%; max-height: 320px; width: auto; height: auto; object-fit: contain;">
                            </div>
                        @else
                            <div class="w-100" style="height: 150px; background: linear-gradient(135deg, #6f42c1, #d63384);"></div>
                        @endif

                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- Logo -->
                                <div class="col-md-2 col-4 text-center">
                                    <img src="{{ $logo_toko }}" alt="Logo {{ $nama_toko }}"
                                        class="img-fluid rounded-circle border border-white border-4 shadow"
                                        style="width: 140px; height: 140px; object-fit: cover; background: #fff;">
                                </div>

                                <!-- Info Utama -->
                                <div class="col-md-10 mt-3 mt-md-0">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <h4 class="fw-bold mb-0">{{ $nama_toko }}</h4>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i>Toko Terverifikasi
                                        </span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                            <i class="fas fa-box-open me-1"></i>{{ $total_produk }} Produk
                                        </span>
                                    </div>

                                    <div class="mt-2 d-flex flex-wrap align-items-center gap-3">
                                        <!-- Rating Toko -->
                                        <div class="d-flex align-items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= round($rating_toko) ? 'fas' : 'far' }} fa-star text-warning"></i>
                                            @endfor
                                            <span class="fw-bold ms-2">{{ number_format($rating_toko, 1, ',', '.') }}</span>
                                            <span class="text-muted ms-1">- {{ $total_ulasan }} Ulasan</span>
                                        </div>

                                        @if ($total_ulasan > 0)
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fas fa-thumbs-up me-1"></i>{{ $persen_rekomendasi }}% rekomendasi pembeli
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                        @if (!empty($no_wa))
                                            <a href="https://api.whatsapp.com/send?phone={{ $no_wa }}" target="_blank"
                                                class="btn btn-success btn-sm">
                                                <i class="fab fa-whatsapp"></i> Chat WhatsApp
                                            </a>
                                        @endif
                                        @if ($user_logged_in)
                                            <a href="javascript:void(0)"
                                                onclick="window.bukaChat && bukaChat('{{ $nama_toko }}')"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-comments"></i> Chat Penjual
                                            </a>
                                        @else
                                            <a href="{{ url('auth/login_pelanggan') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-comments"></i> Chat Penjual
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_IG']))
                                            <a href="{{ $website_data['link_IG'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                <i class="fab fa-instagram"></i> Instagram
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_FB']))
                                            <a href="{{ $website_data['link_FB'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fab fa-facebook-f"></i> Facebook
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_Tiktok']))
                                            <a href="{{ $website_data['link_Tiktok'] }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                                <i class="fab fa-tiktok"></i> TikTok
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Detail Informasi -->
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex">
                                        <i class="fas fa-map-marker-alt text-primary mt-1 me-3"></i>
                                        <div>
                                            <div class="fw-semibold">Alamat</div>
                                            <div class="text-muted">{{ $website_data['alamat_pusat'] ?? '-' }}</div>
                                            @if (!empty($website_data['nama_kota']))
                                                <span class="badge bg-light text-dark border mt-1">{{ $website_data['nama_kota'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex">
                                        <i class="fas fa-globe-asia text-primary mt-1 me-3"></i>
                                        <div>
                                            <div class="fw-semibold">Koordinat</div>
                                            <div class="text-muted small">
                                                Latitude: {{ $website_data['latitude_pusat'] ?? '-' }}<br>
                                                Longitude: {{ $website_data['longitude_pusat'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex">
                                        <i class="fas fa-store text-primary mt-1 me-3"></i>
                                        <div>
                                            <div class="fw-semibold">Kecepatan Pengiriman</div>
                                            <div class="text-muted">+- 1 Hari (estimasi)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($website_data['footer_title']))
                                <hr class="my-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="fw-semibold mb-1">Informasi Toko</div>
                                        <div class="text-muted">{{ $website_data['footer_title'] }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">Data toko tidak ditemukan.</div>
                @endif
            </div>
        </div>
    </main>

   <!-- Promo Section -->
<main class="promo-section">
    <div class="container-promo">
        <div class="promo-carousel" id="promoCarousel">
            @foreach ($produk_data as $index => $produk)
                <img 
                    src="{{ asset('fotoproduk/' . $produk['foto_produk']) }}" 
                    class="carousel-img {{ $index === 0 ? 'active' : '' }}" 
                    alt="{{ $produk['nama_produk'] }}"
                    onclick="handleImageClick(event)"
                >
            @endforeach
            <!-- Indikator Titik (navigasi prev/next) -->
            <div class="carousel-dots" id="carouselDots">
                @foreach ($produk_data as $index => $produk)
                    <span class="dot {{ $index === 0 ? 'active' : '' }}" data-slide-to="{{ $index }}"></span>
                @endforeach
            </div>
        </div>
    </div>
</main>

  <!-- Product Section -->
  <main class="product-section">
    <div class="container-product">
        <h3 class="product-title">Katalog Produk</h3>
        <div class="product-grid">
            @include('layouts.v_produk')
        </div>
    </div>
  </main>

@endsection
