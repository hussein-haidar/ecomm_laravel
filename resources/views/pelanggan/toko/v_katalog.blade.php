@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero carousel auto-slide
        const carousel = document.getElementById('promoCarousel');
        if (carousel) {
            const bsCarousel = new bootstrap.Carousel(carousel, {
                interval: 5000,
                ride: 'carousel',
                pause: 'hover',
                wrap: true
            });
        }

        // Filter form auto-submit on change (for select inputs)
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.querySelectorAll('select, input[type="checkbox"]').forEach(el => {
                el.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        }

        // Price range validation
        const hargaMin = document.querySelector('input[name="harga_min"]');
        const hargaMax = document.querySelector('input[name="harga_max"]');
        if (hargaMin && hargaMax) {
            hargaMax.addEventListener('input', function() {
                if (parseInt(this.value) < parseInt(hargaMin.value || 0)) {
                    hargaMin.value = this.value;
                }
            });
            hargaMin.addEventListener('input', function() {
                if (parseInt(this.value) > parseInt(hargaMax.value || 999999999)) {
                    hargaMax.value = this.value;
                }
            });
        }

        // Rating filter: ensure only one checkbox selected per row? Actually allow multiple (min rating)
        const ratingInputs = document.querySelectorAll('input[name="rating[]"]');
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                // If a higher star is checked, check all lower ones too (min rating logic)
                if (this.checked) {
                    const val = parseInt(this.value);
                    ratingInputs.forEach(other => {
                        if (parseInt(other.value) <= val) {
                            other.checked = true;
                        }
                    });
                } else {
                    // If unchecking, uncheck all higher stars
                    const val = parseInt(this.value);
                    ratingInputs.forEach(other => {
                        if (parseInt(other.value) > val) {
                            other.checked = false;
                        }
                    });
                }
            });
        });
    });
</script>
@endpush

@section('content')
<!-- Hero/Promo Section -->
@if(!empty($data_carousel) && count($data_carousel) > 0)
    <section class="promo-section position-relative" aria-label="Promo Banner">
        <div class="container">
            <div id="promoCarousel" class="carousel slide rounded-4 overflow-hidden shadow-lg" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
                <div class="carousel-inner">
                    @php $slideIndex = 0; @endphp
                    @foreach($data_carousel as $promo)
                        @foreach($promo->all_images as $image)
                            <div class="carousel-item {{ $slideIndex === 0 ? 'active' : '' }}" data-aos="fade-up" data-aos-duration="600">
                                <img src="{{ asset($image) }}" class="d-block w-100" alt="{{ $promo->nama_promo }}" style="height: 400px; object-fit: cover;">
                                <div class="carousel-caption d-none d-md-block text-start" style="bottom: 15%;">
                                    <div class="bg-dark bg-opacity-75 rounded-4 p-4" style="max-width: 90%;">
                                        <span class="badge bg-danger mb-2 px-3 py-1">{{ $promo->nama_promo }}</span>
                                        <h4 class="fw-bold mb-2">{{ $promo->deskripsi_promo ?? 'Promo Spesial' }}</h4>
                                        <a href="{{ route('home_toko.katalog') }}" class="btn btn-light btn-sm rounded-pill px-4">
                                            <i class="fas fa-shopping-bag me-1"></i>Belanja Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @php $slideIndex++; @endphp
                        @endforeach
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev" aria-label="Previous">
                    <span class="carousel-control-prev-icon bg-primary rounded-circle p-3" style="background-size: 60%;"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next" aria-label="Next">
                    <span class="carousel-control-next-icon bg-primary rounded-circle p-3" style="background-size: 60%;"></span>
                </button>
                <div class="carousel-indicators">
                    @for($i = 0; $i < $slideIndex; $i++)
                        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                    @endfor
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Filter & Product Section -->
<section class="py-4" aria-labelledby="katalog-title">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Filter (Desktop) -->
            <aside class="col-lg-3 d-none d-lg-block" data-aos="fade-right">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 100;">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Produk</h5>
                        <a href="{{ route('home_toko.katalog') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ route('home_toko.katalog') }}">
                            <div class="mb-4">
                                <label class="form-label fw-medium">Cari Produk</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Cari..." value="{{ request('keyword') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="jenis_produk" class="form-label fw-medium">Kategori</label>
                                <select name="jenis_produk" id="jenis_produk" class="form-select form-select-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach($jenis_produk_dropdown ?? [] as $jenis)
                                        <option value="{{ $jenis['jenis_produk'] }}" {{ request('jenis_produk') == $jenis['jenis_produk'] ? 'selected' : '' }}>
                                            {{ $jenis['jenis_produk'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Rentang Harga</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_min" class="form-control" placeholder="Min" value="{{ request('harga_min') }}" min="0">
                                    <span class="input-group-text">-</span>
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_max" class="form-control" placeholder="Max" value="{{ request('harga_max') }}" min="0">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Rating / Ulasan</label>
                                <div class="d-flex flex-column gap-1">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="form-check form-check-sm">
                                            <input class="form-check-input" type="checkbox" name="rating[]" value="{{ $i }}" id="rating_{{ $i }}" {{ in_array((string)$i, request('rating', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small text-warning" for="rating_{{ $i }}">
                                                @for($j = 1; $j <= $i; $j++) <i class="fas fa-star"></i> @endfor
                                                {{ $i }}+ bintang
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Urutkan</label>
                                <select name="sort_harga" class="form-select form-select-sm mb-2">
                                    <option value="" {{ request('sort_harga') == '' && request('sort_nama') == '' ? 'selected' : '' }}>Default</option>
                                    <option value="asc" {{ request('sort_harga') == 'asc' ? 'selected' : '' }}>Harga Terendah</option>
                                    <option value="desc" {{ request('sort_harga') == 'desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                </select>
                                <select name="sort_nama" class="form-select form-select-sm">
                                    <option value="" {{ request('sort_nama') == '' && request('sort_harga') == '' ? 'selected' : '' }}>Nama A-Z</option>
                                    <option value="a-z" {{ request('sort_nama') == 'a-z' ? 'selected' : '' }}>Nama A - Z</option>
                                    <option value="z-a" {{ request('sort_nama') == 'z-a' ? 'selected' : '' }}>Nama Z - A</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class="fas fa-filter me-1"></i>Terapkan Filter
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Products Area -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4" data-aos="fade-up">
                    <div>
                        <h2 id="katalog-title" class="fw-bold mb-1 text-dark">Katalog Produk</h2>
                        <p class="text-muted mb-0">
                            @php
                                $totalProduk = count($produk_data ?? []);
                                $filters = [];
                                if(request('keyword')) $filters[] = 'kata kunci: "'.request('keyword').'"';
                                if(request('jenis_produk')) $filters[] = 'kategori: "'.request('jenis_produk').'"';
                                if(request('harga_min') || request('harga_max')) $filters[] = 'harga: '.(request('harga_min') ? 'Rp'.number_format(request('harga_min'),0,',','.') : '').' - '.(request('harga_max') ? 'Rp'.number_format(request('harga_max'),0,',','.') : '');
                                if(request('rating') && count(request('rating'))) {
                                    $minRating = min(array_map('intval', request('rating')));
                                    $filters[] = 'rating minimal: '.$minRating.'+ bintang';
                                }
                            @endphp
                            Menampilkan <strong>{{ $totalProduk }}</strong> produk
                            @if(!empty($filters))
                                ({{ implode(', ', $filters) }})
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-start-pill {{ $viewMode == 'grid' ? 'active' : '' }}" onclick="window.location='{{ route('home_toko.katalog', array_merge(request()->all(), ['view' => 'grid'])) }}'" aria-label="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-end-pill {{ $viewMode == 'list' ? 'active' : '' }}" onclick="window.location='{{ route('home_toko.katalog', array_merge(request()->all(), ['view' => 'list'])) }}'" aria-label="List View">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Grid / List -->
                @if($viewMode == 'list')
                    <div class="list-view" id="productList">
                        @if(empty($produk_data))
                            <div class="col-12 text-center py-5" data-aos="fade-up">
                                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak Ada Produk</h4>
                                <p class="text-muted">Tidak ditemukan produk yang sesuai dengan kriteria pencarian Anda.</p>
                                <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                    <i class="fas fa-arrow-left me-1"></i>Lihat Semua Produk
                                </a>
                            </div>
                        @else
                            @foreach($produk_data as $produk)
                                @include('layouts.v_produk_item_list', ['produk' => $produk, 'loop' => $loop])
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="row g-4" id="productGrid">
                        @if(empty($produk_data))
                            <div class="col-12 text-center py-5" data-aos="fade-up">
                                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak Ada Produk</h4>
                                <p class="text-muted">Tidak ditemukan produk yang sesuai dengan kriteria pencarian Anda.</p>
                                <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                    <i class="fas fa-arrow-left me-1"></i>Lihat Semua Produk
                                </a>
                            </div>
                        @else
                            @foreach($produk_data as $produk)
                                @include('layouts.v_produk_item', ['produk' => $produk, 'loop' => $loop])
                            @endforeach
                        @endif
                    </div>
                @endif

                <!-- Pagination -->
                @if($produk_data instanceof \Illuminate\Pagination\LengthAwarePaginator && $produk_data->lastPage() > 1)
                    <nav aria-label="Pagination" class="mt-5" data-aos="fade-up">
                        <div class="d-flex justify-content-center">
                            {{ $produk_data->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection