<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title2 }}</title>
    <meta name="description" content="{{ $meta_description ?? $dataWebsite['nama_toko'] . ' - Toko Online Terpercaya' }}">
    <meta name="keywords" content="{{ $meta_keywords ?? 'toko online, belanja, produk' }}">
    <meta property="og:title" content="{{ $title2 }}">
    <meta property="og:description" content="{{ $meta_description ?? $dataWebsite['nama_toko'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(isset($produk) && isset($produk['foto_produk']))
    <meta property="og:image" content="{{ url('fotoproduk/' . $produk['foto_produk']) }}">
    @endif
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template_admin/dist/css/toko.css') }}">
    <!-- Icon -->
    <link href="{{ asset('icon/SugarCRM-Outright.ico') }}" rel="shortcut icon">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body>

    <!-- Section -->
    <section class="section">
        @include('layouts.v_navbar')
        @yield('content')
    </section>

    <footer>
        <div class="footer-content">
            <div>
                <h4>Tentang Kami</h4>
                <p> {{ $dataWebsite['nama_toko'] }}</p>
            </div>
            <div>
                <h4>Link Cepat</h4>
                <ul>
                    <li><a href="{{ url('home_toko/syaket') }}">Syarat & Ketentuan</a></li>
                    <li><a href="{{ url('home_toko/bantuan') }}">Bantuan / FAQ</a></li>
                </ul>
            </div>
            <div class="marketplace">
                <h4>Temukan Kami di Sosial Media</h4>
                <ul>
                    <li>
                        <a href="{{ config('settings.instagram_link', '#') }}" target="_blank">
                            <img src="https://img.icons8.com/fluency/24/instagram-new.png " alt="Instagram"
                                style="vertical-align: middle; margin-right: 8px;">
                            Instagram
                        </a>
                    </li>
                    <li>
                        <a href="{{ config('settings.facebook_link', '#') }}" target="_blank">
                            <img src="https://img.icons8.com/fluency/24/facebook-new.png " alt="Facebook"
                                style="vertical-align: middle; margin-right: 8px;">
                            Facebook
                        </a>
                    </li>
                    <li>
                        <a href="{{ config('settings.tiktok_link', '#') }}" target="_blank">
                            <img src="https://img.icons8.com/fluency/24/tiktok.png " alt="TikTok"
                                style="vertical-align: middle; margin-right: 8px;">
                            Tiktok
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js "></script>

    <!-- JS Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle menu mobile navbar
        function toggleNavbar() {
            document.querySelector("header nav").classList.toggle("active");
        }

        // Toggle dropdown saat diklik (untuk mobile)
        document.querySelectorAll('header nav li.dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.stopPropagation();
                    document.querySelectorAll('header nav li.dropdown').forEach(other => {
                        if (other !== this) other.classList.remove('active');
                    });
                    this.classList.toggle('active');
                }
            });
        });

        // Tutup dropdown dan search bar jika klik di luar area
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const dropdowns = document.querySelectorAll('header nav li.dropdown');
                let targetIsDropdown = false;
                dropdowns.forEach(dd => {
                    if (dd.contains(e.target)) targetIsDropdown = true;
                });
                if (!targetIsDropdown) dropdowns.forEach(dd => dd.classList.remove('active'));

                // Tutup search bar jika klik di luar area search bar dan ikon pencarian
                const searchInput = document.querySelector('.search-input');
                const searchIcon = document.querySelector('.search-icon');
                const inputGroup = document.querySelector('.input-group');
                if (searchInput.classList.contains('active') &&
                    !inputGroup.contains(e.target) &&
                    !searchIcon.contains(e.target)) {
                    searchInput.classList.remove('active');
                    searchIcon.style.display = 'block';
                }
            }
        });

        // Toggle input pencarian dan ikon pencarian hanya pada layar kecil
        function toggleSearchBar() {
            if (window.innerWidth <= 768) {
                const searchInput = document.querySelector('.search-input');
                const searchIcon = document.querySelector('.search-icon');
                searchInput.classList.toggle('active');
                searchIcon.style.display = searchInput.classList.contains('active') ? 'none' : 'block';
            }
        }
    </script>

    <!-- JavaScript untuk Carousel -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carousels = document.querySelectorAll('.promo-carousel');

            carousels.forEach(carousel => {
                const images = carousel.querySelectorAll('.carousel-img');
                const dots = carousel.querySelectorAll('.dot');
                let currentIndex = 0;
                let timer = null;

                if (images.length === 0) {
                    return;
                }

                function showSlide(index) {
                    currentIndex = (index + images.length) % images.length;
                    images.forEach((img, i) => img.classList.toggle('active', i === currentIndex));
                    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
                }

                function next() {
                    showSlide(currentIndex + 1);
                }

                function startAuto() {
                    if (timer) clearInterval(timer);
                    timer = setInterval(next, 5000);
                }

                // Navigasi lewat indikator bulat (reset timer auto-slide)
                dots.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        showSlide(i);
                        startAuto();
                    });
                });

                startAuto();
            });
        });
    </script>

<!-- JavaScript untuk gambar -->
    <script>
        // Preview gambar saat upload
        function bacaGambar(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#gambar_load').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('#preview_gambar').change(function() {
            bacaGambar(this);
        });

        // Toggle show/hide password
        function myFunction() {
            var x = document.getElementById("ShowPass");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>

    @if (session('pesan_welcome'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('pesan_welcome') }}',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-start',
                background: '#fff',
                color: '#333',
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-toast'
                }
            });
        </script>
    @endif



    @if (session('pesan_cart'))
        @if (session('pesan_cart') == 'Produk berhasil ditambahkan ke keranjang!')
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('pesan_cart') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @else
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('pesan_cart') }}'
                });
            </script>
        @endif
    @endif

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Anda akan menghapus produk: " + nama,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form secara programatis
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>

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

    <script>
        // Cek apakah ada pesan sukses dari session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Cek apakah ada pesan error dari session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'Tutup'
            });
        @endif
    </script>

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

    <script>
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

    @include('layouts.chat_widget')
</body>

</html>
