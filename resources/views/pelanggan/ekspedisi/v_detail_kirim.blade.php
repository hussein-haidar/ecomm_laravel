@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Lacak Pengiriman</h4>
                    <a href="{{ route('pelanggan_data.statusKirim') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if($ekspedisi)
                        {{-- Header Info --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-3 h-100">
                                    <div class="fw-medium text-muted mb-1">Estimasi Tiba</div>
                                    <div class="fw-bold fs-5 text-success">{{ $ekspedisi->estimasi_waktu_tiba ? \Carbon\Carbon::parse($ekspedisi->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-3 h-100">
                                    <div class="fw-medium text-muted mb-1">Status Saat Ini</div>
                                    <div class="fw-bold fs-5">
                                        @php
                                            $statusClass = match($current_status) {
                                                'Pesanan dibuat' => 'bg-warning text-dark',
                                                'Dibayar' => 'bg-warning text-dark',
                                                'Dikemas' => 'bg-info',
                                                'Dikirim dari toko', 'Disortir', 'Dikirim dari gudang', 'Sampai gudang tujuan', 'Diantar kurir' => 'bg-primary',
                                                'Sampai tujuan', 'Pesanan diterima' => 'bg-success',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                                            {{ $timeline[$current_status]['label'] ?? $current_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Timeline Progress --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="timeline-container">
                                    <div class="progress mb-4" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar bg-primary" id="progress-fill" style="width: {{ (($current_index + 1) / count($timeline)) * 100 }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        @foreach($timeline as $key => $item)
                                            @php
                                                $stepIndex = $loop->index;
                                                $isActive = $key === $current_status;
                                                $isCompleted = $stepIndex < $current_index;
                                                $classes = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                                            @endphp
                                            <div class="timeline-step {{ $classes }}" data-step="{{ $key }}">
                                                <div class="timeline-icon-wrapper d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; border-radius: 50%; background: {{ $isCompleted ? '#28a745' : ($isActive ? '#0d6efd' : '#dee2e6') }}; color: {{ $isCompleted || $isActive ? '#fff' : '#6c757d' }};">
                                                    @if($key === 'Pesanan dibuat') <i class="fas fa-file-alt"></i>
                                                    @elseif($key === 'Dibayar') <i class="fas fa-credit-card"></i>
                                                    @elseif($key === 'Dikemas') <i class="fas fa-box"></i>
                                                    @elseif($key === 'Dikirim dari toko') <i class="fas fa-truck"></i>
                                                    @elseif($key === 'Disortir') <i class="fas fa-sitemap"></i>
                                                    @elseif($key === 'Dikirim dari gudang') <i class="fas fa-truck-loading"></i>
                                                    @elseif($key === 'Sampai gudang tujuan') <i class="fas fa-warehouse"></i>
                                                    @elseif($key === 'Diantar kurir') <i class="fas fa-walking"></i>
                                                    @elseif($key === 'Sampai tujuan') <i class="fas fa-check-circle"></i>
                                                    @else <i class="fas fa-star"></i>
                                                    @endif
                                                </div>
                                                <div class="timeline-label small fw-{{ $isActive || $isCompleted ? 'bold' : 'normal' }} text-{{ $isCompleted || $isActive ? 'primary' : 'muted' }}">{{ $item['label'] }}</div>
                                                <div class="timeline-time small text-muted">{{ $item['time'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Log Timeline --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-0">
                                <h5 class="fw-bold mb-0">Log Pengiriman</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="timeline-log">
                                    @foreach($timeline as $item)
                                        <div class="d-flex gap-3 px-4 py-3 border-bottom last:border-bottom-0">
                                            <div class="flex-shrink-0 text-center" style="width: 60px;">
                                                <div class="timeline-icon d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: #e9ecef; color: #6c757d; font-size: 14px;">
                                                    @if($item['label'] === 'Pesanan dibuat') <i class="fas fa-file-alt"></i>
                                                    @elseif($item['label'] === 'Dibayar') <i class="fas fa-credit-card"></i>
                                                    @elseif($item['label'] === 'Dikemas') <i class="fas fa-box"></i>
                                                    @elseif($item['label'] === 'Dikirim dari toko') <i class="fas fa-truck"></i>
                                                    @elseif($item['label'] === 'Disortir') <i class="fas fa-sitemap"></i>
                                                    @elseif($item['label'] === 'Dikirim dari gudang') <i class="fas fa-truck-loading"></i>
                                                    @elseif($item['label'] === 'Sampai gudang tujuan') <i class="fas fa-warehouse"></i>
                                                    @elseif($item['label'] === 'Diantar kurir') <i class="fas fa-walking"></i>
                                                    @elseif($item['label'] === 'Sampai tujuan') <i class="fas fa-check-circle"></i>
                                                    @else <i class="fas fa-star"></i>
                                                    @endif
                                                </div>
                                                <div class="timeline-time small text-muted mt-1">{{ $item['time'] }}</div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $item['label'] }}</strong>
                                                    <span class="text-muted small">{{ $item['time'] }}</span>
                                                </div>
                                                <div class="text-muted small">{{ $item['desc'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Product Info --}}
                        @if($pembelian)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="fw-bold mb-0">Produk</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('fotoproduk/' . ($pembelian->foto_produk ?? 'default.jpg')) }}" alt="{{ $pembelian->nama_produk }}" class="rounded border" width="80" height="80" style="object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $pembelian->nama_produk ?? 'Nama Produk' }}</div>
                                            <div class="text-muted small">{{ $pembelian->jumlah_produk }} {{ $pembelian->satuan_produk }}</div>
                                            <div class="fw-bold text-success">Rp{{ number_format($pembelian->harga_produk * $pembelian->jumlah_produk, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Order Summary --}}
                        <div class="card border-0 shadow-sm bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium">Subtotal Produk</span>
                                    <span class="fw-bold">Rp{{ isset($pembelian) ? number_format($pembelian->harga_produk * $pembelian->jumlah_produk, 0, ',', '.') : '0' }}</span>
                                </div>
                                @if($ekspedisi->ongkir)
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fw-medium">Ongkir</span>
                                        <span class="fw-bold">Rp{{ number_format((int) preg_replace('/[^0-9]/', '', $ekspedisi->ongkir), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Data Pengiriman Tidak Ditemukan</h5>
                            <a href="{{ route('pelanggan_data.statusKirim') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection