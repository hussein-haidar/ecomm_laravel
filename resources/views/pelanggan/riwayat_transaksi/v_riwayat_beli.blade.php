@extends('layouts.app')
@section('content')

    <main class="product-section">
        <div class="container-product">
            <h3 class="text-title">Riwayat Pembelian</h3>

            <!-- Search Box -->
            <div class="mb-3">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari produk atau status..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('pelanggan_data.riwayatBeli') }}"
                            class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-0 mb-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Waktu Pembelian</th>
                            <th>Nama Produk</th>
                            <th>Jumlah Pembelian</th>
                            <th>Ukuran Produk</th>
                            <th>Foto Produk</th>
                            <th>Status Pesanan</th> 
                             <th>Pesanan Diterima</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pembelian->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center">Pembelian anda kosong, tidak ada yang dibeli</td>
                            </tr>
                        @else
                            @php $no = 1; @endphp
                            @foreach ($pembelian as $value)
                                <tr>
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
                                        $tanggal = strtotime($value->waktu_pembelian);
                                        $formattedTanggal =
                                            date('d', $tanggal) .
                                            ' ' .
                                            $bulan[date('n', $tanggal)] .
                                            ' ' .
                                            date('Y H:i:s', $tanggal);
                                    @endphp
                                    <td>{{ $formattedTanggal }}</td>
                                    <td>{{ $value->nama_produk }}</td>
                                    <td>{{ $value->jumlah_produk }} {{ $value->satuan_produk }}</td>
                                    <td>{{ $value->ukuran_produk }}</td>
                                    <td>
                                        <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle"
                                            width="80" height="80">
                                    </td>
                                    <td>
                                        @if ($value->status_kirim == 'Sampai tujuan')
                                            <span class="badge bg-success">Sampai tujuan</span>
                                        @elseif ($value->status_kirim == 'Pesanan diterima')
                                            <span class="badge bg-info">Pesanan diterima</span>
                                        @elseif ($value->status_kirim == 'Pesanan dibuat')
                                            <span class="badge bg-danger">Pesanan dibuat</span>
                                        @elseif ($value->status_kirim == 'Dibayar')
                                            <span class="badge bg-warning">Dibayar</span>
                                        @elseif ($value->status_kirim == 'Dikemas')
                                            <span class="badge bg-primary">Dikemas</span>
                                        @elseif ($value->status_kirim == 'Diantar kurir')
                                            <span class="badge bg-secondary">Diantar kurir</span>
                                        @else
                                            <span
                                                class="badge bg-light text-dark">{{ $value->status_kirim ?: 'Tidak diketahui' }}</span>
                                        @endif
                                    </td>
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
                                        $formattedTanggal = '-';
                                        if ($value->waktu_pesanan_diterima) {
                                            $tanggal = strtotime($value->waktu_pesanan_diterima);
                                            $formattedTanggal =
                                                date('d', $tanggal) .
                                                ' ' .
                                                $bulan[date('n', $tanggal)] .
                                                ' ' .
                                                date('Y H:i:s', $tanggal);
                                        }
                                    @endphp
                                    <td>{{ $formattedTanggal }}</td>
                                    <td>
                                        <a href="{{ url('pelanggan_data/notaPembelian/' . $value->id_beli) }}"
                                            class="btn btn-primary btn-sm">Nota Beli</a>
                                        <a href="{{ url('pelanggan_data/lihatBayar/' . $value->id_bayar) }}"
                                            class="btn btn-success btn-sm">Bukti Bayar</a>
                                        @if (in_array($value->status_kirim, ['Sampai tujuan', 'Pesanan diterima']))
                                            @if (!in_array($value->id_stok, $id_ulasan_selesai))
                                                <button type="button" class="btn btn-warning btn-sm btn-ulasan"
                                                    data-id-beli="{{ $value->id_beli }}"
                                                    data-id-stok="{{ $value->id_stok }}"
                                                    data-nama-produk="{{ $value->nama_produk }}">
                                                    <i class="fas fa-star"></i> Beri Ulasan
                                                </button>
                                            @else
                                                <span class="badge bg-success">Sudah diulas</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                {{ $pembelian->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    <!-- Modal Beri Ulasan -->
    <div class="modal fade" id="modalUlasan" tabindex="-1" aria-labelledby="modalUlasanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('pelanggan_data.simpanUlasan') }}">
                    @csrf
                    <input type="hidden" name="id_beli" id="ulasan_id_beli">
                    <input type="hidden" name="id_stok" id="ulasan_id_stok">
                    <input type="hidden" name="rating" id="ulasan_rating" value="5">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUlasanLabel"><i class="fas fa-star text-warning"></i> Beri
                            Ulasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Produk: <strong id="ulasan_nama_produk"></strong></p>
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="star-rating fs-2" id="star_container">
                                <i class="fas fa-star text-warning star-btn" data-value="1"></i>
                                <i class="fas fa-star text-warning star-btn" data-value="2"></i>
                                <i class="fas fa-star text-warning star-btn" data-value="3"></i>
                                <i class="fas fa-star text-warning star-btn" data-value="4"></i>
                                <i class="fas fa-star text-warning star-btn" data-value="5"></i>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="ulasan_komentar" class="form-label">Komentar</label>
                            <textarea name="komentar" id="ulasan_komentar" class="form-control" rows="4"
                                maxlength="500" placeholder="Tulis ulasan Anda tentang produk ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> Kirim
                            Ulasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('.btn-ulasan').on('click', function() {
            $('#ulasan_id_beli').val($(this).data('id-beli'));
            $('#ulasan_id_stok').val($(this).data('id-stok'));
            $('#ulasan_nama_produk').text($(this).data('nama-produk'));
            $('#ulasan_rating').val(5);
            $('#ulasan_komentar').val('');
            $('.star-btn').addClass('text-warning').removeClass('text-muted');
            $('#modalUlasan').modal('show');
        });

        $('.star-btn').on('click', function() {
            var nilai = $(this).data('value');
            $('#ulasan_rating').val(nilai);
            $('.star-btn').each(function() {
                var n = $(this).data('value');
                if (n <= nilai) {
                    $(this).removeClass('text-muted').addClass('text-warning');
                } else {
                    $(this).removeClass('text-warning').addClass('text-muted');
                }
            });
        });
    </script>

@endsection
