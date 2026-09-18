@if(empty($produk_data))
    <div class="col-12 text-center py-5" data-aos="fade-up">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Tidak Ada Produk</h4>
        <p class="text-muted">Tidak ditemukan produk yang sesuai dengan kriteria pencarian Anda.</p>
        <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Katalog
        </a>
    </div>
@else
    @foreach($produk_data as $produk)
        @php
            $rating = round($produk['rata_rating'] ?? 0, 1);
            $isFlash = $produk['harga_flash'] ?? false;
            $stok = $produk['jumlah_stok_produk'] ?? 0;
            $stokClass = $stok <= 0 ? 'text-danger' : ($stok <= 5 ? 'text-warning' : 'text-success');
            $stokText = $stok <= 0 ? 'Habis' : ($stok <= 5 ? "Sisa $stok" : 'Tersedia');
        @endphp
        
        <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 8) * 50 }}">
            <article class="product-card card h-100 shadow-sm border-0 overflow-hidden" 
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
                
                {{-- Product Image --}}
                <div class="img-container position-relative bg-light" style="aspect-ratio: 1/1; overflow: hidden;">
                    <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" aria-label="Lihat detail {{ $produk['nama_produk'] }}">
                        <img src="{{ asset('fotoproduk/' . ($produk['foto_produk'] ?? 'default.jpg')) }}" 
                             class="card-img-top img-fluid w-100 h-100" 
                             alt="{{ $produk['nama_produk'] }}"
                             style="object-fit: cover; transition: transform 0.4s ease;"
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
                
                {{-- Product Info --}}
                <div class="card-body d-flex flex-column p-3">
                    {{-- Store Badge --}}
                    <div class="mb-2">
                        <a href="{{ url('home_toko/view_toko/' . urlencode($produk['nama_toko'])) }}" class="text-decoration-none text-muted small fw-medium" target="_blank">
                            <i class="fas fa-store text-primary me-1"></i>{{ $produk['nama_toko'] }}
                        </a>
                    </div>
                    
                    {{-- Product Name --}}
                    <h6 class="card-title fw-bold mb-2 text-truncate" style="font-size: 0.95rem; line-height: 1.3;">
                        <a href="{{ url('home_toko/detail_produk/' . urlencode($produk['nama_produk'])) }}" class="text-dark text-decoration-none hover-text-primary">
                            {{ $produk['nama_produk'] }}
                        </a>
                    </h6>
                    
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
                    
                    {{-- Size Info --}}
                    @if(!empty($produk['ukuran_list']) && is_array($produk['ukuran_list']))
                        <div class="mb-2">
                            <small class="text-muted d-flex align-items-center gap-1 flex-wrap">
                                <i class="fas fa-ruler-combined text-primary"></i>
                                Ukuran:
                                @foreach($produk['ukuran_list'] as $uk)
                                    <span class="badge bg-light text-dark border">{{ $uk }}</span>
                                @endforeach
                            </small>
                        </div>
                    @endif
                    
                    {{-- Stock Status --}}
                    <div class="mb-2">
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
                    
                    {{-- Price --}}
                    <div class="mt-auto">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
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
                        <div class="d-flex gap-2">
                            @if($stok > 0)
                                @if(session('user_logged_in'))
                                    <form action="{{ route('pelanggan_data.add_to_cart') }}" method="POST" class="flex-grow-1" id="product-form-{{ $produk['id_stok'] }}">
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
                                        @if(!empty($produk['ukuran_list']) && is_array($produk['ukuran_list']) && count($produk['ukuran_list']) > 1)
                                            <div class="mb-2">
                                                <label for="ukuran_produk_{{ $produk['id_stok'] }}" class="form-label small fw-medium">Ukuran:</label>
                                                <select id="ukuran_produk_{{ $produk['id_stok'] }}" name="ukuran_produk" class="form-select form-select-sm" required>
                                                    <option value="">Pilih Ukuran</option>
                                                    @foreach($produk['ukuran_list'] as $ukuran)
                                                        <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(!empty($produk['ukuran_list']) && is_array($produk['ukuran_list']))
                                            <input type="hidden" name="ukuran_produk" value="{{ $produk['ukuran_list'][0] }}">
                                        @endif
                                        
                                        {{-- Quantity --}}
                                        <div class="mb-2">
                                            <label for="jumlah_produk_{{ $produk['id_stok'] }}" class="form-label small fw-medium">Qty:</label>
                                            <div class="input-group input-group-sm" style="max-width: 140px;">
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
                                            <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill py-2" title="Tambah ke Keranjang">
                                                <i class="fas fa-shopping-cart me-1"></i>Keranjang
                                            </button>
                                            <button type="button" class="btn btn-success rounded-pill py-2 px-3 btn-buy-now" 
                                                    onclick="beliLangsung('{{ $produk['id_stok'] }}')"
                                                    title="Beli Sekarang">
                                                <i class="fas fa-bolt me-1"></i>Beli
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    {{-- Guest user - redirect to login --}}
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-primary flex-grow-1 rounded-pill py-2" title="Login untuk beli">
                                            <i class="fas fa-sign-in-alt me-1"></i>Login
                                        </a>
                                        <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-outline-primary rounded-pill py-2 px-3" title="Daftar">
                                            <i class="fas fa-user-plus me-1"></i>Daftar
                                        </a>
                                    </div>
                                @endif
                            @else
                                <button type="button" class="btn btn-secondary w-100 rounded-pill py-2" disabled>
                                    <i class="fas fa-ban me-1"></i>Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        </div>
    @endforeach
@endif

{{-- JavaScript untuk interaksi produk --}}
<script>
    // Quick add to cart from overlay
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-add-cart');
        if (btn) {
            e.preventDefault();
            addToCartQuick(btn.dataset.idStok);
        }
    });

    // Quantity controls
    function updateQty(idStok, delta) {
        const input = document.getElementById('jumlah_produk_' + idStok);
        if (!input) return;
        let val = parseInt(input.value) || 1;
        const max = parseInt(input.max) || 99;
        val = Math.max(1, Math.min(val + delta, max));
        input.value = val;
    }

    function validateQtyUI(idStok) {
        const input = document.getElementById('jumlah_produk_' + idStok);
        if (!input) return;
        let val = parseInt(input.value) || 1;
        const max = parseInt(input.max) || 99;
        if (val < 1) input.value = 1;
        else if (val > max) input.value = max;
    }

    // Quick add to cart (from overlay)
    function addToCartQuick(idStok) {
        // Find the form
        const form = document.getElementById('product-form-' + idStok);
        if (!form) {
            showToast('Form produk tidak ditemukan', 'error');
            return;
        }

        // Check size if required
        const sizeSelect = form.querySelector('select[name="ukuran_produk"]');
        if (sizeSelect && sizeSelect.required && !sizeSelect.value) {
            showToast('Silakan pilih ukuran produk terlebih dahulu', 'error');
            sizeSelect.focus();
            return;
        }

        // Get quantity
        const qtyInput = form.querySelector('input[name="jumlah_produk"]');
        const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

        // Submit via AJAX
        const formData = new FormData(form);
        formData.append('jumlah_produk', qty);

        fetch('{{ route('pelanggan_data.add_to_cart') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                updateCartBadge(data.cart_count);
            } else {
                showToast(data.message || 'Gagal menambah ke keranjang', 'error');
            }
        })
        .catch(error => {
            console.error('Add to cart error:', error);
            // Fallback: submit form normally
            form.submit();
        });
    }

    // Buy now
    function beliLangsung(idStok) {
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
    }

    // Wishlist toggle
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-wishlist');
        if (btn) {
            e.preventDefault();
            const idStok = btn.dataset.idStok;
            const icon = btn.querySelector('i');
            const isActive = icon.classList.contains('text-danger');
            
            fetch('{{ route('pelanggan_data.wishlist.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id_stok: idStok })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    icon.classList.toggle('text-danger', !isActive);
                    icon.classList.toggle('text-muted', isActive);
                    icon.classList.toggle('fas', !isActive);
                    icon.classList.toggle('far', isActive);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Gagal update wishlist', 'error');
                }
            })
            .catch(err => console.error('Wishlist error:', err));
        }
    });

    // Real-time stock update
    function updateStockDisplay() {
        fetch('{{ route('home_toko.ambilStok') }}')
            .then(res => res.json())
            .then(data => {
                data.forEach(produk => {
                    const stokEl = document.getElementById('stok-' + produk.id_stok);
                    const card = document.querySelector(`[data-id-stok="${produk.id_stok}"]`);
                    if (stokEl && card) {
                        const newStok = parseInt(produk.jumlah_stok_produk);
                        const oldStok = parseInt(stokEl.textContent);
                        
                        if (newStok !== oldStok) {
                            stokEl.textContent = newStok <= 0 ? 'Habis' : (newStok <= 5 ? `Sisa ${newStok}` : 'Tersedia');
                            stokEl.className = newStok <= 0 ? 'text-danger' : (newStok <= 5 ? 'text-warning' : 'text-success');
                            
                            // Update badge
                            const badge = card.querySelector('.badge.bg-warning, .badge.bg-secondary, .badge.bg-danger');
                            if (newStok <= 0) {
                                if (!badge || !badge.classList.contains('bg-secondary')) {
                                    card.querySelector('.img-container').insertAdjacentHTML('afterbegin', '<span class="badge bg-secondary position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;"><i class="fas fa-times-circle me-1"></i>HABIS</span>');
                                }
                                // Disable buttons
                                card.querySelectorAll('button, .btn-add-cart').forEach(btn => btn.disabled = true);
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Stock update error:', err));
    }

    // Run stock update every 30 seconds
    setInterval(updateStockDisplay, 30000);
    updateStockDisplay(); // Initial run

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

    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
</script>

{{-- CSS tambahan untuk hover effect --}}
<style>
    .product-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2) !important;
    }
    
    .product-card:hover .img-container img {
        transform: scale(1.05);
    }
    
    .product-card:hover .bg-gradient {
        opacity: 1 !important;
    }
    
    .img-container img {
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .hover-text-primary:hover {
        color: #667eea !important;
    }
    
    .btn-qty {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-input-qty {
        border-left: none;
        border-right: none;
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>