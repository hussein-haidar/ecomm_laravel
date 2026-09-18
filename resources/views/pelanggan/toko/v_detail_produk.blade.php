@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize main carousel
        const mainCarousel = document.getElementById('productCarousel');
        if (mainCarousel) {
            new bootstrap.Carousel(mainCarousel, {
                interval: false,
                ride: false
            });
        }

        // Thumbnail click handler
        document.querySelectorAll('.product-thumb').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const targetSlide = parseInt(this.dataset.slide);
                const carousel = bootstrap.Carousel.getInstance(mainCarousel);
                if (carousel) {
                    carousel.to(targetSlide);
                }
                // Update active thumbnail
                document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Quantity controls
        window.updateQty = function(delta) {
            const input = document.getElementById('jumlah_produk');
            if (!input) return;
            let val = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 99;
            val = Math.max(min, Math.min(val + delta, max));
            input.value = val;
        };

        // Validate quantity input
        document.getElementById('jumlah_produk')?.addEventListener('input', function() {
            let val = parseInt(this.value);
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 99;
            if (isNaN(val) || val < min) this.value = min;
            else if (val > max) this.value = max;
        });

        // Size selection for buy now
        window.beliLangsung = function(idStok) {
            const form = document.getElementById('product-form-' + idStok);
            if (!form) {
                showToast('Form produk tidak ditemukan', 'error');
                return;
            }

            const sizeSelect = form.querySelector('select[name="ukuran_produk"]');
            if (sizeSelect && sizeSelect.required && !sizeSelect.value) {
                showToast('Silakan pilih ukuran produk terlebih dahulu', 'error');
                sizeSelect.focus();
                return;
            }

            const qtyInput = form.querySelector('input[name="jumlah_produk"]');
            const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
            const size = sizeSelect ? sizeSelect.value : '';
            const namaToko = form.querySelector('input[name="nama_toko"]')?.value || '';

            const urlTemplate = "{{ route('pelanggan_data.beliLangsung', ['id_stok' => '__ID__']) }}";
            const url = urlTemplate.replace('__ID__', idStok);

            const params = new URLSearchParams({
                ukuran_produk: size,
                jumlah_produk: qty,
                nama_toko: namaToko
            });

            window.location.href = url + '?' + params.toString();
        };

        // Share buttons
        document.querySelectorAll('[data-share]').forEach(btn => {
            btn.addEventListener('click', function() {
                const platform = this.dataset.share;
                const url = encodeURIComponent(window.location.href);
                const text = encodeURIComponent('{{ $produk['nama_produk'] ?? '' }}');
                
                let shareUrl = '';
                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
                        break;
                    case 'whatsapp':
                        shareUrl = `https://wa.me/?text=${text}%20${url}`;
                        break;
                    case 'copy':
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            showToast('Link disalin ke clipboard!', 'success');
                        });
                        return;
                }
                window.open(shareUrl, '_blank', 'width=600,height=400');
            });
        });

        // Review filter
        document.querySelectorAll('.btn-filter-rating').forEach(btn => {
            btn.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating) || 0;
                document.querySelectorAll('.btn-filter-rating').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.review-item').forEach(item => {
                    const itemRating = parseInt(item.dataset.rating) || 0;
                    item.style.display = (rating === 0 || itemRating === rating) ? '' : 'none';
                });
                
                const emptyMsg = document.getElementById('review-empty-filter');
                if (emptyMsg) {
                    const visible = document.querySelectorAll('.review-item[style=""]').length;
                    emptyMsg.style.display = visible === 0 ? '' : 'none';
                }
            });
        });
    });

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow-lg`;
        toast.style.borderRadius = '15px';
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush

@section('content')
@php
    $ukuranList = $produk['ukuran_list'] ?? [];
    $hasMultipleSizes = is_array($ukuranList) && count($ukuranList) > 1;
@endphp
@if(empty($produk))
    <div class="container py-5">
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h4>Produk Tidak Ditemukan</h4>
            <p class="text-muted">Produk yang Anda cari tidak tersedia atau telah dihapus.</p>
            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Katalog
            </a>
        </div>
    </div>
@else
    @php
        $gallery = [];
        if (!empty($produk['gallery_images'])) {
            $gallery = json_decode($produk['gallery_images'], true) ?? [];
        }
        $allImages = array_merge([$produk['foto_produk']], $gallery);
        $rating = round($produk['rata_rating'] ?? 0, 1);
        $totalUlasan = $produk['jumlah_ulasan'] ?? 0;
        $isFlash = $produk['harga_flash'] ?? false;
        $stok = $produk['jumlah_stok_produk'] ?? 0;
        $stokClass = $stok <= 0 ? 'text-danger' : ($stok <= 5 ? 'text-warning' : 'text-success');
        $stokText = $stok <= 0 ? 'Habis' : ($stok <= 5 ? "Sisa $stok" : 'Tersedia');
        $ukuranList = $produk['ukuran_list'] ?? [];
    @endphp

    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-4" data-aos="fade-down">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('home_toko.katalog') }}">Katalog</a></li>
                <li class="breadcrumb-item"><a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}">{{ $produk['nama_toko'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $produk['nama_produk'] }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            {{-- Product Gallery --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="card border-0 shadow-sm overflow-hidden">
                    {{-- Main Carousel --}}
                    <div id="productCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner rounded-top">
                            @foreach($allImages as $index => $img)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('fotoproduk/' . $img) }}" class="d-block w-100" alt="{{ $produk['nama_produk'] }} - Gambar {{ $index + 1 }}" style="height: 500px; object-fit: contain; background: #f8f9fa;">
                                </div>
                            @endforeach
                        </div>
                        @if(count($allImages) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev" aria-label="Previous">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" style="background-size: 60%;"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next" aria-label="Next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3" style="background-size: 60%;"></span>
                            </button>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    @if(count($allImages) > 1)
                        <div class="product-thumbnails d-flex gap-2 justify-content-center flex-wrap p-3 bg-light border-top">
                            @foreach($allImages as $index => $img)
                                <button type="button" class="product-thumb {{ $index === 0 ? 'active' : '' }} border-0 p-1" 
                                        data-slide="{{ $index }}" 
                                        aria-label="Gambar {{ $index + 1 }}"
                                        style="width: 70px; height: 70px; border-radius: 8px; overflow: hidden; transition: all 0.3s ease;">
                                    <img src="{{ asset('fotoproduk/' . $img) }}" alt="Thumbnail {{ $index + 1 }}" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Additional Actions --}}
                    <div class="p-3 bg-light border-top d-flex flex-wrap gap-2">
                        @if($produk['video_url'] ?? false)
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-share="video" onclick="playVideo('{{ $produk['video_url'] }}')">
                                <i class="fas fa-play-circle me-2"></i>Video Produk
                            </button>
                        @endif
                        @if($produk['size_guide_image'] ?? false)
                            <button type="button" class="btn btn-outline-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                                <i class="fas fa-ruler-combined me-2"></i>Panduan Ukuran
                            </button>
                        @endif
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-share="facebook" aria-label="Bagikan ke Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button type="button" class="btn btn-outline-info rounded-pill px-3" data-share="twitter" aria-label="Bagikan ke Twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success rounded-pill px-3" data-share="whatsapp" aria-label="Bagikan ke WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-share="copy" aria-label="Salin Link">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6" data-aos="fade-left">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            {{-- Store Badge --}}
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}" class="text-decoration-none text-muted small" target="_blank">
                                    <i class="fas fa-store text-primary me-1"></i>{{ $produk['nama_toko'] }}
                                </a>
                                @if($produk['jenis_produk'] ?? false)
                                    <span class="badge bg-light text-dark border">{{ $produk['jenis_produk'] }}</span>
                                @endif
                            </div>

                            {{-- Product Name --}}
                            <h2 class="fw-bold mb-3">{{ $produk['nama_produk'] }}</h2>

                            {{-- Rating --}}
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="fw-bold">{{ number_format($rating, 1) }}</span>
                                <span class="text-muted">({{ $totalUlasan }} Ulasan)</span>
                            </div>

                            {{-- Price --}}
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                                @if($isFlash)
                                    <span class="text-danger fw-bold fs-2">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                                    @if(isset($produk['harga_normal']))
                                        <span class="text-muted text-decoration-line-through">Rp{{ number_format($produk['harga_normal'], 0, ',', '.') }}</span>
                                        <span class="badge bg-danger rounded-pill px-3 py-2">{{ $produk['diskon_persen'] ?? 0 }}% OFF</span>
                                    @endif
                                @else
                                    <span class="text-primary fw-bold fs-2">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                                @endif
                            </div>

                            {{-- Flash Sale Badge --}}
                            @if($isFlash)
                                <div class="alert alert-warning border-0 rounded-3 py-2 mb-4" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-bolt text-warning fa-2x"></i>
                                        <div>
                                            <div class="fw-bold">FLASH SALE</div>
                                            <small class="text-muted">Harga spesial terbatas waktu & kuota</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Product Specs --}}
                            <div class="mb-4">
                                <ul class="list-unstyled">
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-box-open text-success"></i>
                                        <span class="{{ $stokClass }} fw-medium">
                                            Stok: <span id="detail-stok">{{ $stokText }}</span> {{ $produk['satuan_produk'] ?? 'pcs' }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        Dikirim dari: <strong>{{ $produk['nama_toko'] }}</strong>
                                    </li>
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-weight-hanging text-primary"></i>
                                        Berat: {{ $produk['berat_produk'] ?? 0 }} {{ $produk['satuan_berat'] ?? 'gram' }}
                                    </li>
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-shield-alt text-info"></i>
                                        Asuransi Pengiriman: Opsional
                                    </li>
                                </ul>
                            </div>

                            {{-- Description --}}
                            @if($produk['deskripsi_produk'] ?? false)
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-2">Deskripsi Produk</h6>
                                    <div class="text-muted small line-clamp-5" style="max-height: 200px; overflow: hidden;">
                                        {{ $produk['deskripsi_produk'] }}
                                    </div>
                                </div>
                            @endif

                            {{-- Size Guide --}}
                            @if($hasMultipleSizes)
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-2">Ukuran Tersedia</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($ukuranList as $uk)
                                            <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.85rem;">{{ $uk }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Buy Form --}}
                            @if($stok > 0)
                                @if(session('user_logged_in'))
                                    <form action="{{ route('pelanggan_data.add_to_cart') }}" method="POST" id="product-form-{{ $produk['id_stok'] }}">
                                        @csrf
                                        <input type="hidden" name="id_stok" value="{{ $produk['id_stok'] }}">
                                        <input type="hidden" name="nama_produk" value="{{ $produk['nama_produk'] }}">
                                        <input type="hidden" name="satuan_produk" value="{{ $produk['satuan_produk'] ?? 'pcs' }}">
                                        <input type="hidden" name="harga_produk" value="{{ $produk['harga_produk'] }}">
                                        <input type="hidden" name="berat_produk" value="{{ $produk['berat_produk'] ?? 0 }}">
                                        <input type="hidden" name="satuan_berat" value="{{ $produk['satuan_berat'] ?? 'gram' }}">
                                        <input type="hidden" name="sesi_user" value="{{ $produk['sesi_user'] }}">
                                        <input type="hidden" name="nama_toko" value="{{ $produk['nama_toko'] }}">

                                        {{-- Size Selector --}}
                                        @if($hasMultipleSizes)
                                            <div class="mb-3">
                                                <label for="ukuran_produk_{{ $produk['id_stok'] }}" class="form-label fw-medium">Pilih Ukuran <span class="text-danger">*</span></label>
                                                <select id="ukuran_produk_{{ $produk['id_stok'] }}" name="ukuran_produk" class="form-select" required>
                                                    <option value="">-- Pilih Ukuran --</option>
                                                    @foreach($ukuranList as $ukuran)
                                                        <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(count($ukuranList) === 1)
                                            <input type="hidden" name="ukuran_produk" value="{{ $ukuranList[0] }}">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Ukuran</label>
                                                <div class="form-control-plaintext fw-bold">{{ $ukuranList[0] }}</div>
                                            </div>
                                        @endif

                                        {{-- Quantity --}}
                                        <div class="mb-3">
                                            <label for="jumlah_produk" class="form-label fw-medium">Jumlah <span class="text-danger">*</span></label>
                                            <div class="input-group" style="max-width: 180px;">
                                                <button type="button" class="btn btn-outline-secondary btn-qty" onclick="updateQty(-1)" aria-label="Kurangi">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" id="jumlah_produk" name="jumlah_produk"
                                                       min="1" value="1" max="{{ $stok }}"
                                                       class="form-control text-center" 
                                                       oninput="validateQty(this)"
                                                       aria-label="Jumlah">
                                                <button type="button" class="btn btn-outline-secondary btn-qty" onclick="updateQty(1)" aria-label="Tambah">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill py-3 fw-semibold" title="Tambah ke Keranjang">
                                                <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
                                            </button>
                                            <button type="button" class="btn btn-success rounded-pill py-3 px-5 fw-semibold btn-buy-now" 
                                                    onclick="beliLangsung('{{ $produk['id_stok'] }}')"
                                                    title="Beli Sekarang">
                                                <i class="fas fa-bolt me-2"></i>Beli Sekarang
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-primary flex-grow-1 rounded-pill py-3 fw-semibold" title="Login untuk beli">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login untuk Beli
                                        </a>
                                        <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-outline-primary rounded-pill py-3 px-4 fw-semibold" title="Daftar">
                                            <i class="fas fa-user-plus me-2"></i>Daftar
                                        </a>
                                    </div>
                                @endif
                            @else
                                <button type="button" class="btn btn-secondary w-100 rounded-pill py-3 fw-semibold" disabled>
                                    <i class="fas fa-ban me-2"></i>Stok Habis
                                </button>
                            @endif

                            {{-- Trust Badges --}}
                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-around text-center small">
                                    <div>
                                        <i class="fas fa-truck-fast text-success mb-1"></i>
                                        <p class="mb-0 fw-medium">Pengiriman Cepat</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-shield-alt text-primary mb-1"></i>
                                        <p class="mb-0 fw-medium">Garansi Resmi</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-rotate-left text-danger mb-1"></i>
                                        <p class="mb-0 fw-medium">Retur Mudah</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Store Info & Reviews --}}
        <div class="row g-4 mt-5">
            {{-- Store Info --}}
            <div class="col-lg-5" data-aos="fade-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="fw-bold mb-0"><i class="fas fa-store me-2 text-primary"></i>Informasi Penjual</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}" class="text-decoration-none text-dark d-flex align-items-center mb-3" target="_blank">
                            @if($produk['logo_website'] ?? false)
                                <img src="{{ asset('logo_website/' . $produk['logo_website']) }}" alt="Logo Toko" class="me-3 rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="me-3 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold">{{ $produk['nama_toko'] }}</div>
                                <small class="text-muted">Toko Terverifikasi</small>
                            </div>
                        </a>

                        @if($ringkasan_toko ?? false)
                            @php
                                $toko_rating = $ringkasan_toko->rata ?? 0;
                                $toko_total = $ringkasan_toko->total ?? 0;
                                $toko_rekomendasi = (int) ($ringkasan_toko->rekomendasi ?? 0);
                                $persen_toko_rekomendasi = $toko_total > 0 ? round($toko_rekomendasi / $toko_total * 100) : 0;
                            @endphp
                            <div class="p-3 bg-light rounded-3 mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($toko_rating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <span class="fw-bold">{{ number_format($toko_rating, 1) }}</span>
                                    <span class="text-muted ms-1">({{ $toko_total }} Ulasan)</span>
                                </div>
                                @if($toko_total > 0)
                                    <p class="text-success small mb-0">
                                        <i class="fas fa-thumbs-up me-1"></i>{{ $persen_toko_rekomendasi }}% pembeli merekomendasikan toko ini
                                    </p>
                                @endif
                            </div>
                        @endif

                        <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $produk['alamat_pusat'] ?? '-' }}</p>
                        <p class="text-muted small mb-3"><i class="fas fa-shipping-fast me-2"></i>Estimasi Pengiriman: 1-3 Hari Kerja</p>

                        @if(session('user_logged_in'))
                            <a href="javascript:void(0)" onclick="window.bukaChat && bukaChat('{{ $produk['nama_toko'] }}', '{{ $produk['id_stok'] }}')" class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="fas fa-comments me-2"></i>Chat Penjual
                            </a>
                        @else
                            <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="fas fa-comments me-2"></i>Chat Penjual
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="fw-bold mb-0"><i class="fas fa-comments me-2 text-primary"></i>Ulasan Pembeli</h5>
                    </div>
                    <div class="card-body">
                        @if($totalUlasan > 0)
                            {{-- Rating Summary --}}
                            <div class="d-flex align-items-center gap-4 pb-3 mb-3 border-bottom">
                                <div class="text-center" style="min-width: 100px;">
                                    <span class="fw-bold fs-1 lh-1 text-dark">{{ number_format($rating, 1, ',', '.') }}</span>
                                    <span class="text-muted">/ 5</span>
                                    <div class="text-warning small mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($rating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $totalUlasan }} ulasan pembeli</div>
                                    <div class="text-success small">
                                        <i class="fas fa-thumbs-up me-1"></i>{{ $ringkasan_ulasan->rekomendasi ?? 0 }}% merekomendasikan
                                    </div>
                                </div>
                            </div>

                            {{-- Rating Distribution --}}
                            @php
                                $distribusi = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                                $ulasan = $ulasan ?? [];
                                foreach($ulasan as $r) {
                                    if(isset($distribusi[$r['rating']])) $distribusi[$r['rating']]++;
                                }
                            @endphp
                            <div class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="small text-muted">Filter:</span>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-filter-rating active" data-rating="0">Semua</button>
                                    @foreach([5, 4, 3, 2, 1] as $bintang)
                                        @if(($distribusi[$bintang] ?? 0) > 0)
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-filter-rating" data-rating="{{ $bintang }}">
                                                {{ $bintang }} <i class="fas fa-star text-warning fa-xs"></i>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                                @foreach([5, 4, 3, 2, 1] as $bintang)
                                    @php
                                        $jml = $distribusi[$bintang];
                                        $persen = $totalUlasan > 0 ? round($jml / $totalUlasan * 100) : 0;
                                    @endphp
                                    <button type="button" class="btn btn-filter-rating d-flex align-items-center gap-2 mb-1 w-100 text-start p-1 border-0 bg-transparent" data-rating="{{ $bintang }}">
                                        <span class="small text-nowrap" style="width: 40px;">{{ $bintang }} <i class="fas fa-star text-warning fa-xs"></i></span>
                                        <div class="progress flex-grow-1" style="height: 6px; margin-bottom: 0;">
                                            <div class="progress-bar bg-warning" style="width: {{ $persen }}%"></div>
                                        </div>
                                        <span class="small text-muted text-nowrap" style="width: 70px; text-align: right;">{{ $jml }} ({{ $persen }}%)</span>
                                    </button>
                                @endforeach
                            </div>

                            {{-- Review List --}}
                            <div id="review-list">
                                @foreach($ulasan as $review)
                                    <div class="border-top pt-3 mb-3 review-item" data-rating="{{ $review['rating'] }}">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px;">
                                                @php
                                                    $parts = preg_split('/\s+/', trim($review['nama_user']));
                                                    $init = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
                                                    if(isset($parts[1])) $init .= mb_strtoupper(mb_substr($parts[1], 0, 1));
                                                @endphp
                                                {{ $init }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1">
                                                    <div>
                                                        <span class="fw-semibold">{{ $review['nama_user'] }}</span>
                                                        @if(!empty($review['terverifikasi']))
                                                            <span class="badge bg-success ms-1"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($review['tanggal_ulasan'])->translatedFormat('d F Y') }}</small>
                                                </div>
                                                <div class="small text-warning my-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="{{ $i <= $review['rating'] ? 'fas' : 'far' }} fa-star"></i>
                                                    @endfor
                                                    <span class="text-muted ms-1">{{ number_format($review['rating'], 1) }}</span>
                                                </div>
                                                <p class="mb-0">{{ $review['komentar'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="review-empty-filter" class="text-center text-muted py-4" style="display:none;">
                                <i class="fas fa-star-half-alt me-1"></i>Tidak ada ulasan dengan rating ini.
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Ulasan</h5>
                                <p class="text-muted">Jadilah yang pertama memberi ulasan untuk produk ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Size Guide Modal --}}
        @if($produk['size_guide_image'] ?? false)
            <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold" id="sizeGuideModalLabel"><i class="fas fa-ruler-combined me-2 text-success"></i>Panduan Ukuran - {{ $produk['nama_produk'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="{{ asset('fotoproduk/' . $produk['size_guide_image']) }}" alt="Size Guide" class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Video Modal --}}
        @if($produk['video_url'] ?? false)
            <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold" id="videoModalLabel"><i class="fas fa-play-circle me-2 text-primary"></i>Video Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0" id="videoModalBody"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
@endsection