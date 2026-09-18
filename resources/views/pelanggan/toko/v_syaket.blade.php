@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
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
                    <i class="fas fa-file-contract"></i>
                </div>
                <h1 class="fw-bold mb-2">Syarat & Ketentuan</h1>
                <p class="text-muted">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            {{-- Intro --}}
            <div class="card border-0 shadow-sm mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body p-4 p-lg-5">
                    <p class="lead text-muted mb-0">
                        Selamat datang di <strong>{{ $dataWebsite['nama_toko'] }}</strong>. 
                        Dengan mengakses atau menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui semua Syarat dan Ketentuan berikut.
                        Mohon baca dengan seksama sebelum melakukan transaksi.
                    </p>
                </div>
            </div>

            {{-- Terms Content --}}
            <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 p-lg-5">
                    <div class="terms-content">
                        <section id="term-1" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">1</span>
                                <h3 class="fw-bold mb-0">Informasi Produk</h3>
                            </div>
                            <p>Kami berusaha untuk menampilkan informasi produk (deskripsi, harga, gambar, spesifikasi) seakurat mungkin. Namun, kesalahan pengetikan, ketidaksesuaian warna akibat tampilan layar, atau perubahan spesifikasi tanpa pemberitahuan sebelumnya dapat terjadi.</p>
                            <ul class="ms-4">
                                <li>Gambar produk bersifat ilustrasi, warna & detail bisa berbeda nyata.</li>
                                <li>Harga & ketersediaan stok dapat berubah sewaktu-waktu.</li>
                                <li>Spesifikasi teknis mengacu pada data pabrikan/resmi.</li>
                            </ul>
                        </section>

                        <section id="term-2" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">2</span>
                                <h3 class="fw-bold mb-0">Pemesanan</h3>
                            </div>
                            <ul class="ms-4">
                                <li>Pemesanan dilakukan melalui sistem keranjang belanja di website.</li>
                                <li>Pembeli wajib mengisi data pemesanan (nama, alamat, nomor telepon) dengan benar, lengkap, dan dapat dihubungi.</li>
                                <li>Data pemesanan yang tidak valid/menyesatkan dapat menyebabkan pembatalan pesanan oleh sistem.</li>
                                <li>Kami berhak membatalkan pesanan apabila terjadi pelanggaran, penyalahgunaan sistem, atau kecurangan.</li>
                                <li>Pesanan otomatis dibatalkan jika pembayaran tidak dikonfirmasi dalam batas waktu (10 menit untuk transfer manual, sesuai timer Midtrans untuk payment gateway).</li>
                            </ul>
                        </section>

                        <section id="term-3" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">3</span>
                                <h3 class="fw-bold mb-0">Pembayaran</h3>
                            </div>
                            <p>Pembayaran dapat dilakukan melalui metode yang tersedia di halaman checkout:</p>
                            <ul class="ms-4">
                                <li><strong>Transfer Bank / Virtual Account / E-Wallet:</strong> Via Midtrans Payment Gateway (BCA, BRI, BNI, Mandiri, Permata, dll + GoPay, ShopeePay, Dana, OVO, LinkAja).</li>
                                <li><strong>COD (Bayar di Tempat):</strong> Tersedia untuk area tertentu dengan batasan nilai transaksi.</li>
                                <li>Bukti pembayaran (transfer manual) wajib diunggah ke halaman <em>Status Bayar</em> untuk verifikasi admin.</li>
                                <li>Pembayaran harus dilakukan dalam mata uang Rupiah (IDR).</li>
                                <li>Biaya administrasi/transfer (jika ada) ditanggung pembeli.</li>
                            </ul>
                        </section>

                        <section id="term-4" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">4</span>
                                <h3 class="fw-bold mb-0">Pengiriman</h3>
                            </div>
                            <ul class="ms-4">
                                <li>Pengiriman dilakukan setelah pembayaran <strong>terverifikasi & dikonfirmasi</strong> oleh admin/toko.</li>
                                <li>Waktu pengiriman tergantung lokasi tujuan, jasa ekspedisi (JNE, J&T, SiCepat, Ninja, Lokal), dan kondisi cuaca/keamanan.</li>
                                <li>Estimasi waktu (ETD) yang ditampilkan hanyalah perkiraan, bukan jaminan pasti.</li>
                                <li>Nomor resi & link tracking akan dikirimkan ke WhatsApp/email & tersedia di menu <em>Pengiriman</em>.</li>
                                <li>Jika paket rusak/hilang di jalan, klaim diajukan ke ekspedisi terkait (kami bantu prosesnya).</li>
                                <li>Alamat pengiriman tidak bisa diubah setelah status <strong>Dikirim</strong>.</li>
                            </ul>
                        </section>

                        <section id="term-5" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">5</span>
                                <h3 class="fw-bold mb-0">Retur, Pengembalian & Penggantian</h3>
                            </div>
                            <ul class="ms-4">
                                <li><strong>Retur Diterima Jika:</strong> Produk rusak/pecah/cacat pabrik, tidak sesuai pesanan (warna/ukuran/model salah dikirim), atau kedaluwarsa (untuk produk konsumsi).</li>
                                <li><strong>Batas Waktu:</strong> Maksimal <strong>2 x 24 jam</strong> setelah produk diterima (berdasarkan bukti terima ekspedisi).</li>
                                <li><strong>Syarat Produk:</strong> Dalam kondisi asli, tidak digunakan, tag/label/packaging utuh, lengkap aksesorisnya.</li>
                                <li><strong>Proses:</strong> Foto/video bukti kerusakan/ketidaksesuaian → Ajukan via menu <em>Pengiriman</em> / Chat Penjual → Verifikasi → Instruksi pengembalian.</li>
                                <li><strong>Biaya Retur:</strong> Dibatalkan oleh toko jika kesalahan dari toko/ekspedisi; ditanggung pembeli jika alasan pribadi (salah pilih, tidak suka, dll).</li>
                                <li><strong>Penggantian:</strong> Prioritas pengiriman ulang barang yang sama; jika stok habis → pengembalian dana full.</li>
                                <li>Produk <strong>tidak dapat diretur</strong>: Produk custom/personalisasi, produk higiene (celana dalam, masker, dll), produk digital/voucher, produk promo/flash sale (kecuali rusak/keliru).</li>
                            </ul>
                        </section>

                        <section id="term-6" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">6</span>
                                <h3 class="fw-bold mb-0">Garansi Produk</h3>
                            </div>
                            <ul class="ms-4">
                                <li>Garansi resmi mengikuti kebijakan pabrikan/distributor resmi masing-masing brand.</li>
                                <li>Klaim garansi memerlukan nota/resi pembelian asli & barang dalam kondisi sesuai ketentuan garansi.</li>
                                <li>Garansi tidak berlaku untuk kerusakan akibat salah penggunaan, jatuh, cairan, modifikasi, bencana alam, atau normal wear & tear.</li>
                            </ul>
                        </section>

                        <section id="term-7" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">7</span>
                                <h3 class="fw-bold mb-0">Privasi & Data Pribadi</h3>
                            </div>
                            <p>Data pribadi Anda (nama, alamat, telepon, email, koordinat lokasi) hanya digunakan untuk:</p>
                            <ul class="ms-4">
                                <li>Proses pemesanan, pembayaran, pengiriman, & komunikasi terkait transaksi.</li>
                                <li>Peningkatan layanan, analitik, & keamanan (fraud prevention).</li>
                                <li>Kepatuhan hukum & peraturan perundang-undangan.</li>
                            </ul>
                            <p>Kami tidak menjual data pribadi ke pihak ketiga. Detail lengkap lihat <a href="#" class="text-primary">Kebijakan Privasi</a>.</p>
                        </section>

                        <section id="term-8" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">8</span>
                                <h3 class="fw-bold mb-0">Hak Kekayaan Intelektual</h3>
                            </div>
                            <p>Semua konten di website (logo, nama brand, desain UI, foto produk, deskripsi, kode program) adalah hak milik <strong>{{ $dataWebsite['nama_toko'] }}</strong> atau mitra/reseller yang berhak. Dilarang menyalin, mendistribusikan, atau memodifikasi tanpa izin tertulis.</p>
                        </section>

                        <section id="term-9" class="mb-5 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">9</span>
                                <h3 class="fw-bold mb-0">Batas Tanggung Jawab</h3>
                            </div>
                            <p>Kami tidak bertanggung jawab atas:</p>
                            <ul class="ms-4">
                                <li>Keterlambatan/pengiriman gagal akibat force majeure, kebijakan pemerintah, gangguan jaringan ekspedisi.</li>
                                <li>Kerusakan/kerugian akibat penggunaan produk tidak sesuai manual/bukit petunjuk.</li>
                                <li>Ketersediaan/performa website 100% uptime (meski kami berusaha maksimal).</li>
                            </ul>
                        </section>

                        <section id="term-10" class="mb-0">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">10</span>
                                <h3 class="fw-bold mb-0">Perubahan Syarat & Ketentuan</h3>
                            </div>
                            <p>Kami berhak mengubah Syarat & Ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya. Versi terbaru akan selalu ditampilkan di halaman ini dengan tanggal pembaruan. Penggunaan berkelanjutan setelah perubahan berarti Anda menyetujui revisi tersebut.</p>
                        </section>
                    </div>
                </div>
            </div>

            {{-- Contact CTA --}}
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-5 text-white">
                        <h4 class="fw-bold mb-3">Masih Punya Pertanyaan?</h4>
                        <p class="mb-4 opacity-75">Tim layanan pelanggan kami siap membantu Anda kapan saja.</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dataWebsite['wa_pusat'] ?? '6281234567890') }}" target="_blank" class="btn btn-light rounded-pill px-4 fw-semibold">
                                <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                            </a>
                            <a href="{{ route('home_toko.bantuan') }}" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                                <i class="fas fa-question-circle me-2"></i>Bantuan / FAQ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection