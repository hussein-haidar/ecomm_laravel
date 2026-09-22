@extends('layouts.app')
@push('scripts')
<script>
    // Hero carousel auto-slide
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('heroCarousel');
        if (carousel) {
            const bsCarousel = new bootstrap.Carousel(carousel, {
                interval: 5000,
                ride: 'carousel',
                pause: 'hover',
                wrap: true
            });
        }

        // Counter animation for stats
        const counters = document.querySelectorAll('.counter');
        const observerOptions = { threshold: 0.5 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.dataset.target);
                    let current = 0;
                    const increment = target / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target.toLocaleString('id-ID') + (counter.dataset.suffix || '');
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current).toLocaleString('id-ID') + (counter.dataset.suffix || '');
                        }
                    }, 40);
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);
        counters.forEach(counter => observer.observe(counter));

        // Product card hover effect
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
                this.style.boxShadow = '0 15px 35px rgba(102, 126, 234, 0.2)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.08)';
            });
        });
    });
</script>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section position-relative" aria-labelledby="hero-title">
    <div class="container">
        <div class="row align-items-center py-5" style="min-height: 70vh;">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                <div class="hero-content text-white">
                    <h1 id="hero-title" class="display-4 fw-bold mb-4" style="line-height: 1.2;">
                        {{ $dataWebsite['nama_toko'] }}
                        @if($dataWebsite['tagline'])
                            <br><span style="font-size: 0.6em; font-weight: 400; opacity: 0.9;">{{ $dataWebsite['tagline'] }}</span>
                        @endif
                    </h1>
                    <p class="lead mb-4" style="opacity: 0.95; max-width: 90%;">
                        {{ $dataWebsite['deskripsi_toko'] ?? 'Temukan produk berkualitas terbaik dengan harga terjangkau. Pengiriman cepat & aman ke seluruh Indonesia.' }}
                    </p>
                    <div class="d-flex gap-3 flex-wrap mb-5">
                        <a href="{{ route('home_toko.katalog') }}" class="btn btn-light btn-lg rounded-pill px-5 shadow-lg fw-semibold">
                            <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                        </a>
                        <a href="#produk-unggulan" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-semibold">
                            <i class="fas fa-arrow-down me-2"></i>Lihat Produk
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row g-4 mt-4">
                        <div class="col-4 text-center">
                            <div class="stat-item p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                <div class="fs-2 fw-bold counter text-white" data-target="{{ $stats['total_produk'] ?? 500 }}" data-suffix="+"></div>
                                <p class="small mb-0 text-white-50">Produk Tersedia</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-item p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                <div class="fs-2 fw-bold counter text-white" data-target="{{ $stats['total_toko'] ?? 50 }}" data-suffix="+"></div>
                                <p class="small mb-0 text-white-50">Toko Mitra</p>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-item p-3 rounded-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                                <div class="fs-2 fw-bold counter text-white" data-target="{{ $stats['total_pelanggan'] ?? 10000 }}" data-suffix="+"></div>
                                <p class="small mb-0 text-white-50">Pelanggan Puas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                <div class="hero-image position-relative" style="height: 500px;">
                    @if($data_carousel && $data_carousel->count() > 0)
                        <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
                            <div class="carousel-inner h-100 rounded-4 overflow-hidden shadow-lg">
                                @php $slideIndex = 0; @endphp
                                @foreach($data_carousel as $promo)
                                    @foreach($promo->all_images as $image)
                                        <div class="carousel-item h-100 {{ $slideIndex === 0 ? 'active' : '' }}" data-aos="zoom-in" data-aos-duration="600">
                                            <img src="{{ asset($image) }}" class="d-block w-100 h-100" alt="{{ $promo->nama_promo }}" style="object-fit: cover;">
                                            <div class="carousel-caption d-none d-md-block text-start" style="bottom: 20%;">
                                                <div class="bg-dark bg-opacity-75 rounded-4 p-4" style="max-width: 80%;">
                                                    <span class="badge bg-danger mb-2">{{ $promo->nama_promo }}</span>
                                                    <h4 class="fw-bold mb-2">{{ $promo->deskripsi_promo ?? 'Promo Spesial' }}</h4>
                                                    <a href="{{ route('home_toko.katalog') }}" class="btn btn-light btn-sm rounded-pill">
                                                        <i class="fas fa-arrow-right me-1"></i>Belanja Sekarang
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @php $slideIndex++; @endphp
                                    @endforeach
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous">
                                <span class="carousel-control-prev-icon bg-primary rounded-circle p-3" style="background-size: 60%;"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next">
                                <span class="carousel-control-next-icon bg-primary rounded-circle p-3" style="background-size: 60%;"></span>
                            </button>
                            <div class="carousel-indicators">
                                @for($i = 0; $i < $slideIndex; $i++)
                                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                                @endfor
                            </div>
                        </div>
                    @else
                        <!-- Default Hero Image -->
                        <div class="h-100 rounded-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                            <div class="position-relative h-100 d-flex flex-column justify-content-center align-items-center text-white p-5">
                                <i class="fas fa-store fa-5x mb-4 opacity-50"></i>
                                <h3 class="fw-bold mb-3">Selamat Datang di {{ $dataWebsite['nama_toko'] }}</h3>
                                <p class="lead opacity-75 mb-4">Belanja mudah, cepat, & aman</p>
                                <a href="{{ route('home_toko.katalog') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-semibold">
                                    <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="py-4 bg-white border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="p-3">
                    <div class="text-primary mb-2"><i class="fas fa-truck-fast fa-2x"></i></div>
                    <h6 class="fw-bold mb-1">Gratis Ongkir</h6>
                    <p class="text-muted small mb-0">Min. belanja Rp500.000</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="p-3">
                    <div class="text-primary mb-2"><i class="fas fa-shield-alt fa-2x"></i></div>
                    <h6 class="fw-bold mb-1">Garansi Resmi</h6>
                    <p class="text-muted small mb-0">Produk 100% Original</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="p-3">
                    <div class="text-primary mb-2"><i class="fas fa-rotate-left fa-2x"></i></div>
                    <h6 class="fw-bold mb-1">Retur Mudah</h6>
                    <p class="text-muted small mb-0">7 Hari Garansi Retur</p>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="p-3">
                    <div class="text-primary mb-2"><i class="fas fa-headset fa-2x"></i></div>
                    <h6 class="fw-bold mb-1">Support 24/7</h6>
                    <p class="text-muted small mb-0">Live Chat & WhatsApp</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="produk-unggulan" class="py-5" aria-labelledby="produk-title">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <div>
                <h2 id="produk-title" class="fw-bold mb-1 text-dark">Produk Unggulan</h2>
                <p class="text-muted mb-0">Produk terlaris & paling dicari hari ini</p>
            </div>
            <a href="{{ route('home_toko.katalog') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-arrow-right me-1"></i>Lihat Semua
            </a>
        </div>

        <div class="row g-4" id="productGrid">
            @foreach(array_slice($produk_data ?? [], 0, 8) as $produk)
                <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <article class="product-card card h-100 shadow-sm border-0 overflow-hidden" style="transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        @if(!empty($produk['badge']))
                            <span class="badge {{ $produk['badge_class'] ?? 'bg-danger' }} position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                                {{ $produk['badge'] }}
                            </span>
                        @endif
                        
                        <div class="img-container position-relative bg-light" style="overflow: hidden;">
                            <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" aria-label="Lihat detail {{ $produk['nama_produk'] }}">
                                <img src="{{ asset('fotoproduk/' . ($produk['foto_produk'] ?? 'default.jpg')) }}" 
                                     class="card-img-top img-fluid w-100" 
                                     alt="{{ $produk['nama_produk'] }}"
                                     loading="lazy">
                            </a>
                            @if($produk['harga_flash'] ?? false)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger px-3 py-1 rounded-pill" style="font-size: 0.7rem;">
                                        <i class="fas fa-bolt me-1"></i>FLASH SALE
                                    </span>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-gradient" style="background: linear-gradient(transparent, rgba(0,0,0,0.7)); opacity: 0; transition: opacity 0.3s ease;">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" class="btn btn-light btn-sm rounded-pill px-3" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(session('user_logged_in'))
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addToCartQuick({{ $produk['id_stok'] }})" title="Tambah ke Keranjang">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="window.bukaChat && bukaChat('{{ $produk['nama_toko'] }}', '{{ $produk['id_stok'] }}')" title="Chat Penjual">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-light btn-sm rounded-pill px-3" title="Login untuk beli">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title fw-bold mb-1 text-truncate" style="font-size: 0.95rem;">
                                <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" class="text-dark text-decoration-none">
                                    {{ $produk['nama_produk'] }}
                                </a>
                            </h6>

                            <!-- Rating -->
                            <div class="mb-2">
                                @php $rating = round($produk['rata_rating'] ?? 0, 1); @endphp
                                <div class="text-warning small mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="text-muted ms-1">({{ number_format($rating, 1) }})</span>
                                </div>
                            </div>

                            <!-- Description snippet -->
                            @if(!empty($produk['deskripsi_produk']))
                                <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ \Illuminate\Support\Str::limit($produk['deskripsi_produk'], 90) }}
                                </p>
                            @endif

                            <!-- Price -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if($produk['harga_flash'] ?? false)
                                    <span class="text-danger fw-bold fs-5">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                                    <span class="text-muted text-decoration-line-through small">Rp{{ number_format($produk['harga_normal'] ?? $produk['harga_produk'], 0, ',', '.') }}</span>
                                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.65rem;">{{ $produk['diskon_persen'] ?? 0 }}%</span>
                                @else
                                    <span class="text-primary fw-bold fs-5">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                                @endif
                            </div>

                            <!-- Store Info -->
                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex align-items-center justify-content-between gap-2 small text-muted">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-store text-primary"></i>
                                        <a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}" class="text-decoration-none text-muted fw-medium" target="_blank">
                                            {{ $produk['nama_toko'] }}
                                        </a>
                                    </div>
                                    @if(!empty($produk['total_stok']))
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-box-open me-1"></i>{{ $produk['total_stok'] }} stok
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        @if(empty($produk_data))
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum Ada Produk</h4>
                <p class="text-muted">Produk akan segera hadir. Silakan cek kembali nanti.</p>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="p-5 rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="text-white fw-bold mb-3">Siap Belanja?</h3>
                    <p class="text-white-50 mb-4">Jelajahi ribuan produk berkualitas dari toko-toko terpercaya kami</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('home_toko.katalog') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-semibold">
                            <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                        </a>
                        @if(!session('user_logged_in'))
                            <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-semibold">
                                <i class="fas fa-user-plus me-2"></i>Daftar Gratis
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection