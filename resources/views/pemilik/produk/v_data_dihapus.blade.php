@extends('layouts.template')   <!-- Sesuaikan layout aplikasi Laravel Anda -->

@section('content')
<div class="box">
    <div class="box-header">
        <a href="{{ route('pemilik_data.produk') }}" class="btn btn-sm btn-primary">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Produk
        </a>
    </div>

    <div class="box-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Produk</th>
                        <th>Jenis Produk</th>
                        <th>Varian Produk</th>
                        <th>Nama Produk</th>
                        <th>Berat Produk</th>
                        <th>Harga Produk</th>
                        <th>Foto Produk</th>
                        <th>Deskripsi Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_produk_dihapus as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->kode_produk }}</td>
                            <td>{{ $value->jenis_produk }}</td>
                            <td>{{ $value->varian_produk ?? '-' }}</td>
                            <td>{{ $value->nama_produk }}</td>
                            <td>{{ $value->berat_produk }} {{ $value->satuan_berat }}</td>
                            <td>Rp. {{ number_format($value->harga_produk, 0, ',', '.') }}</td>
                            <td>
                                <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle" width="80px" height="80px">
                            </td>
                            <td>{{ $value->deskripsi_produk }}</td>
                            <td>
                                <a href="{{ route('pemilik_data.restore', $value->id_produk) }}" class="btn btn-success btn-xs">
                                    <i class="fa fa-fw fa-undo"></i> Restore
                                </a>
                                <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal" data-target="#delete{{ $value->id_produk }}">
                                    <i class="fa fa-fw fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada produk yang dihapus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Delete -->
@foreach ($data_produk_dihapus as $value)
    <div class="modal fade" id="delete{{ $value->id_produk }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Hapus Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Apakah Anda Ingin Menghapus Data&nbsp;<strong>{{ $value->nama_produk }}</strong> secara permanen?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="{{ route('pemilik_data.delete_hard_produk', $value->id_produk) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection