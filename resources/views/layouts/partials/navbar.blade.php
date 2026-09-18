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

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
            </ul>

            <div class="d-flex align-items-center gap-2 navbar-actions">
                <!-- Search (desktop) -->
                <form class="search-box d-none d-md-block" action="{{ route('home_toko.katalog') }}" method="GET" role="search">
                    <label for="searchInput" class="visually-hidden">Cari produk</label>
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input id="searchInput" type="search" name="keyword" placeholder="Cari produk..." value="{{ request('keyword') }}" aria-label="Cari produk">
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
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('auth.logout_pelanggan') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin ingin logout?')">
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
                <i class="fas fa-search" aria-hidden="true"></i>
                <input id="searchInputMobile" type="search" name="keyword" placeholder="Cari produk..." value="{{ request('keyword') }}" aria-label="Cari produk">
            </form>
        </div>
    </div>
</nav>