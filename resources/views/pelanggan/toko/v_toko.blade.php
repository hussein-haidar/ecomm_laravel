@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero carousel auto-slide
        const carousel = document.getElementById('tokoCarousel');
        if (carousel) {
            new bootstrap.Carousel(carousel, {
                interval: 5000,
                ride: 'carousel',
                pause: 'hover',
                wrap: true
            });
        }

        // Smooth scroll for tabs
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
</script>
@endpush

@section('content')
@if(empty($website_data))
    <div class="container py-5">
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h4>Data Toko Tidak Ditemukan</h4>
            <p class="text-muted">Toko yang Anda cari tidak tersedia atau telah dinonaktifkan.</p>
            <a href="{{ route('home_toko.index') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
@else
    @php
        $nama_toko = $website_data['nama_toko'] ?? '';
        $logo_toko = !empty($website_data['logo_website']) ? asset('logo_website/' . $website_data['logo_website']) : asset('assets/images/logo.png');
        $banner_toko = !empty($website_data['bgd_web']) ? asset('logo_website/' . $website_data['bgd_web']) : null;
        $rating_toko = (float) ($ringkasan_toko->rata ?? 0);
        $total_ulasan = (int) ($ringkasan_toko->total ?? 0);
        $rekomendasi = (int) ($ringkasan_toko->rekomendasi ?? 0);
        $persen_rekomendasi = $total_ulasan > 0 ? round($rekomendasi / $total_ulasan * 100) : 0;
        $no_wa = preg_replace('/[^0-9]/', '', $website_data['wa_pusat'] ?? '');
        if (substr($no_wa, 0, 1) == '0') {
            $no_wa = '62' . substr($no_wa, 1);
        }
    @endphp

    {{-- Store Header --}}
    <section class="position-relative" style="min-height: 400px;">
        {{-- Banner --}}
        @if($banner_toko)
            <div class="w-100 position-relative" style="height: 300px; overflow: hidden;">
                <img src="{{ $banner_toko }}" alt="Banner {{ $nama_toko }}" class="w-100 h-100" style="object-fit: cover;">
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
            </div>
        @else
            <div class="w-100 position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
        @endif

        {{-- Store Info Card --}}
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="margin-top: -60px; z-index: 10; position: relative;">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row align-items-center">
                                {{-- Logo --}}
                                <div class="col-auto text-center mb-3 mb-lg-0">
                                    <img src="{{ $logo_toko }}" alt="Logo {{ $nama_toko }}"
                                         class="img-fluid rounded-circle border border-white border-4 shadow"
                                         style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
                                </div>

                                {{-- Info --}}
                                <div class="col ps-lg-4">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <h3 class="fw-bold mb-0">{{ $nama_toko }}</h3>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Toko Terverifikasi
                                        </span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            <i class="fas fa-box-open me-1"></i>{{ $total_produk }} Produk
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                        {{-- Rating --}}
                                        <div class="d-flex align-items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= round($rating_toko) ? 'fas' : 'far' }} fa-star text-warning"></i>
                                            @endfor
                                            <span class="fw-bold ms-2">{{ number_format($rating_toko, 1, ',', '.') }}</span>
                                            <span class="text-muted ms-1">({{ $total_ulasan }} Ulasan)</span>
                                        </div>

                                        @if ($total_ulasan > 0)
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-thumbs-up me-1"></i>{{ $persen_rekomendasi }}% Rekomendasi
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex flex-wrap gap-2">
                                        @if (!empty($no_wa))
                                            <a href="https://api.whatsapp.com/send?phone={{ $no_wa }}" target="_blank"
                                               class="btn btn-success rounded-pill px-4" aria-label="Chat WhatsApp">
                                                <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                                            </a>
                                        @endif
                                        @if ($user_logged_in)
                                            <a href="javascript:void(0)"
                                               onclick="window.bukaChat && bukaChat('{{ $nama_toko }}')"
                                               class="btn btn-outline-primary rounded-pill px-4">
                                                <i class="fas fa-comments me-2"></i>Chat Penjual
                                            </a>
                                        @else
                                            <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-outline-primary rounded-pill px-4">
                                                <i class="fas fa-comments me-2"></i>Chat Penjual
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_IG']))
                                            <a href="{{ $website_data['link_IG'] }}" target="_blank" class="btn btn-outline-danger rounded-pill px-4" aria-label="Instagram">
                                                <i class="fab fa-instagram me-1"></i> Instagram
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_FB']))
                                            <a href="{{ $website_data['link_FB'] }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4" aria-label="Facebook">
                                                <i class="fab fa-facebook-f me-1"></i> Facebook
                                            </a>
                                        @endif
                                        @if (!empty($website_data['link_Tiktok']))
                                            <a href="{{ $website_data['link_Tiktok'] }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4" aria-label="TikTok">
                                                <i class="fab fa-tiktok me-1"></i> TikTok
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Detail Info --}}
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                                        <div class="text-primary" style="font-size: 1.5rem;"><i class="fas fa-map-marker-alt"></i></div>
                                        <div>
                                            <div class="fw-semibold">Alamat Toko</div>
                                            <div class="text-muted small">{{ $website_data['alamat_pusat'] ?? '-' }}</div>
                                            @if (!empty($website_data['nama_kota']))
                                                <span class="badge bg-light text-dark border mt-1">{{ $website_data['nama_kota'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                                        <div class="text-primary" style="font-size: 1.5rem;"><i class="fas fa-globe-asia"></i></div>
                                        <div>
                                            <div class="fw-semibold">Koordinat</div>
                                            <div class="text-muted small">
                                                Lat: {{ $website_data['latitude_pusat'] ?? '-' }}<br>
                                                Lng: {{ $website_data['longitude_pusat'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                                        <div class="text-primary" style="font-size: 1.5rem;"><i class="fas fa-truck-fast"></i></div>
                                        <div>
                                            <div class="fw-semibold">Estimasi Pengiriman</div>
                                            <div class="text-muted small">1-3 Hari Kerja (estimasi)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($website_data['footer_title']))
                                <hr class="my-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fw-semibold mb-1">Informasi Toko</div>
                                    <div class="text-muted">{{ $website_data['footer_title'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Products Tabs --}}
    <section class="py-5" id="produk-toko">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Produk dari {{ $nama_toko }}</h2>
                    <p class="text-muted mb-0">Menampilkan {{ count($produk_data ?? []) }} produk</p>
                </div>
            </div>

            @if(!empty($produk_data))
                <div class="row g-4" id="productGrid">
                    @foreach($produk_data as $produk)
                        @include('layouts.v_produk_item', ['produk' => $produk, 'loop' => $loop])
                    @endforeach
                </div>
            @else
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum Ada Produk</h4>
                    <p class="text-muted">Toko ini belum menambahkan produk apapun.</p>
                </div>
            @endif
        </div>
    </section>
@endif
@endsection