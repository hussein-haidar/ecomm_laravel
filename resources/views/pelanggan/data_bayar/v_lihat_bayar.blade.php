@extends('layouts.app')
@section('content')
    <main class="payment-section">
        <div class="container my-5">
            <form action="{{ route('pelanggan_data.lihatBayar', ['id_bayar' => $pembayaran->id_bayar]) }}" method="POST">
                <div class="row">
                    <!-- Gambar Produk -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h3><strong>Bukti Pembayaran</strong></h3>
                        <div class="image-viewer-container rounded overflow-hidden" style="height: 500px; cursor: grab;">
                            <img src="{{ asset('fotobayar/' . $pembayaran->foto_bayar) }}" alt="Bukti Pembayaran"
                                class="image-draggable img-fluid w-100" style="max-width: none; height: auto;">
                        </div>
                    </div>

                    <!-- Detail Produk -->
                    <div class="col-md-6">
                        <h3><strong>Data Pembeli</strong></h3>
                        <ul class="list-unstyled">
                            <li>
                                <i class="fas fa-user text-primary"></i>
                                <strong>{{ $pembayaran->nama_pelanggan }}</strong>
                            </li>
                            <li>
                                <i class="fas fa-university text-info"></i>
                                Bank: {{ $pembayaran->bank_tujuan }}
                            </li>

                            @php
                                $bulan = [
                                    1 => 'Januari',
                                    'Februari',
                                    'Maret',
                                    'April',
                                    'Mei',
                                    'Juni',
                                    'Juli',
                                    'Agustus',
                                    'September',
                                    'Oktober',
                                    'November',
                                    'Desember',
                                ];
                                $tanggal = strtotime($pembayaran->waktu_pembayaran);
                                $formattedTanggal =
                                    date('d', $tanggal) .
                                    ' ' .
                                    $bulan[date('n', $tanggal)] .
                                    ' ' .
                                    date('Y H:i:s', $tanggal);
                            @endphp

                            <li>
                                <i class="fas fa-clock text-warning"></i>
                                Waktu Bayar: {{ $formattedTanggal }}
                            </li>

                            <li>
                                <i class="fas fa-money-bill-wave text-success"></i>
                                Total Bayar: Rp. {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                            </li>
                            <li>
                                <i class="fas fa-shield-alt text-danger"></i>
                                Asuransi Return: Opsional
                            </li>
                        </ul>
            </form>
        </div>
        </div>

        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.image-viewer-container');
            const img = container.querySelector('.image-draggable');

            let isDragging = false;
            let startX, startY;
            let translateX = 0,
                translateY = 0;

            // Saat klik tekan (mousedown)
            img.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                img.style.cursor = 'grabbing';
                e.preventDefault(); // Cegah seleksi teks
            });

            // Saat gerakkan mouse
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                img.style.transform = `translate(${translateX}px, ${translateY}px)`;
            });

            // Saat lepas klik
            document.addEventListener('mouseup', function() {
                isDragging = false;
                img.style.cursor = 'grab';
            });

            // Reset posisi saat klik di luar gambar
            container.addEventListener('dblclick', function() {
                translateX = 0;
                translateY = 0;
                img.style.transform = 'translate(0, 0)';
            });
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
    </script>
@endsection
