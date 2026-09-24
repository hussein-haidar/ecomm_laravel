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
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-vibe.min.css') }}?v=2">
    <!-- CSS Custom vibe-store -->
    <link rel="stylesheet" href="{{ asset('assets/css/vibe-store.css') }}?v=2">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <!-- Google Fonts: Cairo (support Indonesia) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="{{ !empty($dataWebsite['logo_website']) ? asset('logo_website/' . $dataWebsite['logo_website']) : asset('assets/images/logo.png') }}" type="image/png">

    {{-- Custom CSS untuk integrasi Laravel --}}
    <style>
        /* Override RTL ke LTR */
        html {
            direction: ltr;
        }

        [dir="rtl"] {
            direction: ltr !important;
        }

        /* Warna aksen navbar: biru - hijau (sesuai tema HAPPYSHOP) */
        :root {
            --nav-grad: linear-gradient(135deg, #0ea5e9, #10b981);
            --nav-grad-hover: linear-gradient(135deg, #0b8ec8, #0da271);
        }

        /* Navbar scrolled state */
        .navbar-modern.scrolled {
            background: #ffffff !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .navbar-modern.scrolled .nav-link {
            color: #0f172a !important;
        }

        .navbar-modern.scrolled .search-box input {
            background: #f8fafc !important;
            color: #0f172a;
            border-color: #e2e8f0;
        }

        .navbar-modern.scrolled .search-box input::placeholder {
            color: #94a3b8;
        }

        .navbar-modern.scrolled .search-box i {
            color: #0ea5e9;
        }

        .navbar-modern.scrolled .cart-btn {
            background: var(--nav-grad) !important;
            border-color: transparent;
        }

        .navbar-modern.scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2814, 165, 233, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Professional white navbar (always readable on any page) */
        .navbar-modern,
        .navbar-modern.navbar {
            background: #ffffff !important;
            border-bottom: none !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            padding: 0.45rem 0 !important;
        }

        .navbar-modern .navbar-brand {
            position: relative;
            z-index: 1031;
            flex-shrink: 0;
        }

        .navbar-modern .brand-logo {
            flex-shrink: 0;
        }

        .navbar-modern .brand-text {
            color: #0f172a;
            font-weight: 700;
        }

        .navbar-modern .nav-link {
            color: #0f172a !important;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0 2px;
            padding: 6px 8px !important;
            white-space: nowrap;
        }

        .navbar-modern .nav-link:hover,
        .navbar-modern .nav-link.active {
            background: var(--nav-grad);
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .navbar-modern .nav-link.no-caret::after {
            display: none;
        }

        .navbar-modern .search-box input {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #0f172a;
            width: 100%;
            padding-left: 40px !important;
        }

        .navbar-modern .search-box input::placeholder {
            color: #94a3b8;
        }

        .navbar-modern .search-box .search-submit {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 38px;
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            color: #0ea5e9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            line-height: 1;
        }

        .navbar-modern .search-box .search-submit i {
            position: static !important;
            left: auto !important;
            top: auto !important;
            transform: none !important;
            font-size: 0.85rem;
            color: inherit;
        }

        .navbar-modern .search-box .search-submit:hover {
            color: #0b8ec8;
        }

        .navbar-modern .search-box {
            width: 200px;
        }

        @media (max-width: 767.98px) {
            .navbar-modern .search-box {
                width: 100%;
            }
        }

        @media (min-width: 1200px) {
            .navbar-modern .search-box {
                width: 240px;
            }
        }

        .navbar-modern .cart-btn {
            background: var(--nav-grad) !important;
            border: none;
            color: #fff !important;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
        }

        .navbar-modern .cart-btn:hover {
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
        }

        .navbar-modern .notif-btn {
            background: #eef6ff;
            color: #0ea5e9;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .navbar-modern .notif-btn:hover {
            background: var(--nav-grad);
            color: #fff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
        }

        .navbar-modern .auth-btn {
            border-radius: 50rem;
            font-weight: 600;
            white-space: nowrap;
            padding: 6px 14px !important;
            font-size: 0.85rem;
        }

        .navbar-modern .auth-btn-solid {
            background: var(--nav-grad);
            border: none;
            color: #fff;
        }

        .navbar-modern .auth-btn-solid:hover {
            background: var(--nav-grad-hover);
            color: #fff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
        }

        .navbar-modern .navbar-toggler {
            border: none !important;
            padding: 4px 8px;
        }

        .navbar-modern .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2814, 165, 233, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 1.5em;
            height: 1.5em;
        }

        /* Dropdown navbar: putih solid, shadow proper, z-index */
        .dropdown-menu-modern {
            background: #ffffff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
            border: 1px solid #eef2f7 !important;
            border-radius: 12px;
            z-index: 1040;
        }

        .dropdown-menu-modern .dropdown-item {
            color: #0f172a !important;
            padding: 8px 16px;
        }

        .dropdown-menu-modern .dropdown-item:hover {
            background: #f1f3f9;
            color: #0ea5e9 !important;
        }

        .avatar-placeholder {
            flex-shrink: 0;
        }

        .avatar-placeholder {
            flex-shrink: 0;
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

        .product-card .img-container,
        .img-container {
            position: relative;
            overflow: hidden;
            height: auto !important;
            border-radius: 20px 20px 0 0;
            background: #f8f9fa;
        }

        .product-card .img-container img,
        .img-container img {
            width: 100% !important;
            height: auto !important;
            max-height: 400px;
            max-width: none !important;
            object-fit: contain !important;
            display: block;
            transition: transform 0.4s ease;
        }

        .product-card:hover .img-container img {
            transform: scale(1.08);
        }

        .product-card .card-body {
            padding: 1.1rem 1.25rem !important;
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

        /* Compact professional hero */
        .hero-section {
            min-height: auto !important;
            padding: 130px 0 70px;
        }

        /* Search input focus */
        .search-box input:focus {
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
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

        /* ===== Professional Footer ===== */
        .footer-modern {
            background: #1b1e2b;
            color: rgba(255, 255, 255, 0.85);
            border-top: 3px solid transparent;
            border-image: linear-gradient(90deg, #667eea, #764ba2) 1;
        }

        .footer-modern .footer-widget .footer-title {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-modern .footer-widget .footer-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .footer-modern .footer-links li {
            margin-bottom: 10px;
        }

        .footer-modern .footer-links a,
        .footer-modern .footer-contact a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .footer-modern .footer-links a:hover {
            color: #a5b4fc;
            padding-left: 6px;
        }

        .footer-modern .footer-contact .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .footer-modern .social-btn {
            width: 42px;
            height: 42px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.12);
        }

        .footer-modern .social-btn:hover {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #fff;
        }

        .footer-modern .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.9);
            color: #334155 !important;
            border-radius: 8px;
            padding: 3px 9px;
            font-size: 0.72rem;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .footer-modern .payment-badge i {
            font-size: 0.9rem;
        }

        .footer-modern .footer-bottom {
            border-top-color: rgba(255, 255, 255, 0.1) !important;
        }

        .footer-modern .footer-bottom .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Fix global img rule from template (keeps small logos/avatars their size) */
        img {
            max-width: 100%;
            height: auto;
        }

        .navbar-modern .brand-logo img {
            width: 36px !important;
            height: 36px !important;
        }

        .navbar-modern img.navbar-avatar {
            width: 28px !important;
            height: 28px !important;
            object-fit: cover;
            border: 2px solid rgba(14, 165, 233, 0.5);
            background: var(--nav-grad);
        }

        .navbar-modern .avatar-placeholder {
            width: 28px !important;
            height: 28px !important;
            background: var(--nav-grad);
            font-size: 0.8rem;
        }

        .navbar-modern .brand-text {
            color: #0f172a !important;
            background: none !important;
            -webkit-background-clip: unset !important;
            background-clip: unset !important;
            -webkit-text-fill-color: #0f172a !important;
        }

        .footer-modern .footer-brand img {
            width: 48px !important;
            height: 48px !important;
        }

        .navbar-brand img,
        .footer-brand img,
        .social-btn img,
        .brand-logo img {
            object-fit: cover;
        }

        /* ===== Layout: Proper content separation ===== */
        body {
            padding-top: 60px;
            background: #f8fafc;
        }

        main#main-content {
            background: #fff;
            min-height: calc(100vh - 60px);
        }

        .hero-section {
            margin-top: 0;
            background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
        }

        .hero-section {
            min-height: auto !important;
            padding: 100px 0 60px;
        }

        /* ===== Mobile Navigation Drawer ===== */
        .mobile-nav {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            display: none;
            pointer-events: none;
        }

        .mobile-nav.active {
            display: block;
            pointer-events: auto;
        }

        .mobile-nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1051;
        }

        .mobile-nav.active .mobile-nav-overlay {
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav-drawer {
            position: fixed;
            top: 0;
            right: -320px;
            bottom: 0;
            width: 320px;
            max-width: 85vw;
            background: #fff;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.15);
            z-index: 1052;
            display: flex;
            flex-direction: column;
            transition: right 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow-y: auto;
        }

        .mobile-nav.active .mobile-nav-drawer {
            right: 0;
        }

        .mobile-nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .mobile-nav-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .mobile-nav-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6c757d;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .mobile-nav-close:hover {
            background: #e9ecef;
            color: #1a1a1a;
        }

        .mobile-nav-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section:last-child {
            margin-bottom: 0;
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 0.75rem;
            padding: 0 0.5rem;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-list li {
            margin-bottom: 0.25rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-item:hover,
        .nav-item:focus {
            background: #f1f3f9;
            color: #0ea5e9;
        }

        .nav-item i:first-child {
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
            color: #6c757d;
        }

        .nav-item:hover i:first-child {
            color: #0ea5e9;
        }

        .nav-item span {
            flex: 1;
        }

        .nav-item .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
        }

        .nav-item-primary {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: #fff !important;
        }

        .nav-item-primary:hover {
            background: linear-gradient(135deg, #0b8ec8, #0da271);
            color: #fff !important;
        }

        .nav-item-primary i {
            color: #fff !important;
        }

        .nav-item-danger {
            color: #dc3545 !important;
        }

        .nav-item-danger:hover {
            background: #fff0f0;
            color: #dc3545 !important;
        }

        /* Dropdown in mobile nav */
        .dropdown-trigger {
            position: relative;
        }

        .dropdown-arrow {
            margin-left: auto;
            transition: transform 0.2s ease;
            font-size: 0.8rem;
        }

        .dropdown-trigger[aria-expanded="true"] .dropdown-arrow {
            transform: rotate(180deg);
        }

        .nav-sublist {
            list-style: none;
            padding: 0;
            margin: 0.25rem 0 0.5rem 2.5rem;
            border-left: 2px solid #e9ecef;
        }

        .nav-sublist li {
            margin-bottom: 0.125rem;
        }

        .nav-sublink {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 0.75rem;
            color: #555;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-sublink:hover {
            background: #f1f3f9;
            color: #0ea5e9;
            padding-left: 1rem;
        }

        .nav-sublink i:first-child {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .sublink-arrow {
            margin-left: auto;
            font-size: 0.7rem;
            color: #999;
        }

        .mobile-nav-footer {
            padding: 1rem;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #999;
            font-size: 0.8rem;
        }

        /* ===== Filter Bottom Sheet ===== */
        .filter-sheet-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1060;
        }

        .filter-sheet-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .filter-sheet {
            position: fixed;
            bottom: -100%;
            left: 0;
            right: 0;
            max-height: 85vh;
            background: #fff;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
            z-index: 1061;
            display: flex;
            flex-direction: column;
            transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .filter-sheet.active {
            bottom: 0;
        }

        .filter-sheet-handle {
            width: 40px;
            height: 5px;
            background: #dee2e6;
            border-radius: 3px;
            margin: 1rem auto 0;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .filter-sheet-handle:hover {
            background: #adb5bd;
        }

        .filter-sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 20px 20px 0 0;
        }

        .filter-sheet-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .filter-sheet-header .btn-close {
            opacity: 0.7;
            padding: 0.5rem;
        }

        .filter-sheet-header .btn-close:hover {
            opacity: 1;
        }

        .filter-sheet-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
        }

        .filter-sheet-body .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .filter-sheet-body .form-control,
        .filter-sheet-body .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 0.625rem 1rem;
        }

        .filter-sheet-body .form-control:focus,
        .filter-sheet-body .form-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
        }

        .filter-sheet-body .input-group-text {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
        }

        .filter-sheet-body .input-group-text:first-child {
            border-radius: 10px 0 0 10px;
            border-right: none;
        }

        .filter-sheet-body .input-group-text:last-child {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .filter-sheet-body .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1rem;
        }

        @media (min-width: 769px) {
            .mobile-nav,
            .filter-sheet-overlay,
            .filter-sheet {
                display: none !important;
            }
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

        // Navbar scroll state
        function updateNavbar() {
            const nav = document.querySelector('.navbar-modern');
            if (nav) {
                nav.classList.toggle('scrolled', window.scrollY > 40);
            }
        }
        window.addEventListener('scroll', updateNavbar, { passive: true });
        window.addEventListener('DOMContentLoaded', updateNavbar);

        // Mobile Navigation Drawer
function toggleNavbar() {
            const nav = document.getElementById('mobileMenu');
            const toggler = document.querySelector('.navbar-toggler');
            const isOpen = nav.classList.toggle('active');
            if (toggler) {
                toggler.setAttribute('aria-expanded', isOpen);
            }
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        // Dropdown in mobile nav (breakpoint 992px matching CSS)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 992) {
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
                if (window.innerWidth <= 992) {
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

            // Tutup mobile nav drawer jika terbuka
            const mobileNav = document.getElementById('mobileMenu');
            if (mobileNav && mobileNav.classList.contains('active')) {
                toggleNavbar();
            }

            if (window.innerWidth <= 992) {
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
            }
        });

        // Logout
        function logoutPelanggan(event) {
            event.preventDefault();
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
                    // Cari form logout terdekat (desktop atau mobile)
                    const btn = event.target.closest('button');
                    const form = btn ? btn.closest('form') : null;
                    if (form) {
                        form.submit();
                    } else {
                        // Fallback: cari form dengan ID yang dikenal
                        const formDesktop = document.getElementById('logout-form-desktop');
                        const formMobile = document.getElementById('logout-form-mobile');
                        (formDesktop || formMobile)?.submit();
                    }
                }
            });
        }
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