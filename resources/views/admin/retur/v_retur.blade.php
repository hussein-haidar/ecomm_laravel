@extends('layouts.template')

@push('scripts')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
@endpush

@section('content')

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Data Retur & Pengembalian</h3>
    </div>
    <div class="box-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode retur / produk / pelanggan..." value="{{ request('search') }}">
                    <select name="status" class="form-control ml-2">
                        <option value="">Semua Status</option>
                        @foreach(['Menunggu Verifikasi', 'Disetujui', 'Ditolak', 'Menunggu Pengiriman', 'Barang Dalam Perjalanan', 'Barang Diterima', 'Selesai', 'Dibatalkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary ml-2">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin_data.view_retur') }}" class="btn btn-default ml-2">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Retur</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @php $no = 1; @endphp
                @foreach ($retur as $value)
                @php
                    $badge = match($value->status) {
                        'Menunggu Verifikasi' => 'bg-yellow',
                        'Disetujui' => 'bg-light-blue',
                        'Ditolak' => 'bg-red',
                        'Menunggu Pengiriman' => 'bg-gray',
                        'Barang Dalam Perjalanan' => 'bg-primary',
                        'Barang Diterima' => 'bg-navy',
                        'Selesai' => 'bg-green',
                        'Dibatalkan' => 'bg-black',
                        default => 'bg-gray',
                    };
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td><strong>{{ $value->kode_retur }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($value->waktu_pengajuan)->format('d M Y H:i') }}</td>
                    <td>{{ $value->nama_pelanggan }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('fotoproduk/' . ($value->foto_produk ?? 'default.jpg')) }}" width="45" height="45" style="object-fit: cover;" class="img-circle">
                            <div>
                                <strong>{{ $value->nama_produk }}</strong><br>
                                <small>{{ $value->jumlah_produk }} {{ $value->satuan_produk ?? 'pcs' }} | {{ $value->nama_toko }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($value->tipe_retur == 'penggantian')
                            <span class="label label-info">Penggantian</span>
                        @else
                            <span class="label label-warning">Refund Dana</span>
                        @endif
                    </td>
                    <td><span class="label {{ $badge }}">{{ $value->status }}</span></td>
                    <td>
                        <a href="{{ route('admin_data.detail_retur', $value->id_retur) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center">
            {{ $retur->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#example1').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false
        });
    });
</script>

@endsection