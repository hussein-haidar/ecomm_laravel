@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Star rating modal
        const modal = document.getElementById('modalUlasan');
        if (modal) {
            modal.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                if (btn) {
                    document.getElementById('ulasan_id_beli').value = btn.dataset.idBeli;
                    document.getElementById('ulasan_id_stok').value = btn.dataset.idStok;
                    document.getElementById('ulasan_nama_produk').textContent = btn.dataset.namaProduk;
                    document.getElementById('ulasan_rating').value = 5;
                    document.getElementById('ulasan_komentar').value = '';
                    document.querySelectorAll('.star-btn').forEach(s => {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    });
                }
            });
        }

        // Star rating click
        document.querySelectorAll('.star-btn').forEach(star => {
            star.addEventListener('click', function() {
                const nilai = this.dataset.value;
                document.getElementById('ulasan_rating').value = nilai;
                document.querySelectorAll('.star-btn').forEach(s => {
                    const n = s.dataset.value;
                    if (n <= nilai) {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    } else {
                        s.classList.remove('text-warning');
                        s.classList.add('text-muted');
                    }
                });
            });
        });
    });
</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Pembelian</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    {{-- Search --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari produk atau status..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Cari</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('pelanggan_data.riwayatBeli') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Waktu Pembelian</th>
                                    <th style="min-width: 200px;">Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Ukuran</th>
                                    <th style="width: 130px;">Status Pesanan</th>
                                    <th style="width: 160px;">Pesanan Diterima</th>
                                    <th style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($pembelian->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum Ada Pembelian</h5>
                                            <p class="text-muted">Anda belum memiliki riwayat pembelian.</p>
                                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                                <i class="fas fa-store me-1"></i>Mulai Belanja
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $bulan = [
                                            1 => 'Januari', 'Februari', 'Maret', 'April',
                                            'Mei', 'Juni', 'Juli', 'Agustus',
                                            'September', 'Oktober', 'November', 'Desember'
                                        ];
                                        $no = 1;
                                    @endphp
                                    @foreach($pembelian as $value)
                                        @php
                                            $tanggal = strtotime($value->waktu_pembelian);
                                            $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y H:i:s', $tanggal);
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $formattedTanggal }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ asset('fotoproduk/' . ($value->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="60" height="60" style="object-fit: cover;" alt="{{ $value->nama_produk }}">
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $value->nama_produk }}</div>
                                                        <div class="text-muted small"><i class="fas fa-store me-1"></i>{{ $value->nama_toko }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $value->jumlah_produk }} {{ $value->satuan_produk }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $value->ukuran_produk }}</span></td>
                                            <td>
                                                @php
                                                    $statusClass = match($value->status_kirim) {
                                                        'Sampai tujuan' => 'bg-success',
                                                        'Pesanan diterima' => 'bg-info',
                                                        'Pesanan dibuat' => 'bg-danger',
                                                        'Dibayar' => 'bg-warning text-dark',
                                                        'Dikemas' => 'bg-primary',
                                                        'Diantar kurir' => 'bg-secondary',
                                                        default => 'bg-light text-dark border'
                                                    };
                                                @endphp
                                                <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                                                    {{ $value->status_kirim ?: 'Tidak diketahui' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $formattedTanggal = '-';
                                                    if ($value->waktu_pesanan_diterima) {
                                                        $tanggal = strtotime($value->waktu_pesanan_diterima);
                                                        $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y H:i:s', $tanggal);
                                                    }
                                                @endphp
                                                {{ $formattedTanggal }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <a href="{{ route('pelanggan_data.notaPembelian', $value->id_beli) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Nota Beli">
                                                        <i class="fas fa-receipt me-1"></i>Nota
                                                    </a>
                                                    @if($value->id_bayar)
                                                        <a href="{{ route('pelanggan_data.lihatBayar', $value->id_bayar) }}" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Bukti Bayar">
                                                            <i class="fas fa-credit-card me-1"></i>Bayar
                                                        </a>
                                                    @endif
                                                    @if(in_array($value->status_kirim, ['Sampai tujuan', 'Pesanan diterima']))
                                                        <a href="{{ route('pelanggan_data.retur') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Ajukan Retur">
                                                            <i class="fas fa-undo-alt me-1"></i>Retur
                                                        </a>
                                                        @if(!in_array($value->id_stok, $id_ulasan_selesai ?? []))
                                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 btn-ulasan" 
                                                                data-bs-toggle="modal" data-bs-target="#modalUlasan"
                                                                data-id-beli="{{ $value->id_beli }}"
                                                                data-id-stok="{{ $value->id_stok }}"
                                                                data-nama-produk="{{ $value->nama_produk }}">
                                                                <i class="fas fa-star me-1"></i>Ulasan
                                                            </button>
                                                        @else
                                                            <span class="badge bg-success rounded-pill px-3 py-2">Sudah diulas</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{ $pembelian->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Beri Ulasan --}}
<div class="modal fade" id="modalUlasan" tabindex="-1" aria-labelledby="modalUlasanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('pelanggan_data.simpanUlasan') }}">
                @csrf
                <input type="hidden" name="id_beli" id="ulasan_id_beli">
                <input type="hidden" name="id_stok" id="ulasan_id_stok">
                <input type="hidden" name="rating" id="ulasan_rating" value="5">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalUlasanLabel"><i class="fas fa-star text-warning me-2"></i>Beri Ulasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Produk: <strong id="ulasan_nama_produk"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Rating</label>
                        <div class="star-rating fs-2" id="star_container">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-warning star-btn me-1" data-value="{{ $i }}" style="cursor: pointer;"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ulasan_komentar" class="form-label fw-medium">Komentar</label>
                        <textarea name="komentar" id="ulasan_komentar" class="form-control" rows="4" maxlength="500" placeholder="Tulis ulasan Anda tentang produk ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4"><i class="fas fa-paper-plane me-1"></i> Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUlasan');
        if (modal) {
            modal.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                if (btn) {
                    document.getElementById('ulasan_id_beli').value = btn.dataset.idBeli;
                    document.getElementById('ulasan_id_stok').value = btn.dataset.idStok;
                    document.getElementById('ulasan_nama_produk').textContent = btn.dataset.namaProduk;
                    document.getElementById('ulasan_rating').value = 5;
                    document.getElementById('ulasan_komentar').value = '';
                    document.querySelectorAll('.star-btn').forEach(s => {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    });
                }
            });
        }

        document.querySelectorAll('.star-btn').forEach(star => {
            star.addEventListener('click', function() {
                const nilai = this.dataset.value;
                document.getElementById('ulasan_rating').value = nilai;
                document.querySelectorAll('.star-btn').forEach(s => {
                    const n = s.dataset.value;
                    if (n <= nilai) {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    } else {
                        s.classList.remove('text-warning');
                        s.classList.add('text-muted');
                    }
                });
            });
        });
    });
</script>
@endsection