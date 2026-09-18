<footer class="footer-modern pt-5 mt-5" role="contentinfo">
    <div class="container">
        <div class="row g-4 pb-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-widget">
                    <div class="footer-brand mb-3">
                        @if($dataWebsite['logo_website'])
                            <img src="{{ asset('logo_website/' . $dataWebsite['logo_website']) }}" alt="Logo {{ $dataWebsite['nama_toko'] }}" width="44" height="44" class="rounded-circle me-2" style="object-fit: cover;">
                        @else
                            <i class="fas fa-store text-primary me-2" style="font-size: 1.6rem;"></i>
                        @endif
                        <span class="fw-bold fs-5">{{ $dataWebsite['nama_toko'] }}</span>
                    </div>
                    <p class="text-muted small mb-3">{!! Str::limit($dataWebsite['footer_title'] ?? $dataWebsite['deskripsi_toko'] ?? '', 220) !!}</p>

                    <!-- Social Links -->
                    <div class="social-links d-flex gap-3">
                        @if($dataWebsite['link_IG'] !== '#' && $dataWebsite['link_IG'])
                            <a href="{{ $dataWebsite['link_IG'] }}" target="_blank" class="social-btn instagram" title="Instagram" aria-label="Instagram" rel="noopener">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($dataWebsite['link_FB'] !== '#' && $dataWebsite['link_FB'])
                            <a href="{{ $dataWebsite['link_FB'] }}" target="_blank" class="social-btn facebook" title="Facebook" aria-label="Facebook" rel="noopener">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($dataWebsite['link_Tiktok'] !== '#' && $dataWebsite['link_Tiktok'])
                            <a href="{{ $dataWebsite['link_Tiktok'] }}" target="_blank" class="social-btn tiktok" title="TikTok" aria-label="TikTok" rel="noopener">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        @endif
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dataWebsite['wa_pusat']) }}" target="_blank" class="social-btn whatsapp" title="WhatsApp" aria-label="WhatsApp" rel="noopener">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-3">Menu Cepat</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="{{ url('/') }}"><i class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li><a href="{{ route('home_toko.katalog') }}"><i class="fas fa-chevron-right me-2"></i>Katalog Produk</a></li>
                        <li><a href="{{ route('pelanggan_data.flashSale') }}"><i class="fas fa-bolt me-2"></i>Flash Sale</a></li>
                        <li><a href="{{ route('home_toko.syaket') }}"><i class="fas fa-chevron-right me-2"></i>Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('home_toko.bantuan') }}"><i class="fas fa-chevron-right me-2"></i>Bantuan / FAQ</a></li>
                        @if(session('user_logged_in'))
                            <li><a href="{{ route('pelanggan_data.cart') }}"><i class="fas fa-chevron-right me-2"></i>Keranjang</a></li>
                            <li><a href="{{ route('pelanggan_data.riwayatBeli') }}"><i class="fas fa-chevron-right me-2"></i>Riwayat Beli</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-3">Layanan Pelanggan</h5>
                    <ul class="footer-links list-unstyled">
                        @if(session('user_logged_in'))
                            <li><a href="{{ route('pelanggan_data.profil') }}"><i class="fas fa-chevron-right me-2"></i>Profil Saya</a></li>
                        @endif
                        <li><a href="{{ route('pelanggan_data.statusBayar') }}"><i class="fas fa-chevron-right me-2"></i>Status Pembayaran</a></li>
                        <li><a href="{{ route('pelanggan_data.statusKirim') }}"><i class="fas fa-chevron-right me-2"></i>Pelacakan Pengiriman</a></li>
                        <li><a href="{{ route('pelanggan_data.wishlist') }}"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                        <li><a href="#" onclick="window.bukaChat && bukaChat()"><i class="fas fa-comment-dots me-2"></i>Chat Penjual</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-widget">
                    <h5 class="footer-title mb-3">Kontak Kami</h5>
                    <ul class="footer-contact list-unstyled">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <span class="text-muted small">{{ $dataWebsite['alamat_pusat'] }}</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-phone-alt text-primary mt-1"></i>
                            <span class="text-muted small">
                                <a href="tel:{{ $dataWebsite['wa_pusat'] }}" class="text-muted text-decoration-none">
                                    {{ $dataWebsite['wa_pusat'] }}
                                </a>
                            </span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-envelope text-primary mt-1"></i>
                            <span class="text-muted small">
                                <a href="mailto:{{ $dataWebsite['email_toko'] }}" class="text-muted text-decoration-none">
                                    {{ $dataWebsite['email_toko'] }}
                                </a>
                            </span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-clock text-primary mt-1"></i>
                            <span class="text-muted small">Senin - Minggu: 08.00 - 22.00 WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Trust / Payment strip -->
        <div class="footer-bottom py-4 border-top">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <p class="mb-0 text-muted small">
                        &copy; {{ date('Y') }} <strong>{{ $dataWebsite['nama_toko'] }}</strong>. Hak Cipta Dilindungi.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                        <span class="text-muted small me-1">Pembayaran:</span>
                        <span class="payment-badge"><i class="fab fa-cc-visa text-primary"></i> Visa</span>
                        <span class="payment-badge"><i class="fab fa-cc-mastercard text-danger"></i> Mastercard</span>
                        <span class="payment-badge"><i class="fab fa-cc-paypal text-info"></i> PayPal</span>
                        <span class="payment-badge"><i class="fas fa-mobile-alt text-success"></i> E-Wallet</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg back-to-top" 
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
        aria-label="Kembali ke atas" 
        style="display: none; width: 50px; height: 50px; z-index: 1000;">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    // Back to top button visibility
    window.addEventListener('scroll', function() {
        const btn = document.querySelector('.back-to-top');
        if (window.scrollY > 300) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    }, { passive: true });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>