@extends('layouts.app')
@push('styles')
<style>
    .timeline-retur { position: relative; padding-left: 32px; }
    .timeline-retur::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-retur .tl-item { position: relative; margin-bottom: 1.1rem; }
    .timeline-retur .tl-dot {
        position: absolute;
        left: -32px;
        top: 3px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #adb5bd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }
    .timeline-retur .tl-item.done .tl-label { color: #212529; font-weight: 600; }
    .timeline-retur .tl-item.done .tl-dot { border-color: #198754; background: #d1e7dd; color: #198754; }
    .timeline-retur .tl-item.active .tl-dot { border-color: #0d6efd; background: #cfe2ff; color: #0d6efd; }
    .timeline-retur .tl-item.active .tl-label { color: #0d6efd; font-weight: 700; }
    .timeline-retur .tl-item.pending .tl-label { color: #6c757d; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <a href="{{ route('pelanggan_data.retur') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Retur
            </a>

            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h4 class="fw-bold mb-0"><i class="fas fa-undo-alt me-2 text-primary"></i>Detail Retur</h4>
                    @php
                        $statusClass = match($retur->status) {
                            'Menunggu Verifikasi' => 'bg-warning text-dark',
                            'Disetujui' => 'bg-info',
                            'Ditolak' => 'bg-danger',
                            'Menunggu Pengiriman' => 'bg-secondary',
                            'Barang Dalam Perjalanan' => 'bg-primary',
                            'Barang Diterima' => 'bg-dark',
                            'Selesai' => 'bg-success',
                            'Dibatalkan' => 'bg-light text-dark border',
                            default => 'bg-light text-dark border',
                        };
                    @endphp
                    <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">{{ $retur->status }}</span>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4">
                        {{-- Info Retur --}}
                        <div class="col-lg-7">
                            <div class="d-flex gap-3 mb-4">
                                <img src="{{ asset('fotoproduk/' . ($retur->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="90" height="90" style="object-fit: cover;" alt="{{ $retur->nama_produk }}">
                                <div>
                                    <div class="h5 fw-bold mb-1">{{ $retur->nama_produk }}</div>
                                    <div class="text-muted small">
                                        <i class="fas fa-store me-1"></i>{{ $retur->nama_toko }} |
                                        <i class="fas fa-hashtag me-1"></i>{{ $retur->kode_retur }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $retur->jumlah_produk }} {{ $retur->satuan_produk ?? 'pcs' }}
                                        @if($retur->ukuran_produk) | Ukuran: {{ $retur->ukuran_produk }} @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <div class="text-muted">Tipe Retur</div>
                                    <div class="fw-semibold">{{ ucwords(str_replace('_', ' ', $retur->tipe_retur)) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted">Jenis Alasan</div>
                                    <div class="fw-semibold">{{ $retur->jenis_alasan }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted">Tanggal Pengajuan</div>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($retur->waktu_pengajuan)->format('d M Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted">No. HP</div>
                                    <div class="fw-semibold">{{ $retur->no_telp ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-muted small fw-semibold">Alasan Retur</div>
                                <p class="mb-0">{{ $retur->alasan }}</p>
                            </div>

                            @if($retur->foto_bukti)
                                <div class="mt-4">
                                    <div class="text-muted small fw-semibold mb-2">Foto Bukti</div>
                                    <a href="{{ asset('fotoretur/' . $retur->foto_bukti) }}" target="_blank">
                                        <img src="{{ asset('fotoretur/' . $retur->foto_bukti) }}" class="rounded border" width="180" height="180" style="object-fit: cover;" alt="Foto bukti retur">
                                    </a>
                                </div>
                            @endif

                            @if($retur->catatan_admin)
                                <div class="mt-4 p-3 rounded bg-light border">
                                    <div class="text-muted small fw-semibold mb-1">Catatan Toko</div>
                                    <p class="mb-0">{{ $retur->catatan_admin }}</p>
                                </div>
                            @endif

                            {{-- Info Refund --}}
                            @if($retur->status == 'Selesai' && $retur->tipe_retur == 'pengembalian_dana')
                                <div class="mt-4 p-3 rounded border border-success bg-success-subtle">
                                    <div class="fw-semibold text-success mb-1"><i class="fas fa-wallet me-1"></i>Informasi Refund</div>
                                    <div class="small">Jumlah Refund: <strong>Rp {{ number_format($retur->jumlah_refund ?? 0, 0, ',', '.') }}</strong></div>
                                    <div class="small">Status Refund: <strong>{{ $retur->status_refund ?: 'Belum Diproses' }}</strong></div>
                                </div>
                            @endif
                        </div>

                        {{-- Timeline Status + Aksi --}}
                        <div class="col-lg-5">
                            <div class="card border-0 bg-light rounded-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-stream me-2 text-primary"></i>Alur Retur</h6>
                                    @php
                                        $steps = ['Menunggu Verifikasi', 'Disetujui', 'Barang Dalam Perjalanan', 'Barang Diterima', 'Selesai'];
                                        $curIdx = array_search($retur->status, $steps);
                                    @endphp
                                    <div class="timeline-retur">
                                        @foreach($steps as $i => $step)
                                            @php
                                                $cls = 'pending';
                                                if ($retur->status == 'Ditolak' || $retur->status == 'Dibatalkan') {
                                                    $cls = $i == 0 ? 'done' : 'pending cancel';
                                                } elseif (!is_bool($curIdx) && $i < $curIdx+1) {
                                                    $cls = $i == $curIdx ? 'active' : 'done';
                                                }
                                            @endphp
                                            <div class="tl-item {{ $cls }}">
                                                <div class="tl-dot">
                                                    @if(!is_bool($curIdx) && $i < $curIdx) <i class="fas fa-check"></i>
                                                    @elseif($i == 0) <i class="fas fa-clipboard-list"></i>
                                                    @else <i class="fas fa-angle-right"></i> @endif
                                                </div>
                                                <div class="tl-label">{{ $step }}</div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($retur->status == 'Ditolak')
                                        <div class="alert alert-danger small mt-3 mb-0">
                                            <i class="fas fa-times-circle me-1"></i>Retur ditolak. {{ $retur->catatan_admin ?: 'Silakan hubungi toko.' }}
                                        </div>
                                    @endif

                                    @if($retur->status == 'Dibatalkan')
                                        <div class="alert alert-secondary small mt-3 mb-0">
                                            <i class="fas fa-ban me-1"></i>Pengajuan retur dibatalkan.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Aksi Pelanggan --}}
                            @if(in_array($retur->status, ['Disetujui', 'Menunggu Pengiriman']))
                                <div class="card border-0 bg-light rounded-3 mt-3">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2"><i class="fas fa-truck me-2 text-primary"></i>Kirim Barang Kembali</h6>
                                        <p class="small text-muted mb-3">
                                            Retur telah disetujui. Silakan kirim barang kembali ke alamat toko:
                                            <strong>{{ $retur->alamat_pengembalian ?: $retur->nama_toko }}</strong>
                                        </p>
                                        <form method="POST" action="{{ route('pelanggan_data.kirimRetur', $retur->id_retur) }}">
                                            @csrf
                                            <div class="mb-2">
                                                <input type="text" name="nomor_resi" class="form-control" placeholder="Nomor Resi pengiriman (opsional)" maxlength="100">
                                            </div>
                                            <div class="mb-3">
                                                <textarea name="alamat_pengembalian" class="form-control" rows="2" placeholder="Alamat pengambilan barang oleh kurir (opsional)">{{ $retur->alamat_pengembalian }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary rounded-pill w-100">
                                                <i class="fas fa-paper-plane me-1"></i>Saya Sudah Kirim Barang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if(in_array($retur->status, ['Menunggu Verifikasi', 'Disetujui', 'Menunggu Pengiriman']))
                                <form method="POST" action="{{ route('pelanggan_data.batalkanRetur', $retur->id_retur) }}"
                                      onsubmit="return confirm('Batalkan pengajuan retur ini?');" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                                        <i class="fas fa-ban me-1"></i>Batalkan Retur
                                    </button>
                                </form>
                            @endif

                            @if($retur->status == 'Barang Dalam Perjalanan' && $retur->nomor_resi)
                                <div class="mt-3 p-3 rounded border bg-white">
                                    <div class="text-muted small">Nomor Resi Pengiriman</div>
                                    <div class="fw-semibold">{{ $retur->nomor_resi }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection