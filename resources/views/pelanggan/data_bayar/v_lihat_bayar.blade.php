@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Detail Bukti Pembayaran</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4">
                        {{-- Image --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm overflow-hidden">
                                <div class="card-header bg-white border-0">
                                    <h5 class="fw-bold mb-0">Bukti Pembayaran</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="image-viewer-container rounded-0 overflow-hidden" style="height: 500px; cursor: grab;">
                                        <img src="{{ asset('fotobayar/' . $pembayaran->foto_bayar) }}" alt="Bukti Pembayaran"
                                            class="image-draggable img-fluid w-100" style="max-width: none; height: auto; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0">
                                    <h5 class="fw-bold mb-0">Data Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                            <i class="fas fa-user text-primary me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="text-muted small">Nama Pelanggan</div>
                                                <div class="fw-bold fs-5">{{ $pembayaran->nama_pelanggan }}</div>
                                            </div>
                                        </li>

                                        <li class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                            <i class="fas fa-university text-info me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="text-muted small">Bank</div>
                                                <div class="fw-bold">{{ $pembayaran->bank_tujuan }}</div>
                                            </div>
                                        </li>

                                        @php
                                            $bulan = [
                                                1 => 'Januari', 'Februari', 'Maret', 'April',
                                                'Mei', 'Juni', 'Juli', 'Agustus',
                                                'September', 'Oktober', 'November', 'Desember'
                                            ];
                                            $tanggal = strtotime($pembayaran->waktu_pembayaran);
                                            $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y H:i:s', $tanggal);
                                        @endphp

                                        <li class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                            <i class="fas fa-clock text-warning me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="text-muted small">Waktu Bayar</div>
                                                <div class="fw-bold">{{ $formattedTanggal }}</div>
                                            </div>
                                        </li>

                                        <li class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                            <i class="fas fa-money-bill-wave text-success me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="text-muted small">Total Bayar</div>
                                                <div class="fw-bold fs-5 text-success">Rp{{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</div>
                                            </div>
                                        </li>

                                        <li class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                            <i class="fas fa-shield-alt text-danger me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div class="text-muted small">Asuransi Return</div>
                                                <div class="fw-bold">Opsional</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.image-viewer-container');
        const img = container.querySelector('.image-draggable');

        let isDragging = false;
        let startX, startY;
        let translateX = 0, translateY = 0;

        img.addEventListener('mousedown', function(e) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            img.style.cursor = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            img.style.transform = `translate(${translateX}px, ${translateY}px)`;
        });

        document.addEventListener('mouseup', function() {
            isDragging = false;
            img.style.cursor = 'grab';
        });

        container.addEventListener('dblclick', function() {
            translateX = 0;
            translateY = 0;
            img.style.transform = 'translate(0, 0)';
        });

        img.addEventListener('dblclick', function(e) {
            if (img.classList.contains('zoomed')) {
                img.classList.remove('zoomed');
                translateX = 0;
                translateY = 0;
                img.style.transform = 'translate(0, 0)';
            } else {
                img.classList.add('zoomed');
                img.style.transform = 'scale(1.5) translate(0, 0)';
            }
        });
    });
</script>
@endsection