@extends('layouts.app')
@section('content')

<div class="promo-section">
    <div class="container-promo">
        <div class="promo-carousel" id="promoCarousel">
            @php $index = 0; @endphp
            @foreach ($data_carousel as $promo)
                @foreach ($promo->all_images as $gambar)
                    <img 
                        src="{{ asset($gambar) }}" 
                        class="carousel-img {{ $index === 0 ? 'active' : '' }}" 
                        alt="{{ $promo->nama_promo }}"
                        onclick="handleImageClick(event)"
                        data-promo-id="{{ $promo->id_promo }}"
                    >
                    @php $index++; @endphp
                @endforeach
            @endforeach
            <!-- Indikator Titik (navigasi prev/next) -->
            <div class="carousel-dots" id="carouselDots">
                @php $dotIndex = 0; @endphp
                @foreach ($data_carousel as $promo)
                    @foreach ($promo->all_images as $gambar)
                        <span 
                            class="dot {{ $dotIndex === 0 ? 'active' : '' }}" 
                            data-slide-to="{{ $dotIndex }}"
                        ></span>
                        @php $dotIndex++; @endphp
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

<!-- Product Section -->
  <main class="product-section">
    <div class="container-product">
        <h3 class="product-title">Katalog Produk</h3>
        <div class="product-grid">
            @include('layouts.v_produk')
        </div>
    </div>
  </main>

  </main>

@endsection