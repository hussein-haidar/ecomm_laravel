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
?>

<header class="site-header">
    <div class="header-main">
        <!-- Branding -->
        <a href="{{ url('/') }}" class="brand-link" aria-label="Beranda {{ $dataWebsite['nama_toko'] }}">
            <div class="brand-logo">
                <i class="fas fa-store"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">{{ $dataWebsite['nama_toko'] }}</span>
                <span class="brand-tagline">Toko Online Terpercaya</span>
            </div>
        </a>

        <!-- Search Bar (Always Visible) -->
        <form method="GET" action="{{ url('/katalog') }}" class="search-form" role="search">
            <label for="header-search" class="visually-hidden">Cari produk</label>
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon" aria-hidden="true"></i>
                <input type="text" 
                       id="header-search" 
                       name="keyword" 
                       class="form-control search-input" 
                       placeholder="Cari produk..." 
                       value="{{ request('keyword') }}"
                       autocomplete="off">
                @if(request('keyword'))
                    <button type="button" class="search-clear" onclick="this.form.reset();" aria-label="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-search d-none d-md-inline-flex" aria-label="Cari">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>

        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler" onclick="toggleNavbar()" aria-label="Buka menu" aria-expanded="false" aria-controls="mobileMenu">
            <span class="hamburger"></span>
        </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <nav id="mobileMenu" class="mobile-nav" role="navigation" aria-label="Menu utama">
        <div class="mobile-nav-overlay" onclick="toggleNavbar()" aria-hidden="true"></div>
        <div class="mobile-nav-drawer">
            <div class="mobile-nav-header">
                <h2>Menu</h2>
                <button class="mobile-nav-close" onclick="toggleNavbar()" aria-label="Tutup menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mobile-nav-content">
                <!-- Section 1: Navigasi Utama -->
                <section class="nav-section" aria-labelledby="nav-main-heading">
                    <h3 id="nav-main-heading" class="nav-section-title">Navigasi</h3>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ url('home_toko/katalog') }}" class="nav-item">
                                <i class="fas fa-list"></i>
                                <span>Katalog Produk</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-item dropdown-trigger" data-dropdown="jenis-produk">
                                <i class="fas fa-box-open"></i>
                                <span>Jenis Produk</span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </a>
                            <ul id="jenis-produk" class="nav-sublist" hidden>
                                @foreach ($jenis_produk_dropdown as $jenis)
                                <li>
                                    <a href="{{ url('home_toko/jenisProduk/' . urlencode($jenis['jenis_produk'])) }}" class="nav-sublink">
                                        <i class="fas fa-tag"></i>
                                        <span>{{ $jenis['jenis_produk'] }}</span>
                                        <i class="fas fa-chevron-right sublink-arrow"></i>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @if($user_logged_in)
                        <li>
                            <a href="#" class="nav-item filter-trigger" onclick="openFilterSheet(event);">
                                <i class="fas fa-filter"></i>
                                <span>Filter Produk</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </section>

                @if($user_logged_in)
                <!-- Section 2: Transaksi -->
                <section class="nav-section" aria-labelledby="nav-transaksi-heading">
                    <h3 id="nav-transaksi-heading" class="nav-section-title">Transaksi</h3>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ url('pelanggan_data/statusBayar') }}" class="nav-item">
                                <i class="fas fa-credit-card"></i>
                                <span>Pembayaran</span>
                                @if($jumlahBayar > 0)
                                <span class="badge badge-primary">{{ $jumlahBayar }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('pelanggan_data/cart') }}" class="nav-item">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Keranjang</span>
                                @if($jumlahBarang > 0)
                                <span class="badge badge-danger">{{ $jumlahBarang }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('pelanggan_data/statusKirim') }}" class="nav-item">
                                <i class="fas fa-truck"></i>
                                <span>Pengiriman</span>
                                @if($jumlahLacak > 0)
                                <span class="badge badge-success">{{ $jumlahLacak }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('pelanggan_data/riwayatBeli') }}" class="nav-item">
                                <i class="fas fa-history"></i>
                                <span>Riwayat Beli</span>
                            </a>
                        </li>
                    </ul>
                </section>

                <!-- Section 3: Akun & Bantuan -->
                <section class="nav-section" aria-labelledby="nav-akun-heading">
                    <h3 id="nav-akun-heading" class="nav-section-title">Akun & Bantuan</h3>
                    <ul class="nav-list">
                        <li>
                            <a href="javascript:void(0)" onclick="window.bukaChat && bukaChat()" class="nav-item">
                                <i class="fas fa-comments"></i>
                                <span>Chat Penjual</span>
                                @if($jumlahChat > 0)
                                <span class="badge badge-info">{{ $jumlahChat }}</span>
                                @endif
                            </a>
                        </li>
                        @if(session('level') == 'pelanggan')
                        <li>
                            <a href="{{ route('pelanggan_data.notifikasi') }}" class="nav-item">
                                <i class="fas fa-bell"></i>
                                <span>Notifikasi</span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ url('pelanggan_data/profil') }}" class="nav-item">
                                <i class="fas fa-user"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="logoutPelanggan(event);" class="nav-item nav-item-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Keluar</span>
                            </a>
                            <form id="logout-form" action="{{ route('auth.logout_pelanggan') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </section>
                @else
                <!-- Guest User -->
                <section class="nav-section" aria-labelledby="nav-guest-heading">
                    <h3 id="nav-guest-heading" class="nav-section-title">Masuk</h3>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ url('auth/login_pelanggan') }}" class="nav-item nav-item-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login / Daftar</span>
                            </a>
                        </li>
                    </ul>
                </section>
                @endif
            </div>

            <div class="mobile-nav-footer">
                <small>&copy; {{ date('Y') }} {{ $dataWebsite['nama_toko'] }}</small>
            </div>
        </div>
    </nav>
</header>

<!-- Filter Bottom Sheet (Mobile) -->
<div class="filter-sheet-overlay" id="filterSheetOverlay" onclick="closeFilterSheet()" aria-hidden="true"></div>
<div class="filter-sheet" id="filterSheet" role="dialog" aria-modal="true" aria-labelledby="filterSheetTitle">
    <div class="filter-sheet-handle" onclick="closeFilterSheet()" aria-label="Tutup filter"></div>
    <div class="filter-sheet-header">
        <h5 id="filterSheetTitle" class="filter-sheet-title">Filter Produk</h5>
        <button type="button" class="btn-close" onclick="closeFilterSheet()" aria-label="Tutup filter"></button>
    </div>
    <div class="filter-sheet-body">
        <form id="product-filter-form" method="GET" action="{{ url('/katalog') }}">
            <div class="mb-3">
                <label for="keyword" class="form-label">Cari Kata</label>
                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari..." value="{{ request('keyword') }}">
            </div>
            <div class="mb-3">
                <label for="jenis_produk" class="form-label">Jenis Produk</label>
                <select name="jenis_produk" id="jenis_produk" class="form-control">
                    <option value="">Pilih Kategori</option>
                    @foreach ($jenis_produk_dropdown as $jenis)
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
                <form id="product-filter-form-desktop" method="GET" action="{{ url('/katalog') }}">
                    <div class="mb-3">
                        <label for="keyword_desktop" class="form-label">Cari Kata</label>
                        <input type="text" name="keyword" id="keyword_desktop" class="form-control" placeholder="Cari..." value="{{ request('keyword') }}">
                    </div>
                    <div class="mb-3">
                        <label for="jenis_produk_desktop" class="form-label">Jenis Produk</label>
                        <select name="jenis_produk" id="jenis_produk_desktop" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach ($jenis_produk_dropdown as $jenis)
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

<script>
    // Mobile Navigation
    function toggleNavbar() {
        const nav = document.getElementById('mobileMenu');
        const toggler = document.querySelector('.navbar-toggler');
        const isOpen = nav.classList.toggle('active');
        toggler.setAttribute('aria-expanded', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    // Dropdown in mobile nav
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    e.stopPropagation();
                    const sublistId = this.dataset.dropdown;
                    const sublist = document.getElementById(sublistId);
                    const isOpen = !sublist.hidden;
                    
                    // Close other dropdowns
                    document.querySelectorAll('.nav-sublist').forEach(other => {
                        if (other !== sublist) other.hidden = true;
                    });
                    
                    sublist.hidden = isOpen;
                    this.setAttribute('aria-expanded', !isOpen);
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!e.target.closest('.dropdown-trigger') && !e.target.closest('.nav-sublist')) {
                    document.querySelectorAll('.nav-sublist').forEach(sublist => {
                        sublist.hidden = true;
                    });
                    document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                }
            }
        });
    });

    // Filter Bottom Sheet Functions
    function openFilterSheet(event) {
        event.preventDefault();
        event.stopPropagation();
        if (window.innerWidth <= 768) {
            document.getElementById('filterSheetOverlay').classList.add('active');
            document.getElementById('filterSheet').classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            var modal = new bootstrap.Modal(document.getElementById('filterModalDesktop'));
            modal.show();
        }
    }

    function closeFilterSheet() {
        document.getElementById('filterSheetOverlay').classList.remove('active');
        document.getElementById('filterSheet').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilterSheet();
            const nav = document.getElementById('mobileMenu');
            if (nav.classList.contains('active')) {
                toggleNavbar();
            }
        }
    });

    // Logout
    function logoutPelanggan(event) {
        event.preventDefault();
        const icon = document.getElementById('logout-icon');
        const originalColor = icon.style.color;
        icon.style.color = '#3085d6';

        Swal.fire({
            title: 'Yakin ingin logout?',
            text: 'Anda akan keluar dari akun pelanggan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            } else {
                icon.style.color = originalColor;
            }
        });
    }
</script>