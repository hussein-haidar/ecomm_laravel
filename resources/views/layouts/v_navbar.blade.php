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

<header>
    <div class="header-top">
        <h1>
            <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">
                {{ $dataWebsite['nama_toko'] }}
            </a>
        </h1>

        <!-- Ikon Pencarian untuk layar kecil -->
        <i class="fas fa-search search-icon" onclick="toggleSearchBar()"></i>

        <form method="GET" action="{{ url('/katalog') }}">
            <div class="input-group mb-3">
                <input type="text" class="form-control search-input" placeholder="Cari produk..." name="keyword"
                    value="{{ request('keyword') }}">
                <button class="btn btn-primary btn-search" type="submit">Cari</button>
            </div>
        </form>

        <div class="navbar-toggler" onclick="toggleNavbar()">&#9776;</div>

        <nav>
            <ul>
                <li><a href="{{ url('home_toko/katalog') }}"><i class="fas fa-list"></i> Katalog</li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#"><i class="fas fa-box-open"></i> Jenis Produk</a>
                    <ul class="dropdown-content">
                        @foreach ($jenis_produk_dropdown as $jenis)
                            <li>
                                <a href="{{ url('home_toko/jenisProduk/' . urlencode($jenis['jenis_produk'])) }}">
                                    {{ $jenis['jenis_produk'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @if ($user_logged_in)
                    <li>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#filterModal" role="button">
                            <i class="fas fa-filter"></i> Filter
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('pelanggan_data/statusBayar') }}" class="cart-icon-wrapper" title="Pembayaran">
                            <i class="fa fa-bell"></i>
                            @if ($jumlahBayar > 0)
                                <span class="cart-count">{{ $jumlahBayar }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('pelanggan_data/cart') }}" class="cart-icon-wrapper" title="Keranjang">
                            <i class="fas fa-cart-plus"> </i>
                            @if ($jumlahBarang > 0)
                                <span class="cart-count">{{ $jumlahBarang }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('pelanggan_data/statusKirim') }}" class="cart-icon-wrapper" title="Pengiriman">
                            <i class="fas fa-truck"></i>
                            @if ($jumlahLacak > 0)
                                <span class="cart-count">{{ $jumlahLacak }}</span>
                            @endif
                        </a>
                    </li>
                    <li><a href="{{ url('pelanggan_data/riwayatBeli') }}"><i class="fas fa-history"> </i></a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" onclick="window.bukaChat && bukaChat()" class="cart-icon-wrapper" title="Chat Penjual">
                            <i class="fas fa-comments"></i>
                            <span id="chat-nav-badge"
                                class="cart-count {{ $jumlahChat > 0 ? '' : 'd-none' }}">{{ $jumlahChat }}</span>
                        </a>
                    </li>
                    @if(session('level') == 'pelanggan')
                    <li>
                        <a href="{{ route('pelanggan_data.notifikasi') }}" class="nav-link position-relative">
                            <i class="fas fa-bell"></i>
                        </a>
                    </li>
                    @endif
                    <li><a href="{{ url('pelanggan_data/profil') }}"> <i class="fas fa-user"></i></a></li>
                    <li>
                        <a href="#" onclick="logoutPelanggan(event);">
                            <i id="logout-icon" class="fas fa-sign-out-alt"></i>
                        </a>
                        <form id="logout-form" action="{{ route('auth.logout_pelanggan') }}" method="POST"
                            class="d-none">
                            @csrf
                        </form>
                    </li>
                @else
                    <li><a href="{{ url('auth/login_pelanggan') }}"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                @endif
            </ul>
        </nav>
    </div>
</header>

<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="product-filter-form" method="GET" action="{{ url('/katalog') }}">
                    <!-- Cari Kata -->
                    <div class="mb-3">
                        <label for="keyword">Cari Kata</label>
                        <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari..."
                            value="{{ request('keyword') }}">
                    </div>

                    <!-- Jenis Produk -->
                    <div class="mb-3">
                        <label for="jenis_produk">Jenis Produk</label>
                        <select name="jenis_produk" id="jenis_produk" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach ($jenis_produk_dropdown as $jenis)
                                <option value="{{ $jenis['jenis_produk'] }}"
                                    {{ request('jenis_produk') == $jenis['jenis_produk'] ? 'selected' : '' }}>
                                    {{ $jenis['jenis_produk'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rentang Harga -->
                    <div class="mb-3">
                        <label>Rentang Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="harga_min" class="form-control" placeholder="Min"
                                value="{{ request('harga_min') }}">
                            <span class="input-group-text">-</span>
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="harga_max" class="form-control" placeholder="Max"
                                value="{{ request('harga_max') }}">
                        </div>
                    </div>

                    <!-- Sorting Harga -->
                    <div class="mb-3">
                        <label>Urutkan Berdasarkan Harga</label>
                        <select name="sort_harga" class="form-control">
                            <option value="">Default</option>
                            <option value="asc" {{ request('sort_harga') == 'asc' ? 'selected' : '' }}>Harga
                                Terendah</option>
                            <option value="desc" {{ request('sort_harga') == 'desc' ? 'selected' : '' }}>Harga
                                Tertinggi</option>
                        </select>
                    </div>

                    <!-- Sorting Nama -->
                    <div class="mb-3">
                        <label>Sorting Nama</label>
                        <select name="sort_nama" class="form-control">
                            <option value="">Pilih Urutan</option>
                            <option value="a-z" {{ request('sort_nama') == 'a-z' ? 'selected' : '' }}>A - Z
                            </option>
                            <option value="z-a" {{ request('sort_nama') == 'z-a' ? 'selected' : '' }}>Z - A
                            </option>
                        </select>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>

                    <!-- Tombol Reset -->
                    <a href="{{ url('home_toko/katalog') }}" class="btn btn-secondary w-100 mt-2">Reset Filter</a>
                </form>
            </div>
        </div>
    </div>
</div>