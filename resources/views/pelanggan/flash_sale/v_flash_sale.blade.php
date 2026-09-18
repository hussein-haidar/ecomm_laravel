@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="fw-bold mb-0"><i class="fas fa-bolt text-warning me-2"></i>Flash Sale</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if(empty($flashSales))
                        <div class="text-center py-5">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Flash Sale Aktif</h5>
                            <p class="text-muted">Saat ini tidak ada promo flash sale yang berjalan.</p>
                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                <i class="fas fa-store me-1"></i>Lihat Katalog
                            </a>
                        </div>
                    @else
                        @foreach($flashSales as $id => $fs)
                            <div class="mb-5 pb-5 border-bottom last:pb-0 last:border-bottom-0" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                                    <h5 class="fw-bold text-danger mb-0"><i class="fas fa-fire me-2"></i>{{ $fs['info']->nama_flash_sale }}</h5>
                                    <span class="badge bg-danger fs-countdown rounded-pill px-3 py-2" data-end="{{ $fs['info']->waktu_selesai }}" style="font-size: 0.85rem;"></span>
                                </div>

                                <div class="row g-4">
                                    @foreach($fs['items'] as $item)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <article class="product-card card h-100 shadow-sm border-danger overflow-hidden" style="transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                                <span class="badge bg-danger position-absolute m-3 px-3 rounded-pill" style="z-index: 10; font-size: 0.7rem;">
                                                    <i class="fas fa-bolt me-1"></i>FLASH SALE
                                                </span>
<div class="img-container position-relative bg-light" style="overflow: hidden;">

                                                    <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" aria-label="Lihat detail {{ $item->nama_produk }}">
                                                        <img src="{{ asset('fotoproduk/' . ($item->foto_produk ?? 'default.jpg')) }}" class="card-img-top img-fluid w-100" alt="{{ $item->nama_produk }}" style="transition: transform 0.4s ease;" loading="lazy">
                                                    </a>
                                                    <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-gradient" style="background: linear-gradient(transparent, rgba(0,0,0,0.8)); opacity: 0; transition: opacity 0.3s ease;">
                                                        <div class="d-flex justify-content-center">
                                                            <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" class="btn btn-light btn-sm rounded-pill px-3" title="Detail Produk">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body d-flex flex-column p-3">
                                                    <h6 class="card-title fw-bold mb-1 text-truncate" style="font-size: 0.9rem;">
                                                        <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" class="text-dark text-decoration-none">
                                                            {{ $item->nama_produk }}
                                                        </a>
                                                    </h6>

                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                                        <span class="text-danger fw-bold fs-6">Rp{{ number_format($item->harga_flash_sale, 0, ',', '.') }}</span>
                                                        <span class="text-muted text-decoration-line-through small">Rp{{ number_format($item->harga_normal, 0, ',', '.') }}</span>
                                                        <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.65rem;">{{ $item->diskon_persen ?? 0 }}%</span>
                                                    </div>

                                                    <div class="text-muted small mb-2">Stok: {{ ($item->kuota ?? 0) - ($item->terjual ?? 0) }}</div>

                                                    <div class="mt-auto">
                                                        <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" class="btn btn-danger w-100 rounded-pill py-2 fw-semibold" title="Beli Sekarang">
                                                            <i class="fas fa-bolt me-2"></i>Beli Flash Sale
                                                        </a>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.fs-countdown').forEach(el => {
        const end = new Date(el.dataset.end).getTime();
        setInterval(() => {
            const diff = end - Date.now();
            if (diff <= 0) {
                el.textContent = 'Selesai';
                return;
            }
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = h + 'j ' + m + 'm ' + s + 's';
        }, 1000);
    });
</script>
@endsection