<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title2 ?? config('app.name', 'Toko Online') }}</title>
    <meta name="description" content="{{ $meta_description ?? $dataWebsite['nama_toko'] . ' - Toko Online Terpercaya' }}">
    <meta name="keywords" content="{{ $meta_keywords ?? 'toko online, belanja, produk, fashion' }}">
    <meta property="og:title" content="{{ $title2 ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta_description ?? $dataWebsite['nama_toko'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(isset($produk) && isset($produk['foto_produk']))
    <meta property="og:image" content="{{ url('fotoproduk/' . $produk['foto_produk']) }}">
    @endif

    <!-- Preconnect untuk performance -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">

    <!-- CSS Bootstrap 5 (local) -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-vibe.min.css') }}">
    <!-- CSS Custom vibe-store -->
    <link rel="stylesheet" href="{{ asset('assets/css/vibe-store.css') }}">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <!-- Google Fonts: Cairo (support Indonesia) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

    {{-- Custom CSS untuk integrasi Laravel --}}
    <style>
        /* Override RTL ke LTR */
        html {
            direction: ltr;
        }

        [dir="rtl"] {
            direction: ltr !important;
        }

        /* Navbar scrolled state */
        .navbar-modern.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-modern.scrolled .nav-link {
            color: #333 !important;
        }

        .navbar-modern.scrolled .search-box input {
            background: rgba(0, 0, 0, 0.05) !important;
            color: #333;
            border-color: rgba(0, 0, 0, 0.1);
        }

        .navbar-modern.scrolled .search-box input::placeholder {
            color: #999;
        }

        .navbar-modern.scrolled .search-box i {
            color: #667eea;
        }

        .navbar-modern.scrolled .cart-btn {
            background: linear-gradient(45deg, #667eea, #764ba2) !important;
            border-color: transparent;
        }

        .navbar-modern.scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28102, 126, 234, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Product card integration */
        .product-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }

        .img-container {
            position: relative;
            overflow: hidden;
        }

        .img-container img {
            transition: transform 0.3s ease;
        }

        .product-card:hover .img-container img {
            transform: scale(1.05);
        }

        /* Badge positioning */
        .product-card .badge {
            z-index: 10;
        }

        /* Modal enhancements */
        .modal-backdrop.show {
            opacity: 0.8 !important;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
        }

        /* Cart offcanvas */
        .offcanvas {
            width: 380px !important;
        }

        @media (max-width: 576px) {
            .offcanvas {
                width: 100% !important;
                max-width: 380px;
            }
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
        }

        .cart-item .img-wrapper {
            flex-shrink: 0;
            width: 70px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
        }

        .cart-item .offcan-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item .product-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item .product-name {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item .price {
            font-size: 13px;
            font-weight: 700;
            color: #dc3545;
            background: #fff0f0;
            padding: 2px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .cart-item .btn-danger {
            padding: 4px 8px;
            font-size: 12px;
        }

        .cart-footer {
            border-top: 1px solid #e9ecef;
            padding-top: 16px;
            margin-top: 16px;
        }

        /* Product filters */
        .product-filters .btn {
            transition: all 0.3s ease;
        }

        .product-filters .btn.active {
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Responsive hero */
        @media (max-width: 768px) {
            .hero-section {
                min-height: auto;
                padding: 120px 0 60px;
            }

            .hero-content {
                text-align: center;
                margin-bottom: 3rem;
            }

            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-image {
                height: 400px;
            }

            .floating-card {
                width: 200px;
                position: relative;
                margin: 1rem auto;
                animation: none;
            }

            .floating-card-2 {
                position: relative;
                top: 0;
                left: 0;
                margin-top: 1rem;
            }

            .floating-card:first-child {
                position: relative;
                top: 0;
                right: 0;
            }
        }

        /* Search input focus */
        .search-box input:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Loading state */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Toast notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Product modal carousel */
        #carouselExampleControls .carousel-inner img {
            max-height: 400px;
            object-fit: contain;
        }

        /* Counter animation */
        .counter {
            font-variant-numeric: tabular-nums;
        }

        /* Trust badges */
        .trust-features .badge,
        .contact-features .badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.7rem;
        }

        /* Form enhancements */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Social buttons */
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            transform: translateY(-3px) scale(1.1);
        }
    </style>
</head>

<body>
    <!-- Skip link for accessibility -->
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    {{-- Scripts --}}
    <!-- Bootstrap Bundle (local) -->
    <script src="{{ asset('assets/js/bootstrap-vibe.bundle.min.js') }}" defer></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <!-- Vibe Store Custom JS -->
    <script src="{{ asset('assets/js/vibe-store.js') }}" defer></script>
    {{-- Laravel Integration JS --}}
    <script>
        // Global Laravel data
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}',
            userLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            userId: {{ auth()->id() ?? 'null' }},
            cartCount: {{ session('cart_count') ?? 0 }},
            routes: {
                addToCart: '{{ route('pelanggan_data.add_to_cart') }}',
                buyNow: '{{ route('pelanggan_data.beliLangsung', ['id_stok' => '__ID__']) }}',
                getCart: '{{ route('pelanggan_data.get_cart') }}',
                updateCart: '{{ route('pelanggan_data.update_cart', ['id_keranjang' => '__ID__']) }}',
                removeFromCart: '{{ route('pelanggan_data.remove_from_cart', ['id_keranjang' => '__ID__']) }}',
                checkout: '{{ route('pelanggan_data.checkout') }}',
                login: '{{ route('auth.login_pelanggan') }}',
                register: '{{ route('auth.register_pelanggan') }}',
                productDetail: '{{ route('home_toko.detail_produk', ['nama_produk' => '__SLUG__']) }}',
                storeDetail: '{{ route('view.toko', ['nama_toko' => '__NAMA__']) }}',
            }
        };

        // Helper functions
        function route(name, params = {}) {
            let url = Laravel.routes[name];
            if (url) {
                Object.keys(params).forEach(key => {
                    url = url.replace('__' + key.toUpperCase() + '__', params[key]);
                });
            }
            return url;
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow-lg`;
            toast.style.borderRadius = '15px';
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
                toast.classList.add('fade');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-out-cubic',
                    once: true,
                    offset: 100
                });
            }
        });
    </script>
    {{-- Page-specific scripts --}}
    @stack('scripts')

    {{-- SweetAlert2 untuk notifikasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('pesan_welcome'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('pesan_welcome') }}',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end',
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('pesan_cart'))
        <script>
            Swal.fire({
                icon: '{{ session('pesan_cart') == 'Produk berhasil ditambahkan ke keranjang!' ? 'success' : 'error' }}',
                title: '{{ session('pesan_cart') == 'Produk berhasil ditambahkan ke keranjang!' ? 'Berhasil!' : 'Oops...' }}',
                text: '{{ session('pesan_cart') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('hapus_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('hapus_success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('pesan_beli'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Pembelian Berhasil!',
                text: '{{ session('pesan_beli') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'Tutup'
            });
        </script>
    @endif

    @if (session('pesan_bayar'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: '{{ session('pesan_bayar') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('pesan_profil'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('pesan_profil') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('pesan_logout'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Logout Berhasil!',
                text: '{{ session('pesan_logout') }} Anda telah berhasil keluar dari akun pelanggan.',
                timer: 2000,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = "{{ url('home_toko/index') }}";
                }
            });
        </script>
    @endif

    {{-- Chat Widget --}}
    @include('layouts.chat_widget')
</body>

</html>