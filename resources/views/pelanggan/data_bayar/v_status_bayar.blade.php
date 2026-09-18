@extends('layouts.app')
@push('scripts')
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
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Status Pembayaran</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if(empty($pembayaran))
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>Belum Ada Tagihan</h5>
                            <p class="text-muted">Anda tidak memiliki tanggungan pembayaran saat ini.</p>
                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                <i class="fas fa-shopping-bag me-1"></i>Mulai Belanja
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Silahkan lakukan pembayaran sebelum jatuh tempo.</strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Waktu Pembayaran</th>
                                        <th>Pelanggan</th>
                                        <th class="text-end">Total Tagihan</th>
                                        <th style="width: 200px;">QR Pembayaran</th>
                                        <th>Batas Waktu Bayar</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $bulan = [
                                            1 => 'Januari', 'Februari', 'Maret', 'April',
                                            'Mei', 'Juni', 'Juli', 'Agustus',
                                            'September', 'Oktober', 'November', 'Desember'
                                        ];
                                        $no = 1;
                                    @endphp
                                    @foreach ($pembayaran as $value)
                                        <tr data-id-bayar="{{ $value->id_bayar }}">
                                            <td>{{ $no++ }}</td>

                                            <td>
                                                @if ($value->waktu_pembayaran && $value->waktu_pembayaran != '0000-00-00 00:00:00')
                                                    @php
                                                        $timestamp = strtotime($value->waktu_pembayaran);
                                                        $formattedTanggal = date('d', $timestamp) . ' ' . $bulan[date('n', $timestamp)] . ' ' . date('Y H:i:s', $timestamp);
                                                    @endphp
                                                    {{ $formattedTanggal }}
                                                @else
                                                    <span class="text-danger">Belum dibayar</span>
                                                @endif
                                            </td>

                                            <td class="fw-medium">{{ $value->nama_pelanggan }}</td>

                                            <td class="text-end fw-semibold text-success">Rp{{ number_format($value->total_bayar, 0, ',', '.') }}</td>

                                            <td class="text-center">
                                                @if ($value->status_bayar == 'Belum Bayar' && !empty($value->qris_url))
                                                    <div class="mb-2">
                                                        <strong class="text-success small">QRIS Midtrans</strong><br>
                                                        <img src="{{ $value->qris_url }}" width="160" alt="QRIS Pembayaran" class="img-fluid border rounded p-1 shadow-sm">
                                                        <div class="mt-1">
                                                            <small class="text-muted">Scan dengan GoPay, OVO, DANA, LinkAja, ShopeePay, atau Mobile Banking</small>
                                                        </div>
                                                    </div>
                                                    <hr class="my-2">
                                                    <small class="text-muted">Atau transfer manual:</small><br>
                                                    <img src="{{ $value->qr_url }}" width="100" alt="QR Manual" class="mt-1">
                                                @else
                                                    <img src="{{ $value->qr_url }}" width="130" alt="QR Pembayaran" class="border rounded p-1">
                                                @endif
                                                <div class="mt-2">
                                                    <a href="{{ $value->qr_url }}" download="qr_{{ $value->id_bayar }}.png" class="btn btn-sm btn-outline-primary rounded-pill">
                                                        <i class="fas fa-download me-1"></i>Unduh QR
                                                    </a>
                                                </div>
                                            </td>

                                            <td>
                                                @if ($value->batas_waktu_bayar && $value->batas_waktu_bayar != '0000-00-00 00:00:00')
                                                    @php
                                                        $timestamp_batas = strtotime($value->batas_waktu_bayar);
                                                        $formatted_batas = date('d', $timestamp_batas) . ' ' . $bulan[date('n', $timestamp_batas)] . ' ' . date('Y H:i:s', $timestamp_batas);
                                                    @endphp
                                                    <span class="text-danger fw-bold small">{{ $formatted_batas }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <span class="badge rounded-pill px-3 py-2
                                                    @if ($value->status_bayar == 'Dibayar') bg-success
                                                    @elseif ($value->status_bayar == 'Dibatalkan') bg-danger
                                                    @else bg-warning text-dark @endif">
                                                    {{ $value->status_bayar }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @if ($value->status_bayar == 'Belum Bayar' || $value->status_bayar == 'Dibatalkan')
                                                    <a href="{{ route('pelanggan_data.addBayar', $value->id_bayar) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                                        <i class="fas fa-credit-card me-1"></i>Bayar
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection