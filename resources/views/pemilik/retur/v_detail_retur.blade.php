@extends('layouts.template')

@section('content')

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <strong>{{ $retur->kode_retur }}</strong>
            <span class="label {{ $retur->status == 'Selesai' ? 'bg-green' : ($retur->status == 'Ditolak' ? 'bg-red' : ($retur->status == 'Menunggu Verifikasi' ? 'bg-yellow' : 'bg-light-blue')) }} ml-2">{{ $retur->status }}</span>
        </h3>
        <div class="box-tools pull-right">
            <a href="{{ route('pemilik_data.view_retur') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="box-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="row">
            {{-- Info Retur --}}
            <div class="col-md-7">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Informasi Retur</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped">
                            <tr>
                                <th style="width:220px;">Pelanggan</th>
                                <td>: {{ $retur->nama_pelanggan }}</td>
                            </tr>
                            <tr>
                                <th>Produk</th>
                                <td>: {{ $retur->nama_produk }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td>: {{ $retur->jumlah_produk }} {{ $retur->satuan_produk ?? 'pcs' }}
                                    @if($retur->ukuran_produk) (Ukuran: {{ $retur->ukuran_produk }}) @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tipe Retur</th>
                                <td>:
                                    @if($retur->tipe_retur == 'penggantian')
                                        <span class="label label-info">Penggantian Barang</span>
                                    @else
                                        <span class="label label-warning">Pengembalian Dana</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jenis Alasan</th>
                                <td>: {{ $retur->jenis_alasan }}</td>
                            </tr>
                            <tr>
                                <th>Alasan</th>
                                <td>: {{ $retur->alasan }}</td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>: {{ $retur->no_telp ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kode Order</th>
                                <td>: #{{ $retur->id_beli }}</td>
                            </tr>
                            <tr>
                                <th>Total Pembayaran</th>
                                <td>: Rp. {{ number_format($retur->total_bayar ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <td>: {{ \Carbon\Carbon::parse($retur->waktu_pengajuan)->format('d M Y H:i') }}</td>
                            </tr>
                            @if($retur->waktu_verifikasi)
                                <tr>
                                    <th>Tanggal Verifikasi</th>
                                    <td>: {{ \Carbon\Carbon::parse($retur->waktu_verifikasi)->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($retur->waktu_selesai)
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td>: {{ \Carbon\Carbon::parse($retur->waktu_selesai)->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                        </table>

                        @if($retur->foto_bukti)
                            <div class="mt-3">
                                <label>Foto Bukti</label><br>
                                <a href="{{ asset('fotoretur/' . $retur->foto_bukti) }}" target="_blank">
                                    <img src="{{ asset('fotoretur/' . $retur->foto_bukti) }}" width="200" height="200" style="object-fit: cover;" class="img-thumbnail">
                                </a>
                            </div>
                        @endif

                        @if($retur->nomor_resi)
                            <div class="mt-3">
                                <label>Nomor Resi Pengiriman Balik</label>
                                <span class="text-bold">{{ $retur->nomor_resi }}</span>
                            </div>
                        @endif

                        @if($retur->alamat_pengembalian)
                            <div class="mt-3">
                                <label>Alamat Pengambilan / Pengembalian</label>
                                <p class="text-muted">{{ $retur->alamat_pengembalian }}</p>
                            </div>
                        @endif

                        @if($retur->catatan_admin)
                            <div class="mt-3">
                                <label>Catatan Toko</label>
                                <p class="text-muted">{{ $retur->catatan_admin }}</p>
                            </div>
                        @endif

                        @if($retur->status == 'Selesai' && $retur->tipe_retur == 'pengembalian_dana')
                            <div class="mt-3 alert alert-success">
                                <strong>Refund:</strong> Rp. {{ number_format($retur->jumlah_refund ?? 0, 0, ',', '.') }}
                                ({{ $retur->status_refund ?: 'Belum Diproses' }})
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="col-md-5">
                @if($retur->status == 'Menunggu Verifikasi')
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Verifikasi Retur</h3>
                        </div>
                        <div class="box-body">
                            <p class="text-muted">
                                Setujui retur untuk menginstruksikan pelanggan mengirim barang kembali,
                                atau tolak jika tidak memenuhi syarat.
                            </p>
                            <form method="POST" action="{{ route('pemilik_data.verifikasi_retur', $retur->id_retur) }}">
                                @csrf
                                <div class="form-group">
                                    <label>Keputusan</label>
                                    <select name="keputusan" class="form-control" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="setuju">Setujui</option>
                                        <option value="tolak">Tolak</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Catatan untuk Pelanggan</label>
                                    <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: Silakan kirim kembali ke alamat toko. / Maaf retur tidak dapat diproses karena..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-check"></i> Proses Keputusan
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($retur->status == 'Barang Dalam Perjalanan')
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Barang Dikirim Pelanggan</h3>
                        </div>
                        <div class="box-body">
                            <p class="text-muted">
                                Konfirmasi setelah barang retur dari pelanggan diterima toko.
                                @if($retur->nomor_resi)
                                    <br>Resi: <strong>{{ $retur->nomor_resi }}</strong>
                                @endif
                            </p>
                            <form method="POST" action="{{ route('pemilik_data.terima_retur', $retur->id_retur) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-box-open"></i> Terima Barang Retur
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif(in_array($retur->status, ['Barang Diterima', 'Selesai']))
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Selesaikan Retur</h3>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('pemilik_data.selesaikan_retur', $retur->id_retur) }}">
                                @csrf
                                @if($retur->tipe_retur == 'pengembalian_dana')
                                    <div class="form-group">
                                        <label>Jumlah Refund (Rp)</label>
                                        <input type="number" name="jumlah_refund" class="form-control" min="0" step="0.01"
                                               value="{{ $retur->jumlah_refund ?: $retur->total_bayar }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Status Refund</label>
                                        <select name="status_refund" class="form-control">
                                            <option value="Belum Diproses" {{ $retur->status_refund == 'Belum Diproses' ? 'selected' : '' }}>Belum Diproses</option>
                                            <option value="Refund Diproses" {{ $retur->status_refund == 'Refund Diproses' ? 'selected' : '' }}>Refund Diproses</option>
                                            <option value="Refund Selesai" {{ $retur->status_refund == 'Refund Selesai' ? 'selected' : '' }}>Refund Selesai</option>
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Catatan</label>
                                    <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan penyelesaian...">{{ $retur->catatan_admin }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-check"></i> Tandai Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($retur->catatan_admin)
                    <div class="box box-solid">
                        <div class="box-header with-border"><h3 class="box-title">Riwayat Catatan</h3></div>
                        <div class="box-body">
                            <div class="callout callout-info">
                                <strong>{{ $retur->status }}</strong><br>
                                {{ $retur->catatan_admin }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection