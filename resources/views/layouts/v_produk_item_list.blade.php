@php
    $produk = $produk ?? $data['produk'] ?? [];
    $loop = $loop ?? $data['loop'] ?? new \stdClass();
    $rating = round($produk['rata_rating'] ?? 0, 1);
    $isFlash = $produk['harga_flash'] ?? false;
    $stok = $produk['jumlah_stok_produk'] ?? 0;
    $stokClass = $stok <= 0 ? 'text-danger' : ($stok <= 5 ? 'text-warning' : 'text-success');
    $stokText = $stok <= 0 ? 'Habis' : ($stok <= 5 ? "Sisa $stok" : 'Tersedia');
    $ukuranList = $produk['ukuran_list'] ?? [];
    $hasMultipleSizes = is_array($ukuranList) && count($ukuranList) > 1;
@endphp

<div class="col-12 mb-3" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 8) * 50 }}">
    <article class="product-card card h-100 shadow-sm border-0 overflow-hidden list-item"
             style="transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);"
             data-id-stok="{{ $produk['id_stok'] }}"
             data-nama-produk="{{ $produk['nama_produk'] }}"
             data-nama-toko="{{ $produk['nama_toko'] }}"
             data-harga="{{ $produk['harga_produk'] }}"
             data-stok="{{ $stok }}">

        {{-- Badge --}}
        @if(!empty($produk['badge']))
            <span class="badge {{ $produk['badge_class'] ?? 'bg-danger' }} position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                {{ $produk['badge'] }}
            </span>
        @elseif($isFlash)
            <span class="badge bg-danger position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                <i class="fas fa-bolt me-1"></i>FLASH SALE
            </span>
        @elseif($stok <= 0)
            <span class="badge bg-secondary position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                <i class="fas fa-times-circle me-1"></i>HABIS
            </span>
        @elseif($stok <= 5)
            <span class="badge bg-warning text-dark position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                <i class="fas fa-exclamation-triangle me-1"></i>SISA {{ $stok }}
            </span>
        @endif

        <div class="row g-0">
            {{-- Product Image (Left) --}}
            <div class="col-12 col-md-4 col-lg-3">
                <div class="img-container position-relative bg-light h-100" style="overflow: hidden;">
                    <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" aria-label="Lihat detail {{ $produk['nama_produk'] }}">
                        <img src="{{ asset('fotoproduk/' . ($produk['foto_produk'] ?? 'default.jpg')) }}"
                             class="card-img-top img-fluid w-100 h-100"
                             alt="{{ $produk['nama_produk'] }}"
                             style="transition: transform 0.4s ease; object-fit: cover;"
                             loading="lazy">
                    </a>

                    {{-- Quick Actions Overlay --}}
                    <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-gradient" style="background: linear-gradient(transparent, rgba(0,0,0,0.8)); opacity: 0; transition: opacity 0.3s ease;">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" class="btn btn-light btn-sm rounded-pill px-3" title="Detail Produk">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($stok > 0)
                                @if(session('user_logged_in'))
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 btn-add-cart"
                                            data-id-stok="{{ $produk['id_stok'] }}"
                                            data-nama-produk="{{ $produk['nama_produk'] }}"
                                            data-harga="{{ $produk['harga_produk'] }}"
                                            data-nama-toko="{{ $produk['nama_toko'] }}"
                                            title="Tambah ke Keranjang">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3"
                                            onclick="window.bukaChat && bukaChat('{{ $produk['nama_toko'] }}', '{{ $produk['id_stok'] }}')"
                                            title="Chat Penjual">
                                        <i class="fas fa-comments"></i>
                                    </button>
                                @else
                                    <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-light btn-sm rounded-pill px-3" title="Login untuk beli">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                @endif
                            @else
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" disabled title="Stok Habis">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Wishlist Button --}}
                    @if(session('user_logged_in'))
                        <button type="button" class="btn btn-light rounded-circle position-absolute top-0 end-0 m-2 btn-wishlist shadow-sm"
                                data-id-stok="{{ $produk['id_stok'] }}"
                                data-nama-produk="{{ $produk['nama_produk'] }}"
                                title="Tambah ke Wishlist"
                                style="width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-heart text-muted"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Product Info (Right) --}}
            <div class="col-12 col-md-8 col-lg-9">
                <div class="card-body d-flex flex-column p-3 p-md-4">
                    {{-- Store Badge --}}
                    <div class="mb-2">
                        <a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}" class="text-decoration-none text-muted small fw-medium" target="_blank">
                            <i class="fas fa-store text-primary me-1"></i>{{ $produk['nama_toko'] }}
                        </a>
                    </div>

                    {{-- Product Name --}}
                    <h5 class="card-title fw-bold mb-2" style="font-size: 1.1rem; line-height: 1.3;">
                        <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" class="text-dark text-decoration-none hover-text-primary">
                            {{ $produk['nama_produk'] }}
                        </a>
                    </h5>

                    {{-- Rating --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2 small">
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted">({{ number_format($rating, 1) }} - {{ $produk['jumlah_ulasan'] ?? 0 }} Ulasan)</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if(!empty($produk['deskripsi_produk']))
                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ \Illuminate\Support\Str::limit($produk['deskripsi_produk'], 150) }}
                        </p>
                    @endif

                    {{-- Stock Status --}}
                    <div class="mb-3">
                        <small class="{{ $stokClass }} d-flex align-items-center gap-1 fw-medium">
                            @if($stok <= 0)
                                <i class="fas fa-times-circle"></i>
                            @elseif($stok <= 5)
                                <i class="fas fa-exclamation-triangle"></i>
                            @else
                                <i class="fas fa-check-circle"></i>
                            @endif
                            <span id="stok-{{ $produk['id_stok'] }}">{{ $stokText }}</span>
                            @if($stok > 0)
                                <span class="text-muted">({{ $produk['satuan_produk'] ?? 'pcs' }})</span>
                            @endif
                        </small>
                    </div>

                    {{-- Price & Actions --}}
                    <div class="mt-auto d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($isFlash)
                                <span class="text-danger fw-bold fs-5">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                                @if(isset($produk['harga_normal']))
                                    <span class="text-muted text-decoration-line-through small">Rp{{ number_format($produk['harga_normal'], 0, ',', '.') }}</span>
                                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.65rem;">{{ $produk['diskon_persen'] ?? 0 }}%</span>
                                @endif
                            @else
                                <span class="text-primary fw-bold fs-5">Rp{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2 flex-wrap">
                            @if($stok > 0)
                                @if(session('user_logged_in'))
                                    <form action="{{ route('pelanggan_data.add_to_cart') }}" method="POST" class="d-flex gap-2 flex-wrap" id="product-form-{{ $produk['id_stok'] }}">
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
                                            <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                                                <label for="ukuran_produk_{{ $produk['id_stok'] }}" class="form-label small fw-medium mb-0">Ukuran:</label>
                                                <select id="ukuran_produk_{{ $produk['id_stok'] }}" name="ukuran_produk" class="form-select form-select-sm" style="width: auto;" required>
                                                    <option value="">Pilih Ukuran</option>
                                                    @foreach($ukuranList as $ukuran)
                                                        <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(is_array($ukuranList) && count($ukuranList) === 1)
                                            <input type="hidden" name="ukuran_produk" value="{{ $ukuranList[0] }}">
                                        @endif

                                        {{-- Quantity --}}
                                        <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                                            <label for="jumlah_produk_{{ $produk['id_stok'] }}" class="form-label small fw-medium mb-0">Qty:</label>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button type="button" class="btn btn-outline-secondary btn-qty" onclick="updateQty('{{ $produk['id_stok'] }}', -1)" aria-label="Kurangi">-</button>
                                                <input type="number" id="jumlah_produk_{{ $produk['id_stok'] }}" name="jumlah_produk"
                                                       min="1" value="1" max="{{ $stok }}"
                                                       class="form-control text-center form-input-qty"
                                                       oninput="validateQtyUI('{{ $produk['id_stok'] }}')"
                                                       aria-label="Jumlah">
                                                <button type="button" class="btn btn-outline-secondary btn-qty" onclick="updateQty('{{ $produk['id_stok'] }}', 1)" aria-label="Tambah">+</button>
                                            </div>
                                        </div>

                                        {{-- Buttons --}}
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary rounded-pill py-2 px-4" title="Tambah ke Keranjang">
                                                <i class="fas fa-shopping-cart me-1"></i>Keranjang
                                            </button>
                                            <button type="button" class="btn btn-success rounded-pill py-2 px-4 btn-buy-now"
                                                    onclick="beliLangsung('{{ $produk['id_stok'] }}')"
                                                    title="Beli Sekarang">
                                                <i class="fas fa-bolt me-1"></i>Beli
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    {{-- Guest user - redirect to login --}}
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-primary rounded-pill py-2 px-4" title="Login untuk beli">
                                            <i class="fas fa-sign-in-alt me-1"></i>Login
                                        </a>
                                        <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-outline-primary rounded-pill py-2 px-4" title="Daftar">
                                            <i class="fas fa-user-plus me-1"></i>Daftar
                                        </a>
                                    </div>
                                @endif
                            @else
                                <button type="button" class="btn btn-secondary rounded-pill py-2 px-4" disabled>
                                    <i class="fas fa-ban me-1"></i>Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>