@extends('layouts.app')

@section('content')

 <!-- Product Section -->
  <main class="product-section">
    <div class="container-product">
        <h3 class="product-title">Katalog Produk</h3>
        <div class="product-grid">
            @include('layouts.v_produk')
        </div>
    </div>
  </main>
  
    @endsection