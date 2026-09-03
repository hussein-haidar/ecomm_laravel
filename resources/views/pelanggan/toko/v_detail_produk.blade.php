@extends('layouts.app')
@section('content')

    <main class="product-section">
        <div class="container my-5">
            <div class="row">
                <!-- Gambar Produk -->
                <div class="col-md-6 mb-4 mb-md-0">
                    @php
                        $gallery = [];
                        if (!empty($produk['gallery_images'])) {
                            $gallery = json_decode($produk['gallery_images'], true) ?? [];
                        }
                        $allImages = array_merge([$produk['foto_produk']], $gallery);
                    @endphp
                    
                    <div class="product-image-container rounded overflow-hidden" style="height: 500px; position: relative;">
                        <img id="mainProductImage" src="{{ asset('fotoproduk/' . $produk['foto_produk']) }}" alt="Gambar Produk"
                            class="img-fluid w-100 h-100 object-fit-contain">
                        
                        @if (count($allImages) > 1)
                        <div class="product-thumbnails d-flex gap-2 justify-content-center mt-2 flex-wrap">
                            @foreach ($allImages as $index => $img)
                                <img src="{{ asset('fotoproduk/' . $img) }}" alt="Thumbnail {{ $index + 1 }}"
                                    class="product-thumb {{ $index === 0 ? 'active' : '' }}"
                                    data-src="{{ asset('fotoproduk/' . $img) }}"
                                    style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid transparent; border-radius: 8px;">
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if ($produk['video_url'])
                    <div class="mt-3">
                        <button class="btn btn-outline-primary w-100" onclick="playVideo('{{ $produk['video_url'] }}')">
                            <i class="fas fa-play-circle me-2"></i>Putar Video Produk
                        </button>
                    </div>
                    @endif
                    
                    @if ($produk['size_guide_image'])
                    <div class="mt-3">
                        <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                            <i class="fas fa-ruler-combined me-2"></i>Lihat Panduan Ukuran
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Detail Produk -->
                <div class="col-md-6">
                    <h3><strong>{{ $produk['nama_produk'] }}</strong></h3>
                    <p class="text-muted">Kategori: {{ $produk['jenis_produk'] }}</p>

                    <!-- Harga dan Diskon -->
                    <div class="d-flex align-items-center mb-2">
                        <span
                            class="text-danger h4 me-2">Rp.{{ number_format($produk['harga_produk'], 0, ',', '.') }}</span>
                        <span class="badge bg-danger text-white">20 % Off</span>
                    </div>

                    <!-- Informasi Tambahan -->
                    <ul class="list-unstyled mb-3">
                        <li><i class="fas fa-box-open text-success"></i> Stok: {{ $produk['jumlah_stok_produk'] }}
                            {{ $produk['satuan_produk'] }}</>
                        </li>
                        <li><i class="fas fa-check-circle text-success"></i> Dikirim dari: {{ $produk['nama_toko'] }}</>
                        </li>
                        <li><i class="fas fa-box-open"></i> Dropship:
                            Rp.{{ number_format($produk['harga_produk'], 0, ',', '.') }}
                        </li>
                        <li><i class="fas fa-weight-hanging"></i> Berat: {{ $produk['berat_produk'] }}
                            {{ $produk['satuan_berat'] }}</>
                        </li>
                        <li><i class="fas fa-shield-alt"></i> Asuransi Pengiriman: Opsional</>
                        </li>
                    </ul>

                    @if ($user_logged_in)
                        <!-- Form Keranjang -->
                        <form action="{{ route('pelanggan_data.add_to_cart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_stok" value="{{ $produk['id_stok'] }}">
                            <input type="hidden" name="nama_produk" value="{{ $produk['nama_produk'] }}">
                            <input type="hidden" name="satuan_produk" value="{{ $produk['satuan_produk'] }}">
                            <input type="hidden" name="harga_produk" value="{{ $produk['harga_produk'] }}">
                            <input type="hidden" name="berat_produk" value="{{ $produk['berat_produk'] }}">
                            <input type="hidden" name="satuan_berat" value="{{ $produk['satuan_berat'] }}">
                            <input type="hidden" name="nama_toko" value="{{ $produk['nama_toko'] }}">

                            <!-- Form Detail Produk -->
                            <div class="form-detail-cart-wrapper d-flex flex-wrap align-items-center gap-2 mt-3">

                                <!-- Pilihan Ukuran -->
                                <div class="d-flex align-items-center flex-wrap gap-2 w-100">
                                    <label for="ukuran_produk" class="form-label m-0">Ukuran:</label>
                                    <select id="ukuran_produk" name="ukuran_produk" class=""
                                        required>
                                        <option value="">Pilih Ukuran</option>
                                        @foreach ($produk['ukuran_list'] as $ukuran)
                                            <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah Produk -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="jumlah_produk" class="form-label m-0">Qty:</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="updateQty(-1)">-</button>
                                    <input type="number" id="jumlah_produk" name="jumlah_produk" min="1"
                                        value="1" max="{{ $produk['total_stok'] }}"
                                        class="form-control form-control-sm text-center" style="width: 60px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="updateQty(1)">+</button>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 mt-3">
                                    <button type="submit" class="btn btn-cart">
                                        <i class="fas fa-shopping-cart"></i>&nbsp;Keranjang
                                    </button>
                                    <button type="submit" class="btn btn-buy"
                                        formaction="{{ route('pelanggan_data.beliLangsung', ['id_stok' => $produk['id_stok']]) }}">
                                        <i class="fa fa-fw fa-check"></i>&nbsp;Beli Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    @if (!$user_logged_in)
                        <form action="{{ route('pelanggan_data.add_to_cart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_stok" value="{{ $produk['id_stok'] }}">
                            <input type="hidden" name="nama_produk" value="{{ $produk['nama_produk'] }}">
                            <input type="hidden" name="satuan_produk" value="{{ $produk['satuan_produk'] }}">
                            <input type="hidden" name="harga_produk" value="{{ $produk['harga_produk'] }}">
                            <input type="hidden" name="berat_produk" value="{{ $produk['berat_produk'] }}">
                            <input type="hidden" name="satuan_berat" value="{{ $produk['satuan_berat'] }}">
                            <input type="hidden" name="sesi_user" value="{{ $produk['sesi_user'] }}">
                            <input type="hidden" name="nama_toko" value="{{ $produk['nama_toko'] }}">

                            <!-- Form Detail Produk -->
                            <div class="form-detail-cart-wrapper d-flex flex-wrap align-items-center gap-2 mt-3">

                                <!-- Pilihan Ukuran -->
                                <div class="d-flex align-items-center flex-wrap gap-2 w-100">
                                    <label for="ukuran_produk" class="form-label m-0">Ukuran:</label>
                                    <select id="ukuran_produk" name="ukuran_produk" class=""
                                        required>
                                        <option value="">Pilih Ukuran</option>
                                        @foreach ($produk['ukuran_list'] as $ukuran)
                                            <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah Produk -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="jumlah_produk" class="form-label m-0">Qty:</label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="updateQty(-1)">-</button>
                                    <input type="number" id="jumlah_produk" name="jumlah_produk" min="1"
                                        value="1" max="{{ $produk['total_stok'] }}"
                                        class="form-control form-control-sm text-center" style="width: 60px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="updateQty(1)">+</button>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 mt-3">
                                    
                                    <button type="submit" class="btn btn-cart" formaction="{{ url('pelanggan_data/add_to_cart') }}">
                                        <i class="fas fa-shopping-cart"></i>&nbsp;Keranjang
                                    </button>
                                    <button type="submit" class="btn btn-buy" formaction="{{ route('pelanggan_data.beliLangsung', ['id_stok' => $produk['id_stok']]) }}">
                                        <i class="fa fa-fw fa-check"></i>&nbsp;Beli Sekarang
                                    </button>
                                   
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Size Guide Modal -->
            @if ($produk['size_guide_image'])
            <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sizeGuideModalLabel"><i class="fas fa-ruler-combined me-2"></i>Panduan Ukuran - {{ $produk['nama_produk'] }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="{{ asset('fotoproduk/' . $produk['size_guide_image']) }}" alt="Size Guide" class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Video Modal -->
            <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="videoModalLabel"><i class="fas fa-play-circle me-2"></i>Video Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0" id="videoModalBody">
                            <!-- Video will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Share Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5><strong>Bagikan Produk</strong></h5>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-facebook-f"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($produk['nama_produk']) }}" target="_blank" class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i> Twitter</a>
                        <a href="https://wa.me/?text={{ urlencode($produk['nama_produk'] . ' - ' . url()->current()) }}" target="_blank" class="btn btn-outline-success btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText('{{ url()->current() }}');Swal.fire({icon:'success',title:'Link disalin!',timer:1500,showConfirmButton:false})"><i class="fas fa-link"></i> Salin Link</button>
                    </div>
                </div>
            </div>

            <!-- Informasi Toko dan Ulasan -->
            <div class="row mt-5">
                <!-- Informasi Toko -->
                <div class="col-md-6">
                    <div class="border p-3 rounded toko-info">
                        <h5><strong>Informasi Penjual</strong></h5>
                        <a href="{{ url('home_toko/view_toko/' . $produk['nama_toko']) }}"
                            class="text-decoration-none text-dark" data-sesi-user="{{ $produk['sesi_user'] }}">
                            <!-- ↑ Menyimpan data di sini -->
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('logowebsite/' . $produk['logo_website']) }}" alt="Logo Toko"
                                    class="me-2 rounded-circle" style="width: 30px; height: 30px;">
                                <strong>{{ $produk['nama_toko'] }}</strong>
                            </div>
                        </a>
                        <p><i class="fas fa-map-marker-alt"></i> Lokasi: {{ $produk['alamat_pusat'] }}</p>
                        <p>
                            @php
                                $toko_rating = $ringkasan_toko->rata ?? 0;
                                $toko_total = $ringkasan_toko->total ?? 0;
                                $toko_rekomendasi = (int) ($ringkasan_toko->rekomendasi ?? 0);
                                $persen_toko_rekomendasi = $toko_total > 0 ? round($toko_rekomendasi / $toko_total * 100) : 0;
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= round($toko_rating) ? 'fas' : 'far' }} fa-star text-warning"></i>
                            @endfor
                            ({{ number_format($toko_rating, 1, ',', '.') }} - {{ $toko_total }} Ulasan)
                        </p>
                        @if ($toko_total > 0)
                            <p class="small text-success">
                                <i class="fas fa-thumbs-up me-1"></i>{{ $persen_toko_rekomendasi }}% pembeli merekomendasikan produk dari toko ini
                            </p>
                        @endif
                        <p><i class="fas fa-shipping-fast"></i> Kecepatan pengiriman: +- 1 Hari</p>
                        @if ($user_logged_in)
                            <a href="javascript:void(0)"
                                onclick="window.bukaChat && bukaChat('{{ $produk['nama_toko'] }}', '{{ $produk['id_stok'] }}')"
                                class="btn btn-outline-primary"><i class="fas fa-comments"></i> Chat Penjual</a>
                        @else
                            <a href="{{ url('auth/login_pelanggan') }}" class="btn btn-outline-primary"><i
                                    class="fas fa-comments"></i> Chat Penjual</a>
                        @endif
                    </div>
                </div>

                <!-- Ulasan Produk -->
                <div class="col-md-6" id="review_list">
                    <div class="border p-3 rounded review-card h-100">
                        <h5 class="mb-3"><strong>Ulasan Pembeli</strong></h5>

                        @php
                            $ulasan = $ulasan ?? [];
                            $ringkasan = $ringkasan_ulasan ?? null;
                            $total_ulasan = $ringkasan->total ?? count($ulasan);
                            $rata_rating = (float) ($ringkasan->rata ?? 0);
                            $rekomendasi = (int) ($ringkasan->rekomendasi ?? 0);

                            $distribusi = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                            foreach ($ulasan as $r) {
                                if (isset($distribusi[$r['rating']])) {
                                    $distribusi[$r['rating']]++;
                                }
                            }
                            $persen_rekomendasi = $total_ulasan > 0 ? round($rekomendasi / $total_ulasan * 100) : 0;
                            $bulan_id = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                            $fmt_tanggal = function ($tanggal) use ($bulan_id) {
                                $t = \Carbon\Carbon::parse($tanggal);
                                return $t->format('d ') . $bulan_id[(int) $t->format('n')] . $t->format(' Y');
                            };
                            $initial_avatar = function ($nama) {
                                $bagian = preg_split('/\s+/', trim($nama));
                                $init = mb_strtoupper(mb_substr($bagian[0] ?? '', 0, 1));
                                if (isset($bagian[1])) {
                                    $init .= mb_strtoupper(mb_substr($bagian[1], 0, 1));
                                }
                                return $init;
                            };
                        @endphp

                        @if ($total_ulasan > 0)
                            <!-- Ringkasan Rating -->
                            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                                <div class="text-center" style="min-width: 100px;">
                                    <span class="fw-bold fs-1 lh-1 text-dark">{{ number_format($rata_rating, 1, ',', '.') }}</span>
                                    <span class="text-muted">/ 5</span>
                                    <div class="text-warning small mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($rata_rating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $total_ulasan }} ulasan pembeli</div>
                                    <div class="text-success small">
                                        <i class="fas fa-thumbs-up me-1"></i>{{ $persen_rekomendasi }}% pembeli merekomendasikan produk ini
                                    </div>
                                </div>
                            </div>

                            <!-- Distribusi Rating -->
                            <div class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="small text-muted">Filter Ulasan:</span>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-filter-rating active"
                                        data-rating="0">Semua</button>
                                    @foreach ([5, 4, 3, 2, 1] as $bintang)
                                        @if (($distribusi[$bintang] ?? 0) > 0)
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-filter-rating"
                                                data-rating="{{ $bintang }}">{{ $bintang }} <i
                                                    class="fas fa-star text-warning fa-xs"></i></button>
                                        @endif
                                    @endforeach
                                </div>
                                @foreach ([5, 4, 3, 2, 1] as $bintang)
                                    @php
                                        $jml = $distribusi[$bintang];
                                        $persen = $total_ulasan > 0 ? round($jml / $total_ulasan * 100) : 0;
                                    @endphp
                                    <button type="button"
                                        class="btn btn-filter-rating d-flex align-items-center gap-2 mb-1 w-100 text-start p-1 border-0 bg-transparent"
                                        data-rating="{{ $bintang }}">
                                        <span class="small text-nowrap" style="width: 40px;">{{ $bintang }} <i
                                                class="fas fa-star text-warning fa-xs"></i></span>
                                        <div class="progress flex-grow-1" style="height: 6px; margin-bottom: 0;">
                                            <div class="progress-bar bg-warning" style="width: {{ $persen }}%"></div>
                                        </div>
                                        <span class="small text-muted text-nowrap" style="width: 70px; text-align: right;">{{ $jml }} ({{ $persen }}%)</span>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
                        @endif

                        <!-- Daftar Ulasan -->
                        <div id="review-list">
                            @foreach ($ulasan as $review)
                                <div class="border-top pt-3 mb-3 review-item" data-rating="{{ $review['rating'] }}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                        style="width: 40px; height: 40px;">
                                        {{ $initial_avatar($review['nama_user']) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-1">
                                            <div>
                                                <span class="fw-semibold">{{ $review['nama_user'] }}</span>
                                                @if (!empty($review['terverifikasi']))
                                                    <span class="badge bg-success ms-1">
                                                        <i class="fas fa-check-circle"></i> Pembeli Terverifikasi
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $fmt_tanggal($review['tanggal_ulasan']) }}</small>
                                        </div>
                                        <div class="small text-warning my-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $review['rating'] ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                            <span class="text-muted ms-1">{{ number_format($review['rating'], 1, ',', '.') }}</span>
                                        </div>
                                        <p class="mb-0 text-body">{{ $review['komentar'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div id="review-empty-filter" class="text-center text-muted py-4" style="display:none;">
                            <i class="fas fa-star-half-alt me-1"></i>Tidak ada ulasan dengan rating ini.
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

<!-- Script untuk Qty +/- -->
<script>
    function updateQty(delta) {
        const input = document.getElementById('jumlah_produk');
        let val = parseInt(input.value) || 1; // Default ke 1 jika NaN
        val += delta;

        // Batasi sesuai min dan max
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        val = Math.max(min, Math.min(val, max));

        input.value = val;
    }

    function validateQty(inputElement) {
        let val = parseInt(inputElement.value);
        const min = parseInt(inputElement.min);
        const max = parseInt(inputElement.max);

        if (isNaN(val) || val < min) {
            inputElement.value = min;
        } else if (val > max) {
            inputElement.value = max;
        }
    }

    // Gallery thumbnail click handler
    document.addEventListener('DOMContentLoaded', function() {
        const thumbs = document.querySelectorAll('.product-thumb');
        const mainImg = document.getElementById('mainProductImage');
        
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                mainImg.src = this.dataset.src;
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });

    // Video player handler
    function playVideo(url) {
        const modal = new bootstrap.Modal(document.getElementById('videoModal'));
        const body = document.getElementById('videoModalBody');
        
        let html = '';
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            // Extract YouTube video ID
            let videoId = '';
            if (url.includes('youtube.com/watch')) {
                const urlParams = new URLSearchParams(new URL(url).search);
                videoId = urlParams.get('v');
            } else if (url.includes('youtu.be/')) {
                videoId = url.split('youtu.be/')[1].split('?')[0];
            }
            if (videoId) {
                html = `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            }
        } else if (url.includes('vimeo.com')) {
            // Extract Vimeo video ID
            const videoId = url.split('vimeo.com/')[1].split('?')[0];
            html = `<iframe width="100%" height="400" src="https://player.vimeo.com/video/${videoId}" frameborder="0" allowfullscreen></iframe>`;
        } else {
            // Direct video file (MP4, WebM, etc)
            html = `<video width="100%" height="400" controls><source src="${url}" type="video/mp4">Browser Anda tidak mendukung video.</video>`;
        }
        
        body.innerHTML = html;
        modal.show();
        
        // Clear video when modal closes
        document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
            body.innerHTML = '';
        }, { once: true });
    }
</script>

<!-- Filter Ulasan berdasarkan Rating -->
<style>
    .btn-filter-rating.active {
        background-color: #fff8e1 !important;
        border-color: #ffc107 !important;
    }

    .btn-filter-rating .progress-bar {
        transition: opacity .2s;
    }

    .btn-filter-rating:hover .progress-bar {
        opacity: .75;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tombol = document.querySelectorAll('.btn-filter-rating');
        var items = document.querySelectorAll('#review_list .review-item');
        var kosong = document.getElementById('review-empty-filter');

        function terapkanFilter(rating) {
            var tampil = 0;
            items.forEach(function(item) {
                var r = parseInt(item.getAttribute('data-rating')) || 0;
                var show = rating === 0 || r === rating;
                item.style.display = show ? '' : 'none';
                if (show) tampil++;
            });
            if (kosong) kosong.style.display = tampil === 0 ? '' : 'none';
        }

        tombol.forEach(function(b) {
            b.addEventListener('click', function() {
                var rating = parseInt(this.getAttribute('data-rating')) || 0;
                tombol.forEach(function(x) {
                    x.classList.toggle('active', x === b);
                });
                terapkanFilter(rating);
            });
        });
    });
</script>
