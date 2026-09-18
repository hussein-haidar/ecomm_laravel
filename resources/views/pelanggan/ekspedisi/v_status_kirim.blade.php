@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            {{-- Active Shipments --}}
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="fw-bold mb-0"><i class="fas fa-truck me-2 text-primary"></i>Pengiriman Aktif</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Kode Resi</th>
                                    <th style="width: 80px;">Gambar</th>
                                    <th style="min-width: 200px;">Produk</th>
                                    <th>Ekspedisi</th>
                                    <th style="width: 150px;">Status</th>
                                    <th>Estimasi</th>
                                    <th style="width: 150px;">Est. Tiba</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengiriman_aktif as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-medium">{{ $value->kode_resi ?? '-' }}</td>
                                        <td>
                                            <img src="{{ asset('fotoproduk/' . ($value->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="60" height="60" style="object-fit: cover;" alt="{{ $value->nama_produk }}">
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $value->nama_produk ?? '-' }}</div>
                                            <div class="text-muted small"><i class="fas fa-store me-1"></i>{{ $value->nama_toko ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Kurir:</strong> {{ $value->jenis_kurir }}<br>
                                                <strong>Ongkir:</strong> Rp{{ number_format((int) preg_replace('/[^0-9]/', '', $value->ongkir), 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($value->status_kirim) {
                                                    'Pesanan dibuat' => 'bg-warning text-dark',
                                                    'Dibayar' => 'bg-warning text-dark',
                                                    'Dikemas' => 'bg-info',
                                                    'Dikirim dari toko', 'Disortir', 'Dikirim dari gudang', 'Sampai gudang tujuan', 'Diantar kurir' => 'bg-primary',
                                                    'Sampai tujuan', 'Pesanan diterima' => 'bg-success',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                                                {{ $value->status_kirim ?? 'Belum Dikirim' }}
                                            </span>
                                        </td>
                                        <td>{{ $value->estimasi_waktu ?? '-' }}</td>
                                        <td>{{ $value->estimasi_waktu_tiba ? \Carbon\Carbon::parse($value->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('pelanggan_data.tracking', $value->id_beli) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                                <i class="fas fa-map-marker-alt me-1"></i>Lacak
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Tidak Ada Pengiriman Aktif</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Completed Shipments --}}
            <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="fw-bold mb-0"><i class="fas fa-check-circle me-2 text-success"></i>Pengiriman Selesai</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Kode Resi</th>
                                    <th style="width: 80px;">Gambar</th>
                                    <th style="min-width: 200px;">Produk</th>
                                    <th>Ekspedisi</th>
                                    <th style="width: 150px;">Status</th>
                                    <th>Estimasi</th>
                                    <th style="width: 150px;">Est. Tiba</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengiriman_selesai as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-medium">{{ $value->kode_resi ?? '-' }}</td>
                                        <td>
                                            <img src="{{ asset('fotoproduk/' . ($value->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="60" height="60" style="object-fit: cover;" alt="{{ $value->nama_produk }}">
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $value->nama_produk ?? '-' }}</div>
                                            <div class="text-muted small"><i class="fas fa-store me-1"></i>{{ $value->nama_toko ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Kurir:</strong> {{ $value->jenis_kurir }}<br>
                                                <strong>Ongkir:</strong> Rp{{ number_format((int) preg_replace('/[^0-9]/', '', $value->ongkir), 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($value->status_kirim) {
                                                    'Sampai tujuan', 'Pesanan diterima' => 'bg-success',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                                                {{ $value->status_kirim ?? 'Selesai' }}
                                            </span>
                                        </td>
                                        <td>{{ $value->estimasi_waktu ?? '-' }}</td>
                                        <td>{{ $value->estimasi_waktu_tiba ? \Carbon\Carbon::parse($value->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('pelanggan_data.tracking', $value->id_beli) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum Ada Pengiriman Selesai</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection