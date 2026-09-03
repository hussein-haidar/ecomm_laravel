@extends('layouts.app')
@section('content')

    <!-- Product Section -->
    <main class="product-section">
         <div class="container-product">
        <h3 class="text-title">Status Pembayaran</h3>

            <div class="row">
                <div class="col-md-5 col-12">
                    <div class="notif-pembayaran alert-info p-2">
                        <p class="notif-title">
                            ℹ️ <strong>Silahkan lakukan pembayaran sebelum jatuh tempo.</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-0 mb-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Waktu Pembayaran</th>
                            <th>Pelanggan</th>
                            <th>Total Tagihan</th>
                            <th>QR Rincian Pembayaran</th>
                            <th>Batas Waktu Bayar</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($pembayaran))
                            <tr>
                                <td colspan="10" class="text-center">Status pembayaran anda kosong, tidak ada tanggungan pembayaran</td>
                            </tr>
                        @else
                            @php $no = 1; @endphp
                            @foreach ($pembayaran as $value)
                                <tr data-id-bayar="{{ $value->id_bayar }}">
                                    <td>{{ $no++ }}</td>
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
                                    @endphp
                                    <td>
                                        @if ($value->waktu_pembayaran && $value->waktu_pembayaran != '0000-00-00 00:00:00')
                                            @php
                                                $timestamp = strtotime($value->waktu_pembayaran);
                                                $formattedTanggal =
                                                    date('d', $timestamp) .
                                                    ' ' .
                                                    $bulan[date('n', $timestamp)] .
                                                    ' ' .
                                                    date('Y H:i:s', $timestamp);
                                            @endphp
                                            {{ $formattedTanggal }}
                                        @else
                                            <span class="text-danger">Silahkan lakukan pembayaran</span>
                                        @endif
                                    </td>
                                    <td>{{ $value->nama_pelanggan }}</td>
                                    <td>Rp. {{ number_format($value->total_bayar, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if ($value->status_bayar == 'Belum Bayar' && !empty($value->qris_url))
                                            <div class="mb-2">
                                                <strong class="text-success">QRIS Midtrans</strong>
                                                <img src="{{ $value->qris_url }}" width="200" alt="QRIS Pembayaran" class="img-fluid border rounded p-1">
                                                <div class="mt-2">
                                                    <small class="text-muted">Scan QRIS dengan GoPay, OVO, DANA, LinkAja, ShopeePay, atau Mobile Banking</small>
                                                </div>
                                            </div>
                                            <hr>
                                            <small class="text-muted">Atau transfer manual:</small>
                                            <img src="{{ $value->qr_url }}" width="120" alt="QR Manual" class="mt-1">
                                        @else
                                            <img src="{{ $value->qr_url }}" width="150" alt="QR Pembayaran">
                                        @endif
                                        <div class="mt-2">
                                            <a href="{{ $value->qr_url }}" download="qr_{{ $value->id_bayar }}.png"
                                                class="btn btn-sm btn-primary">
                                                Unduh QR
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($value->batas_waktu_bayar && $value->batas_waktu_bayar != '0000-00-00 00:00:00')
                                            @php
                                                $timestamp_batas = strtotime($value->batas_waktu_bayar);
                                                $formatted_batas =
                                                    date('d', $timestamp_batas) .
                                                    ' ' .
                                                    $bulan[date('n', $timestamp_batas)] .
                                                    ' ' .
                                                    date('Y H:i:s', $timestamp_batas);
                                            @endphp
                                            <span class="text-danger fw-bold">{{ $formatted_batas }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="label-status 
                                                @if ($value->status_bayar == 'Dibayar') label-success
                                                @elseif ($value->status_bayar == 'Dibatalkan') label-danger
                                                @else label-warning @endif">
                                            {{ $value->status_bayar }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($value->status_bayar == 'Belum Bayar' || $value->status_bayar == 'Dibatalkan')
                                            <a href="{{ url('pelanggan_data/addBayar/' . $value->id_bayar) }}"
                                                class="btn btn-buying">Pembayaran</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const linkWa = "{{ session('link_wa') }}";
            if (linkWa && linkWa !== 'https://wa.me/?text=') {
                setTimeout(() => {
                    window.open(linkWa, '_blank', 'noopener,noreferrer');
                }, 500);
            }

            // Auto-refresh setiap 5 detik jika ada pembayaran "Belum Bayar"
            const pendingPayments = document.querySelectorAll('[data-id-bayar]');
            if (pendingPayments.length > 0) {
                setInterval(function () {
                    pendingPayments.forEach(function (el) {
                        const idBayar = el.getAttribute('data-id-bayar');
                        fetch('{{ url("midtrans/check-status") }}/' + idBayar)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status_bayar === 'Dibayar') {
                                    location.reload();
                                }
                            })
                            .catch(() => {});
                    });
                }, 5000);
            }
        });
    </script>
    
    @endsection

  