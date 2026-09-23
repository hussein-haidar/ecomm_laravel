@extends('layouts.app')
@push('styles')
<style>
    .retur-badge { min-width: 140px; }
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
            @if(session('pesan'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('pesan') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h4 class="fw-bold mb-0"><i class="fas fa-undo-alt me-2 text-primary"></i>Retur & Pengembalian</h4>
                    @if($pembelian_eligible->count() > 0)
                        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjukanRetur">
                            <i class="fas fa-plus me-1"></i>Ajukan Retur
                        </button>
                    @endif
                </div>
                <div class="card-body p-4 p-lg-5">
                    {{-- Filter & Search --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari kode retur atau produk..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach(['Menunggu Verifikasi', 'Disetujui', 'Ditolak', 'Menunggu Pengiriman', 'Barang Dalam Perjalanan', 'Barang Diterima', 'Selesai', 'Dibatalkan'] as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('pelanggan_data.retur') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    @if($retur->isEmpty() && !$pembelian_eligible->count())
                        <div class="text-center py-5">
                            <i class="fas fa-undo-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Pengajuan Retur</h5>
                            <p class="text-muted">Anda dapat mengajukan retur untuk pesanan yang sudah sampai tujuan.<br>
                                <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                    <i class="fas fa-store me-1"></i>Mulai Belanja
                                </a>
                            </p>
                        </div>
                    @elseif($retur->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Riwayat Retur</h5>
                            <p class="text-muted">Anda memiliki pesanan yang berhak diretur. Klik "Ajukan Retur".</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Kode Retur</th>
                                        <th style="min-width: 200px;">Produk</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($retur as $item)
                                        @php
                                            $statusClass = match($item->status) {
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
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td><span class="fw-semibold text-dark">{{ $item->kode_retur }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ asset('fotoproduk/' . ($item->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="60" height="60" style="object-fit: cover;" alt="{{ $item->nama_produk }}">
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $item->nama_produk }}</div>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-store me-1"></i>{{ $item->nama_toko }}
                                                            @if($item->jumlah_produk) · {{ $item->jumlah_produk }} {{ $item->satuan_produk ?? 'pcs' }} @endif
                                                        </div>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-tag me-1"></i>{{ ucwords(str_replace('_', ' ', $item->tipe_retur)) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->waktu_pengajuan)->format('d M Y H:i') }}</td>
                                            <td><span class="badge retur-badge rounded-pill px-3 py-2 {{ $statusClass }}">{{ $item->status }}</span></td>
                                            <td>
                                                <a href="{{ route('pelanggan_data.detailRetur', $item->id_retur) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $retur->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajukan Retur --}}
@if($pembelian_eligible->count() > 0)
<div class="modal fade" id="modalAjukanRetur" tabindex="-1" aria-labelledby="modalAjukanReturLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('pelanggan_data.ajukanRetur') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalAjukanReturLabel"><i class="fas fa-undo-alt text-primary me-2"></i>Ajukan Retur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-medium">Pilih Pesanan <span class="text-danger">*</span></label>
                        <select name="id_beli" id="retur_id_beli" class="form-select" required>
                            <option value="">-- Pilih pesanan yang sudah sampai --</option>
                            @foreach($pembelian_eligible as $pb)
                                <option value="{{ $pb->id_beli }}"
                                    data-produk="{{ $pb->nama_produk }}"
                                    data-jumlah="{{ $pb->jumlah_produk }}"
                                    data-satuan="{{ $pb->satuan_produk }}"
                                    data-ukuran="{{ $pb->ukuran_produk }}">
                                    #{{ $pb->id_beli }} — {{ $pb->nama_produk }} ({{ $pb->jumlah_produk }} {{ $pb->satuan_produk }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text"><i class="fas fa-info-circle me-1"></i>Pesanan yang ditampilkan sudah berstatus "Sampai tujuan" atau "Pesanan diterima" dan belum pernah diretur.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Jenis Alasan <span class="text-danger">*</span></label>
                            <select name="jenis_alasan" class="form-select" required>
                                <option value="Produk rusak/cacat">Produk rusak/cacat</option>
                                <option value="Barang salah/keliru">Barang salah/keliru</option>
                                <option value="Tidak sesuai deskripsi">Tidak sesuai deskripsi</option>
                                <option value="Barang tidak lengkap">Barang tidak lengkap</option>
                                <option value="Alasan lain">Alasan lain</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tipe Retur <span class="text-danger">*</span></label>
                            <select name="tipe_retur" class="form-select" required>
                                <option value="penggantian">Penggantian Barang</option>
                                <option value="pengembalian_dana">Pengembalian Dana (Refund)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-medium">Detail Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control" rows="4" maxlength="2000" placeholder="Jelaskan kondisi barang, misalnya: kain robek di bagian lengan, warna tidak sesuai, dll." required></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Foto Bukti (opsional)</label>
                            <input type="file" name="foto_bukti" class="form-control" accept="image/*">
                            <div class="form-text">Maks 2 MB (JPG/PNG). Foto kondisi barang / kemasan.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">No. HP (opsional)</label>
                            <input type="text" name="no_telp" class="form-control" maxlength="30" placeholder="Contoh: 0812xxxxxx">
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded bg-light border">
                        <div class="fw-semibold mb-2"><i class="fas fa-clipboard-list me-1"></i>Ringkasan Pesanan</div>
                        <div class="row g-2 small">
                            <div class="col-6">Produk</div>
                            <div class="col-6 text-end" id="r_produk">-</div>
                            <div class="col-6">Jumlah</div>
                            <div class="col-6 text-end" id="r_jumlah">-</div>
                            <div class="col-6">Ukuran</div>
                            <div class="col-6 text-end" id="r_ukuran">-</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-paper-plane me-1"></i>Ajukan Retur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('retur_id_beli');
        if (select) {
            const update = () => {
                const opt = select.options[select.selectedIndex];
                document.getElementById('r_produk').textContent = opt.dataset.produk || '-';
                document.getElementById('r_jumlah').textContent = opt.dataset.jumlah ? opt.dataset.jumlah + ' ' + (opt.dataset.satuan || 'pcs') : '-';
                document.getElementById('r_ukuran').textContent = opt.dataset.ukuran || '-';
            };
            select.addEventListener('change', update);
            update();
        }
    });
</script>
@endsection