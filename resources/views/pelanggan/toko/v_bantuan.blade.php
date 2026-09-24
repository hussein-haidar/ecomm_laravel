@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const hash = this.getAttribute('href');
                if (hash.length <= 1) return;
                const target = document.querySelector(hash);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Auto-open FAQ from URL hash
        if (window.location.hash) {
            const target = document.querySelector(window.location.hash);
            if (target && target.classList.contains('accordion-collapse')) {
                const bsCollapse = new bootstrap.Collapse(target, { show: true });
            }
        }
    });
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            {{-- Header --}}
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h1 class="fw-bold mb-2">Bantuan / FAQ</h1>
                <p class="text-muted">Temukan jawaban atas pertanyaan umum seputar belanja di <strong>{{ $dataWebsite['nama_toko'] }}</strong></p>
            </div>

            {{-- Search FAQ --}}
            <div class="card border-0 shadow-sm mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body p-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="faqSearch" class="form-control form-control-lg ps-5" placeholder="Cari pertanyaan... (contoh: pengiriman, pembayaran, retur)">
                    </div>
                </div>
            </div>

            {{-- FAQ Categories --}}
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-5" data-aos="fade-up" data-aos-delay="200" id="faqCategories">
                <button class="btn btn-primary rounded-pill px-4 active" data-category="all">Semua</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pemesanan">Pemesanan</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pembayaran">Pembayaran</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pengiriman">Pengiriman</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="retur">Retur & Garansi</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="akun">Akun & Profil</button>
            </div>

            {{-- FAQ Accordion --}}
            <div class="accordion accordion-flush" id="faqAccordion">
                {{-- PESANAN --}}
                @php
                    $faqs = [
                        'pemesanan' => [
                            [
                                'q' => 'Bagaimana cara melakukan pemesanan?',
                                'a' => '1) Pilih produk yang diinginkan di halaman Katalog atau Toko.<br>
                                      2) Pilih ukuran (jika ada) dan jumlah, lalu klik "Tambah ke Keranjang".<br>
                                      3) Buka Keranjang (ikon keranjang di navbar), centang produk, klik "Checkout".<br>
                                      4) Isi alamat pengiriman, pilih ekspedisi & metode pembayaran.<br>
                                      5) Klik "Buat Pesanan" lalu lakukan pembayaran sesuai instruksi.',
                            ],
                            [
                                'q' => 'Apakah saya harus login untuk memesan?',
                                'a' => 'Ya. Anda harus mendaftar & login untuk checkout, melihat riwayat, melacak pengiriman, dan mengelola profil. Login juga memungkinkan fitur Chat Penjual & Wishlist.',
                            ],
                            [
                                'q' => 'Bisa ganti alamat/ukuran setelah pesanan dibuat?',
                                'a' => 'Tidak bisa diubah lewat sistem setelah checkout. Segera hubungi toko via <strong>Chat Penjual</strong> (di halaman Detail Produk / Keranjang / Riwayat) sebelum status berubah jadi "Dikemas/Dikirim".',
                            ],
                            [
                                'q' => 'Produk yang saya inginkan stoknya habis. Bisa notif kalau stok masuk?',
                                'a' => 'Fitur notif stok belum tersedia. Saran: cek berkala atau chat penjual via tombol <strong>Chat Penjual</strong> di halaman produk untuk tanya kapan restock.',
                            ],
                        ],
                        'pembayaran' => [
                            [
                                'q' => 'Metode pembayaran apa saja yang tersedia?',
                                'a' => '<strong>Via Midtrans Payment Gateway:</strong><br>
                                      • Virtual Account (BCA, BRI, BNI, Mandiri, Permata, CIMB, dll)<br>
                                      • E-Wallet (GoPay, ShopeePay, Dana, OVO, LinkAja, Sakuku)<br>
                                      • Retail/Minimarket (Alfamart, Indomaret via VA)<br>
                                      • Kartu Kredit/Debit (Visa, Mastercard, JCB)<br>
                                      • QRIS<br><br>
                                      <strong>Manual Transfer:</strong> Rekening toko (BCA/BRI/Mandiri) - bukti bayar wajib diupload.<br>
                                      <strong>COD:</strong> Bayar di tempat (area & nominal terbatas).',
                            ],
                            [
                                'q' => 'Berapa batas waktu pembayaran?',
                                'a' => '<strong>Midtrans (VA/E-Wallet/QRIS/Kartu):</strong> Ada timer real-time (biasanya 15-24 jam).<br>
                                      <strong>Transfer Manual:</strong> 10 menit setelah checkout (timer di halaman Status Bayar).<br>
                                      <strong>COD:</strong> Bayar saat kurir sampai.',
                            ],
                            [
                                'q' => 'Sudah bayar tapi status belum berubah jadi "Dibayar".',
                                'a' => '<strong>Midtrans:</strong> Biasanya otomatis < 5 menit. Jika > 1 jam, cek email Midtrans/notif WhatsApp.<br>
                                      <strong>Transfer Manual:</strong> Admin verifikasi manual (jam kerja 08-22 WIB). Pastikan bukti JPG/PNG < 2MB, nominal & nomor rekening terlihat jelas.<br>
                                      <strong>COD:</strong> Status update setelah kurir konfirmasi pembayaran.',
                            ],
                            [
                                'q' => 'Bisa bayar cicilan?',
                                'a' => 'Ya, via Midtrans dengan Kartu Kredit (biasanya 3/6/12 bln) atau Akulata/Kredivo (pilih di halaman pembayaran Midtrans). Syarat & bunga mengikuti kebijakan masing-masing penyedia.',
                            ],
                        ],
                        'pengiriman' => [
                            [
                                'q' => 'Berapa lama estimasi pengiriman?',
                                'a' => 'Tergantung asal toko & kota tujuan:<br>
                                      • <strong>Jawa & Bali:</strong> 1-3 hari kerja<br>
                                      • <strong>Sumatra:</strong> 2-4 hari kerja<br>
                                      • <strong>Kalimantan/Sulawesi/NTB/NTT:</strong> 3-6 hari kerja<br>
                                      • <strong>Papua/Maluku:</strong> 5-10 hari kerja<br><br>
                                      <em>Estimasi ini tidak mengikat & tidak termasuk hari libur/force majeure.</em>',
                            ],
                            [
                                'q' => 'Cara cek nomor resi & tracking?',
                                'a' => '1) Buka menu <strong>Pengiriman</strong> di navbar (ikon truk).<br>
                                      2) Klik "Lacak" pada pesanan.<br>
                                      3) Atau klik link WhatsApp/Email notif pengiriman.<br>
                                      Nomor resi juga terlihat di detail pesanan (menu Riwayat Beli).',
                            ],
                            [
                                'q' => 'Paket tertulis "Diterima" tapi saya belum terima.',
                                'a' => '1) Cek ke tetangga/RT/security/lokasi penitipan kurir.<br>
                                      2) Tanya ke kurir via nomor telepon di detail tracking.<br>
                                      3) Jika 1x24 jam tidak ketemu: hubungi kami via Chat/WA dengan nomor resi & bukti tidak terima (foto CCTV/skck RT). Kami akan klaim ke ekspedisi.',
                            ],
                            [
                                'q' => 'Bisa ganti alamat pengiriman setelah dikirim?',
                                'a' => 'Tidak bisa via sistem. Hubungi <strong>langsung kurir</strong> via nomor telepon di tracking. Beberapa ekspedisi izinkan ganti alamat (bisa ada biaya tambahan).',
                            ],
                        ],
                        'retur' => [
                            [
                                'q' => 'Syarat retur/produk diganti?',
                                'a' => '<strong>Diterima jika:</strong> Rusak/pecah, cacat pabrik, salah kirim (warna/ukuran/model), kedaluwarsa.<br>
                                      <strong>Batas waktu:</strong> Maksimal <strong>2 hari</strong> setelah terima (berdasarkan bukti terima ekspedisi).<br>
                                      <strong>Kondisi:</strong> Asli, tidak dipakai, tag/packaging utuh, lengkap aksesoris.',
                            ],
                            [
                                'q' => 'Cara ajukan retur?',
                                'a' => '1) Foto/video bukti kerusakan/ketidaksesuaian (wajib jelas).<br>
                                      2) Buka menu <strong>Pengiriman</strong> → Klik "Ajukan Retur" pada pesanan.<br>
                                      3) Atau Chat Penjual langsung dari halaman Detail Produk/Riwayat.<br>
                                      4) Tunggu verifikasi (max 1x24 jam) → Dapatkan instruksi & alamat pengembalian.',
                            ],
                            [
                                'q' => 'Siapa bayar ongkir retur?',
                                'a' => '<strong>Kesalahan toko/ekspedisi</strong> (rusak, salah kirim, cacat): <strong>Toko bayar</strong> (kita kirim label return/transfer ongkir).<br>
                                      <strong>Alasan pribadi</strong> (salah pilih, tidak suka, ukuran tidak pas): <strong>Pembeli bayar</strong> ongkir bolak-balik.',
                            ],
                            [
                                'q' => 'Produk Flash Sale / Promo bisa diretur?',
                                'a' => 'Hanya jika <strong>rusak/cacat/salah kirim</strong>. Retur alasan pribadi (tidak suka, salah ukuran) <strong>tidak diterima</strong> untuk produk Flash Sale/Promo/Bundle. Cek deskripsi & tabel ukuran sebelum beli.',
                            ],
                        ],
                        'akun' => [
                            [
                                'q' => 'Lupa password, gimana reset?',
                                'a' => 'Klik <strong>"Lupa Password"</strong> di halaman Login → Masukkan email terdaftar → Cek email (cek folder Spam) → Klik link reset (berlaku 60 menit) → Buat password baru.',
                            ],
                            [
                                'q' => 'Gimana ganti email/nomor HP?',
                                'a' => 'Login → Menu <strong>Profil Saya</strong> → Edit Profil → Ganti email/nomor HP → Simpan. Verifikasi OTP akan dikirim ke kontak baru.',
                            ],
                            [
                                'q' => 'Bisa hapus akun?',
                                'a' => 'Hubungi admin via WA/Email dengan permintaan hapus akun. Data transaksi (riwayat, nota, garansi) akan diarsipkan sesuai regulasi (min 5 tahun).',
                            ],
                            [
                                'q' => 'Notifikasi WA/Email tidak masuk.',
                                'a' => '• Cek folder Spam/Promotions (email).<br>
                                      • Pastikan nomor WA aktif & tidak block nomor kami.<br>
                                      • Cek pengaturan notifikasi di aplikasi WA.<br>
                                      • Data kontak di Profil sudah benar?',
                            ],
                        ],
                    ];
                @endphp

                @foreach($faqs as $category => $items)
                    @foreach($items as $index => $faq)
                        <div class="accordion-item faq-item" data-category="{{ $category }}" data-aos="fade-up" data-aos-delay="{{ ($loop->index * 30) }}">
                            <h2 class="accordion-header" id="heading{{ ucfirst($category) }}{{ $index + 1 }}">
                                <button class="accordion-button {{ $loop->first && $category === 'pemesanan' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($category) }}{{ $index + 1 }}" aria-expanded="{{ $loop->first && $category === 'pemesanan' ? 'true' : 'false' }}" aria-controls="collapse{{ ucfirst($category) }}{{ $index + 1 }}">
                                    {{ $faq['q'] }}
                                </button>
                            </h2>
                            <div id="collapse{{ ucfirst($category) }}{{ $index + 1 }}" class="accordion-collapse collapse {{ $loop->first && $category === 'pemesanan' ? 'show' : '' }}" aria-labelledby="heading{{ ucfirst($category) }}{{ $index + 1 }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {!! $faq['a'] !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- No Results Message --}}
            <div id="faqNoResults" class="text-center py-5 d-none" data-aos="fade-up">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Hasil</h5>
                <p class="text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
            </div>

            {{-- Contact CTA --}}
            <div class="card border-0 shadow-sm mt-5" data-aos="fade-up" data-aos-delay="300" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-5 text-white text-center">
                    <h4 class="fw-bold mb-3">Masih Butuh Bantuan?</h4>
                    <p class="mb-4 opacity-75">Tim support kami siap bantu via WhatsApp atau Email.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dataWebsite['wa_pusat'] ?? '6281234567890') }}" target="_blank" class="btn btn-light rounded-pill px-4 fw-semibold">
                            <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                        <a href="mailto:{{ $dataWebsite['email_toko'] ?? 'info@tokokita.com' }}" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                            <i class="fas fa-envelope me-2"></i>Kirim Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FAQ Search Filter
    const searchInput = document.getElementById('faqSearch');
    const categoryButtons = document.querySelectorAll('#faqCategories button');
    const faqItems = document.querySelectorAll('.faq-item');
    const noResults = document.getElementById('faqNoResults');

    let currentCategory = 'all';
    let searchTerm = '';

    function filterFAQs() {
        let visibleCount = 0;
        faqItems.forEach(item => {
            const category = item.dataset.category;
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            const text = question + ' ' + answer;

            const matchesCategory = currentCategory === 'all' || category === currentCategory;
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', function() {
        searchTerm = this.value.toLowerCase().trim();
        filterFAQs();
    });

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            categoryButtons.forEach(button => {
                button.classList.remove('active');
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            currentCategory = this.dataset.category;
            filterFAQs();
        });
    });
</script>
@endsection