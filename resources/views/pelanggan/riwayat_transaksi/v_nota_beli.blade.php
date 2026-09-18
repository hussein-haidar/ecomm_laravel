@extends('layouts.app')
@push('scripts')
<script>
    function cetakNota() {
        window.print();
    }
</script>
@endpush

@section('content')
<div class="container my-4">
    <div class="d-flex flex-wrap gap-2 mb-4 no-print">
        <a href="{{ route('pelanggan_data.unduhNotaPdf', $pembelian->id_beli) }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-download me-1"></i> Unduh Nota
        </a>
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="cetakNota()">
            <i class="fas fa-print me-1"></i> Cetak Nota
        </button>
        <a href="{{ route('pelanggan_data.riwayatBeli') }}" class="btn btn-outline-secondary rounded-pill px-4 ms-auto">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>

    @if($pembelian)
        <div id="nota-area" class="card border-0 shadow-lg" style="border-radius: 16px; max-width: 900px; margin: 0 auto;">
            {{-- Header --}}
            <div class="card-header bg-white border-0" style="background: linear-gradient(135deg, #1d4ed8, #7c3aed); border-radius: 16px 16px 0 0; color: #fff; padding: 24px;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="nota-logo d-flex align-items-center justify-content-center overflow-hidden" style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.95); flex-shrink: 0;">
                            @if(!empty($pembelian->logo_website))
                                <img src="{{ asset('logowebsite/' . $pembelian->logo_website) }}" alt="Logo {{ $pembelian->nama_toko }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-store" style="font-size: 28px; color: #1d4ed8;"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $pembelian->nama_toko }}</h4>
                            <div class="small opacity-75">{{ $pembelian->alamat_pusat }}</div>
                            <div class="small opacity-75"><i class="fab fa-whatsapp me-1"></i>{{ $pembelian->wa_pusat ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-3" style="letter-spacing: 1px;">NOTA PEMBELIAN</div>
                        @php
                            $badgeStatus = [
                                'Berhasil' => 'bg-success',
                                'Ditunda' => 'bg-warning text-dark',
                                'Dibatalkan' => 'bg-danger',
                            ];
                            $kelasStatus = $badgeStatus[$pembelian->status_beli] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $kelasStatus }} rounded-pill px-3 py-2 mt-2" style="font-size: 12px;">
                            <i class="fas fa-check-circle me-1"></i>{{ $pembelian->status_beli }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-lg-5">
                {{-- Info Transaksi & Pengiriman --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-file-invoice me-1"></i> Informasi Transaksi</h6>
                            <table class="table table-borderless mb-0">
                                <tr><td class="text-muted" style="width: 45%;">Kode Transaksi</td><td class="fw-bold">{{ $pembelian->kode_beli }}</td></tr>
                                <tr><td class="text-muted">Waktu Pembelian</td><td>{{ $tanggalBeli }}</td></tr>
                                <tr><td class="text-muted">Status Pembelian</td><td>{{ $pembelian->status_beli }}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-truck me-1"></i> Informasi Pengiriman</h6>
                            <table class="table table-borderless mb-0">
                                <tr><td class="text-muted" style="width: 45%;">Kurir</td><td>{{ $pembelian->jenis_kurir ?: '-' }}</td></tr>
                                <tr><td class="text-muted">Ongkos Kirim</td><td>{{ $rupiah($ongkir) }}</td></tr>
                                <tr><td class="text-muted">Estimasi Tiba</td><td>{{ $estimasiTiba }}</td></tr>
                                <tr><td class="text-muted">Total Berat</td><td>{{ number_format($totalBerat, 0, ',', '.') }} {{ $pembelian->satuan_berat }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Penerima & Pengirim --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-user me-1"></i> Penerima</h6>
                            <table class="table table-borderless mb-0">
                                <tr><td class="text-muted" style="width: 30%;">Nama</td><td class="fw-bold">{{ $pembelian->nama_pelanggan }}</td></tr>
                                <tr><td class="text-muted">No. Telepon</td><td>{{ $pembelian->no_telpon }}</td></tr>
                                <tr><td class="text-muted">Email</td><td>{{ $pembelian->email }}</td></tr>
                                <tr><td class="text-muted">Alamat</td><td>{{ $pembelian->alamat }}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-store me-1"></i> Pengirim</h6>
                            <table class="table table-borderless mb-0">
                                <tr><td class="text-muted" style="width: 30%;">Nama Toko</td><td>{{ $pembelian->nama_toko }}</td></tr>
                                <tr><td class="text-muted">Alamat</td><td>{{ $pembelian->alamat_pusat }}</td></tr>
                                <tr><td class="text-muted">Kontak</td><td>{{ $pembelian->wa_pusat ?: '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Detail Produk --}}
                <h5 class="fw-bold mb-3"><i class="fas fa-box-open me-1 text-primary"></i> Detail Produk</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th style="width: 10%;">Foto</th>
                                <th>Nama Produk</th>
                                <th>Ukuran</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    @if(!empty($pembelian->foto_produk))
                                        <img src="{{ asset('fotoproduk/' . $pembelian->foto_produk) }}" class="rounded border" width="64" height="64" style="object-fit: cover;" alt="{{ $pembelian->nama_produk }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width: 64px; height: 64px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $pembelian->nama_produk }}</div>
                                    <small class="text-muted">{{ $pembelian->berat_produk }} {{ $pembelian->satuan_berat }}</small>
                                </td>
                                <td>{{ $pembelian->ukuran_produk }}</td>
                                <td class="text-end">{{ $rupiah($pembelian->harga_produk) }}</td>
                                <td class="text-center">{{ $pembelian->jumlah_produk }} {{ $pembelian->satuan_produk }}</td>
                                <td class="text-end fw-semibold">{{ $rupiah($subtotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Ringkasan --}}
                <div class="d-flex justify-content-end mb-4">
                    <div class="card bg-light border-0 shadow-sm" style="width: 280px; max-width: 100%; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Produk</span>
                                <span>{{ $rupiah($subtotal) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span>{{ $rupiah($ongkir) }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold fs-5 text-primary">
                                <span>Total Bayar</span>
                                <span>{{ $rupiah($totalBayar) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card bg-primary bg-opacity-10 border-0 rounded-3 p-4 mt-4 text-center">
                    <p class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-heart me-1"></i>
                        Terima kasih sudah berbelanja di {{ $pembelian->nama_toko }}.
                        <br>Simpan atau unduh nota ini sebagai bukti pembelian Anda.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center py-5">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Data Nota Tidak Ditemukan</h5>
            <a href="{{ route('pelanggan_data.riwayatBeli') }}" class="alert-link">Kembali ke Riwayat Pembelian</a>
        </div>
    @endif
</div>

<script>
    function cetakNota() {
        window.print();
    }
</script>
@endsection