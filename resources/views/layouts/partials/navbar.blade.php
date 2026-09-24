<?php
$jumlahBarang = 0;
$id_pelanggan = session()->get('id_pelanggan');

if (!empty($id_pelanggan)) {
    $keranjangModel = new \App\Models\M_Keranjang();
    $jumlahBarang = $keranjangModel->get_searchKeranjang($id_pelanggan)->count();
}

$jumlahBayar = 0;
$id_pelanggan = session()->get('id_pelanggan');

if (!empty($id_pelanggan)) {
    $pembayaranModel = new \App\Models\M_Pembayaran();
    $status = $pembayaranModel->get_bayar_by_status($id_pelanggan);
    $jumlahBayar = count($status);
}

$jumlahLacak = 0;
$id_pelanggan = session()->get('id_pelanggan');

if (!empty($id_pelanggan)) {
    $lacakModel = new \App\Models\M_Ekspedisi();
    $status = $lacakModel->get_lacak_by_status($id_pelanggan);
    $jumlahLacak = count($status);
}

$jumlahChat = 0;
$id_pelanggan = session()->get('id_pelanggan');

if (!empty($id_pelanggan)) {
    $jumlahChat = \App\Models\M_Chat::hitungBelumDibacaPelanggan($id_pelanggan);
}

$user_logged_in = session()->has('user_logged_in') && session('user_logged_in');
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-modern" role="navigation" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}" aria-label="Beranda {{ $dataWebsite['nama_toko'] }}">
            <div class="brand-logo">
                @if($dataWebsite['logo_website'])
                    <img src="{{ asset('logo_website/' . $dataWebsite['logo_website']) }}" alt="Logo {{ $dataWebsite['nama_toko'] }}" width="34" height="34" class="rounded-circle">
                @else
                    <i class="fas fa-store text-primary"></i>
                @endif
                <span class="brand-text">{{ $dataWebsite['nama_toko'] }}</span>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" onclick="toggleNavbar()" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home_toko.index') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i>Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home_toko.katalog') ? 'active' : '' }}" href="{{ route('home_toko.katalog') }}">
                        <i class="fas fa-th-large me-1"></i>Katalog
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle no-caret d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-box-open me-1"></i>Kategori <i class="fas fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-modern">
                        <li><a class="dropdown-item" href="{{ route('home_toko.katalog') }}"><i class="fas fa-list me-2"></i>Semua Produk</a></li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach ($jenis_produk_dropdown ?? [] as $jenis)
                            <li>
                                <a class="dropdown-item" href="{{ route('home_toko.jenisProduk', ['jenis_produk' => urlencode($jenis['jenis_produk'])] ) }}">
                                    <i class="fas fa-tag me-2"></i>{{ $jenis['jenis_produk'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home_toko.syaket') ? 'active' : '' }}" href="{{ route('home_toko.syaket') }}">
                        <i class="fas fa-file-contract me-1"></i>Syarat &amp; Ketentuan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home_toko.bantuan') ? 'active' : '' }}" href="{{ route('home_toko.bantuan') }}">
                        <i class="fas fa-question-circle me-1"></i>Bantuan
                    </a>
                </li>

                {{-- Filter Produk (khusus mobile, muncul di dalam collapse) --}}
                <li class="nav-item d-lg-none">
                    <a class="nav-link" href="#" onclick="openFilterSheet(event);">
                        <i class="fas fa-filter me-1"></i>Filter Produk
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 navbar-actions">
                <!-- Search (desktop) -->
                <form class="search-box d-none d-md-block" action="{{ route('home_toko.katalog') }}" method="GET" role="search">
                    <label for="searchInput" class="visually-hidden">Cari produk</label>
                    <button type="submit" class="search-submit" aria-label="Cari">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                    <input id="searchInput" type="search" name="keyword" placeholder="Cari produk..." value="{{ request('keyword') }}" aria-label="Cari produk" autocomplete="off">
                </form>

                {{-- Cart Button (selalu tampil) --}}
                <a href="{{ session('user_logged_in') ? route('pelanggan_data.cart') : route('auth.login_pelanggan') }}"
                   class="btn cart-btn position-relative"
                   aria-label="Keranjang belanja"
                   title="{{ session('user_logged_in') ? 'Keranjang belanja' : 'Login untuk mengakses keranjang' }}">
                    <i class="fas fa-shopping-cart"></i>
                    @php
                        $cartCount = 0;
                        if (session('user_logged_in') && session('id_pelanggan')) {
                            $cartCount = \App\Models\M_Keranjang::where('id_pelanggan', session('id_pelanggan'))->where('status_keranjang', 'proses')->count();
                        }
                    @endphp
                    @if($cartCount > 0)
                        <span class="cart-count">{{ $cartCount }}</span>
                    @endif
                </a>

                {{-- User Menu / Auth --}}
                @if(session('user_logged_in'))
                    <a href="{{ route('pelanggan_data.notifikasi') }}" class="btn notif-btn position-relative" aria-label="Notifikasi" title="Notifikasi">
                        <i class="fas fa-bell"></i>
                    </a>

                    <div class="nav-item dropdown position-relative">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#333 !important; margin:0;">
                            @if(session('foto_pelanggan'))
                                <img src="{{ asset('fotopelanggan/' . session('foto_pelanggan')) }}" alt="{{ session('nama_pelanggan') }}" class="rounded-circle navbar-avatar" width="28" height="28">
                            @else
                                <div class="avatar-placeholder rounded-circle navbar-avatar d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <span class="d-none d-lg-inline">{{ session('nama_pelanggan') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-modern">
                            <li><h6 class="dropdown-header fs-6 fw-bold">{{ session('nama_pelanggan') }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.profil') }}"><i class="fas fa-user me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.cart') }}"><i class="fas fa-shopping-cart me-2"></i>Keranjang</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.statusBayar') }}"><i class="fas fa-credit-card me-2"></i>Pembayaran</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.statusKirim') }}"><i class="fas fa-truck me-2"></i>Pengiriman</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.riwayatBeli') }}"><i class="fas fa-history me-2"></i>Riwayat Beli</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.wishlist') }}"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                            <li><a class="dropdown-item" href="{{ route('pelanggan_data.retur') }}"><i class="fas fa-undo-alt me-2"></i>Retur & Pengembalian</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form id="logout-form-desktop" action="{{ route('auth.logout_pelanggan') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="dropdown-item text-danger" onclick="logoutPelanggan(event)">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('auth.login_pelanggan') }}" class="btn btn-sm btn-outline-primary rounded-pill auth-btn px-3">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-sm rounded-pill auth-btn auth-btn-solid px-3">
                            <i class="fas fa-user-plus me-1"></i>Daftar
                        </a>
                    </div>
                @endif
            </div>

            <!-- Search (mobile, di dalam collapse) -->
            <form class="search-box d-md-none mt-3" action="{{ route('home_toko.katalog') }}" method="GET" role="search">
                <label for="searchInputMobile" class="visually-hidden">Cari produk</label>
                <button type="submit" class="search-submit" aria-label="Cari">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>
                <input id="searchInputMobile" type="search" name="keyword" placeholder="Cari produk..." value="{{ request('keyword') }}" aria-label="Cari produk" autocomplete="off">
            </form>
        </div>
    </div>
</nav>

<!-- Mobile Navigation Drawer -->
<div class="mobile-nav" id="mobileMenu" role="dialog" aria-modal="true" aria-labelledby="mobileNavTitle">
    <div class="mobile-nav-overlay" onclick="toggleNavbar()" aria-hidden="true"></div>
    <div class="mobile-nav-drawer">
        <div class="mobile-nav-header">
            <h2 id="mobileNavTitle">{{ $dataWebsite['nama_toko'] }}</h2>
            <button class="mobile-nav-close" onclick="toggleNavbar()" aria-label="Tutup menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-nav-content">
            <div class="nav-section">
                <div class="nav-section-title">Menu Utama</div>
                <ul class="nav-list">
                    <li>
                        <a class="nav-item" href="{{ url('/') }}">
                            <i class="fas fa-home"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-item" href="{{ route('home_toko.katalog') }}">
                            <i class="fas fa-th-large"></i>
                            <span>Katalog Produk</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-item" href="#" onclick="openFilterSheet(event)">
                            <i class="fas fa-filter"></i>
                            <span>Filter Produk</span>
                        </a>
                    </li>
                    <li class="dropdown-trigger" data-dropdown="mobileKategori" aria-expanded="false">
                        <a class="nav-item" href="#" role="button">
                            <i class="fas fa-box-open"></i>
                            <span>Kategori</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="nav-sublist" id="mobileKategori" hidden>
                            <li><a class="nav-sublink" href="{{ route('home_toko.katalog') }}"><i class="fas fa-list"></i> Semua Produk</a></li>
                            @foreach ($jenis_produk_dropdown ?? [] as $jenis)
                            <li><a class="nav-sublink" href="{{ route('home_toko.jenisProduk', ['jenis_produk' => urlencode($jenis['jenis_produk'])] ) }}"><i class="fas fa-tag"></i> {{ $jenis['jenis_produk'] }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li>
                        <a class="nav-item" href="{{ route('home_toko.syaket') }}">
                            <i class="fas fa-file-contract"></i>
                            <span>Syarat & Ketentuan</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-item" href="{{ route('home_toko.bantuan') }}">
                            <i class="fas fa-question-circle"></i>
                            <span>Bantuan / FAQ</span>
                        </a>
                    </li>
                </ul>
            </div>

            @if(session('user_logged_in'))
            <div class="nav-section">
                <div class="nav-section-title">Akun Saya</div>
                <ul class="nav-list">
                    <li><a class="nav-item" href="{{ route('pelanggan_data.profil') }}"><i class="fas fa-user"></i> <span>Profil Saya</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.cart') }}"><i class="fas fa-shopping-cart"></i> <span>Keranjang</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.statusBayar') }}"><i class="fas fa-credit-card"></i> <span>Pembayaran</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.statusKirim') }}"><i class="fas fa-truck"></i> <span>Pengiriman</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.riwayatBeli') }}"><i class="fas fa-history"></i> <span>Riwayat Beli</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.wishlist') }}"><i class="fas fa-heart"></i> <span>Wishlist</span></a></li>
                    <li><a class="nav-item" href="{{ route('pelanggan_data.retur') }}"><i class="fas fa-undo-alt"></i> <span>Retur & Pengembalian</span></a></li>
                    <li>
                        <form id="logout-form-mobile" action="{{ route('auth.logout_pelanggan') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" class="nav-item nav-item-danger w-100 text-start" onclick="logoutPelanggan(event)">
                                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            <div class="nav-section">
                <div class="nav-section-title">Masuk / Daftar</div>
                <ul class="nav-list">
                    <li><a class="nav-item nav-item-primary" href="{{ route('auth.login_pelanggan') }}"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
                    <li><a class="nav-item" href="{{ route('auth.register_pelanggan') }}"><i class="fas fa-user-plus"></i> <span>Daftar</span></a></li>
                </ul>
            </div>
            @endif
        </div>
        <div class="mobile-nav-footer">
            &copy; {{ date('Y') }} {{ $dataWebsite['nama_toko'] }}
        </div>
    </div>
</div>

<!-- Filter Bottom Sheet (Mobile) -->
<div class="filter-sheet-overlay" id="filterSheetOverlay" onclick="closeFilterSheet()" aria-hidden="true"></div>
<div class="filter-sheet" id="filterSheet" role="dialog" aria-modal="true" aria-labelledby="filterSheetTitle">
    <div class="filter-sheet-handle" onclick="closeFilterSheet()" aria-label="Tutup filter"></div>
    <div class="filter-sheet-header">
        <h5 id="filterSheetTitle" class="filter-sheet-title">Filter Produk</h5>
        <button type="button" class="btn-close" onclick="closeFilterSheet()" aria-label="Tutup filter"></button>
    </div>
    <div class="filter-sheet-body">
        <form id="product-filter-form" method="GET" action="{{ route('home_toko.katalog') }}">
            <div class="mb-3">
                <label for="keyword" class="form-label">Cari Kata</label>
                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari..." value="{{ request('keyword') }}">
            </div>
            <div class="mb-3">
                <label for="jenis_produk" class="form-label">Jenis Produk</label>
                <select name="jenis_produk" id="jenis_produk" class="form-control">
                    <option value="">Pilih Kategori</option>
                    @foreach ($jenis_produk_dropdown ?? [] as $jenis)
                        <option value="{{ $jenis['jenis_produk'] }}" {{ request('jenis_produk') == $jenis['jenis_produk'] ? 'selected' : '' }}>
                            {{ $jenis['jenis_produk'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Rentang Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp.</span>
                    <input type="number" name="harga_min" class="form-control" placeholder="Min" value="{{ request('harga_min') }}">
                    <span class="input-group-text">-</span>
                    <span class="input-group-text">Rp.</span>
                    <input type="number" name="harga_max" class="form-control" placeholder="Max" value="{{ request('harga_max') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Rating / Ulasan</label>
                <div class="d-flex flex-column gap-1">
                    @for($i = 5; $i >= 1; $i--)
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" name="rating[]" value="{{ $i }}" id="rating_m_{{ $i }}" {{ in_array((string)$i, request('rating', [])) ? 'checked' : '' }}>
                            <label class="form-check-label small text-warning" for="rating_m_{{ $i }}">
                                @for($j = 1; $j <= $i; $j++) <i class="fas fa-star"></i> @endfor
                                {{ $i }}+ bintang
                            </label>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Urutkan Berdasarkan Harga</label>
                <select name="sort_harga" class="form-control">
                    <option value="">Default</option>
                    <option value="asc" {{ request('sort_harga') == 'asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="desc" {{ request('sort_harga') == 'desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Sorting Nama</label>
                <select name="sort_nama" class="form-control">
                    <option value="">Pilih Urutan</option>
                    <option value="a-z" {{ request('sort_nama') == 'a-z' ? 'selected' : '' }}>A - Z</option>
                    <option value="z-a" {{ request('sort_nama') == 'z-a' ? 'selected' : '' }}>Z - A</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
            <a href="{{ url('home_toko/katalog') }}" class="btn btn-secondary w-100 mt-2">Reset Filter</a>
        </form>
    </div>
</div>

<!-- Filter Modal (Desktop) -->
<div class="modal fade" id="filterModalDesktop" tabindex="-1" aria-labelledby="filterModalDesktopLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalDesktopLabel">Filter Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-filter-form-desktop" method="GET" action="{{ route('home_toko.katalog') }}">
                    <div class="mb-3">
                        <label for="keyword_desktop" class="form-label">Cari Kata</label>
                        <input type="text" name="keyword" id="keyword_desktop" class="form-control" placeholder="Cari..." value="{{ request('keyword') }}">
                    </div>
                    <div class="mb-3">
                        <label for="jenis_produk_desktop" class="form-label">Jenis Produk</label>
                        <select name="jenis_produk" id="jenis_produk_desktop" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach ($jenis_produk_dropdown ?? [] as $jenis)
                                <option value="{{ $jenis['jenis_produk'] }}" {{ request('jenis_produk') == $jenis['jenis_produk'] ? 'selected' : '' }}>
                                    {{ $jenis['jenis_produk'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rentang Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="harga_min" class="form-control" placeholder="Min" value="{{ request('harga_min') }}">
                            <span class="input-group-text">-</span>
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="harga_max" class="form-control" placeholder="Max" value="{{ request('harga_max') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating / Ulasan</label>
                        <div class="d-flex flex-column gap-1">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="checkbox" name="rating[]" value="{{ $i }}" id="rating_d_{{ $i }}" {{ in_array((string)$i, request('rating', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small text-warning" for="rating_d_{{ $i }}">
                                        @for($j = 1; $j <= $i; $j++) <i class="fas fa-star"></i> @endfor
                                        {{ $i }}+ bintang
                                    </label>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutkan Berdasarkan Harga</label>
                        <select name="sort_harga" class="form-control">
                            <option value="">Default</option>
                            <option value="asc" {{ request('sort_harga') == 'asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="desc" {{ request('sort_harga') == 'desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sorting Nama</label>
                        <select name="sort_nama" class="form-control">
                            <option value="">Pilih Urutan</option>
                            <option value="a-z" {{ request('sort_nama') == 'a-z' ? 'selected' : '' }}>A - Z</option>
                            <option value="z-a" {{ request('sort_nama') == 'z-a' ? 'selected' : '' }}>Z - A</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                    <a href="{{ url('home_toko/katalog') }}" class="btn btn-secondary w-100 mt-2">Reset Filter</a>
                </form>
            </div>
        </div>
    </div>
</div>