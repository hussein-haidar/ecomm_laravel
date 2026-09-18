@extends('layouts.app')

@section('content')
<section class="py-4" aria-labelledby="katalog-title">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4" data-aos="fade-up">
            <div>
                <h2 id="katalog-title" class="fw-bold mb-1 text-dark">Katalog Produk</h2>
                <p class="text-muted mb-0">
                    @php
                        $totalProduk = is_array($produk_data) || $produk_data instanceof \Countable ? count($produk_data) : 0;
                    @endphp
                    Menampilkan <strong>{{ $totalProduk }}</strong> produk
                    @if(!empty($title2))
                        (kategori: "{{ $title2 }}")
                    @endif
                </p>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row g-4" id="productGrid">
            @if(empty($produk_data))
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Tidak Ada Produk</h4>
                    <p class="text-muted">Tidak ditemukan produk pada kategori ini.</p>
                    <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                        <i class="fas fa-arrow-left me-1"></i>Lihat Semua Produk
                    </a>
                </div>
            @else
                @foreach($produk_data as $produk)
                    @include('layouts.v_produk_item', ['produk' => $produk, 'loop' => $loop])
                @endforeach
            @endif
        </div>

        <!-- Pagination -->
        @if($produk_data instanceof \Illuminate\Pagination\LengthAwarePaginator && $produk_data->lastPage() > 1)
            <nav aria-label="Pagination" class="mt-5" data-aos="fade-up">
                <div class="d-flex justify-content-center">
                    {{ $produk_data->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </nav>
        @endif
    </div>
</section>
@endsection