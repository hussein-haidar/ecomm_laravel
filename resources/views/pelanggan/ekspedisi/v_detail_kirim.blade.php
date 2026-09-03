@extends('layouts.app')
@section('content')

<main class="product-section">
    <div class="container-detail">
        <header class="header-detail">
            <div>
                <a href="javascript:history.back()" style="color:white; text-decoration:none;">← KEMBALI</a>
            </div>
            <div>
                NO. PESANAN: {{ $order_id }} | <span style="color:#28a745">SEDANG DIKIRIM</span>
            </div>
        </header>

  @if ($ekspedisi)
        <div class="tracking-header">
            <div>
                Produk diperkirakan sampai pada <strong>{{ $ekspedisi->estimasi_waktu_tiba ? \Carbon\Carbon::parse($ekspedisi->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }} </strong>.
            </div>
            <div class="tracking-status">
                Status: <span id="current-status">{{ $timeline[$current_status]['label'] }}</span>
            </div>
        </div>
        @endif

        <!-- Timeline Horizontal -->
        <div class="timeline-container">
            <div class="timeline-progress">
                <div class="timeline-progress-fill" id="progress-fill" 
                     style="width: {{ (($current_index + 1) / count($timeline)) * 100 }}%"></div>
            </div>
            <div class="timeline-steps">
                @foreach ($timeline as $key => $item)
                    @php
                        $stepIndex = $loop->index;
                        $isActive = $key === $current_status;
                        $isCompleted = $stepIndex < $current_index;
                        $classes = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                    @endphp
                    <div class="timeline-step {{ $classes }}" data-step="{{ $key }}">
                        <div class="timeline-icon">
                            @if ($key === 'Pesanan dibuat') 📄
                            @elseif($key === 'Dibayar') 💰
                            @elseif($key === 'Dikemas') 📦
                            @elseif($key === 'Dikirim dari toko') 🚚
                            @elseif($key === 'Disortir') 🚚
                            @elseif($key === 'Dikirim dari gudang') 🚚
                            @elseif($key === 'Sampai gudang tujuan') 🏢
                            @elseif($key === 'Diantar kurir') 🚶
                            @elseif($key === 'Sampai tujuan') ✅
                           
                            @else ⭐
                            @endif
                        </div>
                        <div class="timeline-label">{{ $item['label'] }}</div>
                        <div class="timeline-time">{{ $item['time'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Log Timeline -->
        <div class="shipping-details">
            <div class="log-timeline">
                <h5>Log Pengiriman</h5>
                @foreach ($timeline as $item)
                    <div class="log-item">
                        <div class="log-time">{{ $item['time'] }}</div>
                        <div class="log-desc">
                            <strong>{{ $item['label'] }}</strong><br>
                            {{ $item['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Produk -->
        @if ($pembelian)
            <div class="order-product-item">
                <img src="{{ asset('fotoproduk/' . $pembelian->foto_produk) }}" 
                     alt="Produk" class="product-image">
                <div class="product-info">
                    <div class="product-name">{{ $pembelian->nama_produk ?? 'Nama Produk' }}</div>
                    <div class="product-price">Rp.{{ number_format($pembelian->harga_produk * $pembelian->jumlah_produk, 0, ',', '.') }}</div>
                </div>
                <div>x{{ $pembelian->jumlah_produk }} {{ $pembelian->satuan_produk }}</div>
            </div>
        @else
            <div class="order-product-item">
                <p>Tidak ada produk.</p>
            </div>
        @endif

        <div class="total-section">
            <span>Subtotal Produk</span>
            <span>Rp. {{ isset($pembelian) ? number_format($pembelian->harga_produk * $pembelian->jumlah_produk, 0, ',', '.') : '0' }}</span>
        </div>

    </div>
</main>

@endsection