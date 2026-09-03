@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header">
            <a href="{{ route('pemilik_data.add_flash_sale') }}" class="btn btn-sm">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
            </a>
        </div>

        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">No</th>
                            <th>Nama Flash Sale</th>
                            <th>Produk</th>
                            <th>Harga Normal</th>
                            <th>Harga Flash Sale</th>
                            <th>Kuota</th>
                            <th>Terjual</th>
                            <th>Sisa Kuota</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($flash_sales as $value)
                            @foreach ($value->items as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $value->nama_flash_sale }}</td>
                                    <td>{{ $item->nama_produk }} ({{ $item->ukuran_produk ?? '-' }})</td>
                                    <td>Rp. {{ number_format($item->harga_normal, 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($item->harga_flash_sale, 0, ',', '.') }}</td>
                                    <td>{{ $item->kuota }}</td>
                                    <td>{{ $item->terjual ?? 0 }}</td>
                                    <td>{{ max(0, ($item->kuota ?? 0) - ($item->terjual ?? 0)) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($value->waktu_mulai)->locale('id')->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($value->waktu_selesai)->locale('id')->translatedFormat('d F Y H:i') }}</td>
                                    <td>
                                        @php
                                            $berjalan = now()->between(\Carbon\Carbon::parse($value->waktu_mulai), \Carbon\Carbon::parse($value->waktu_selesai));
                                        @endphp
                                        @if ($value->status == 'Aktif' && $berjalan)
                                            <span class="label label-success">Aktif</span>
                                        @elseif ($value->status != 'Aktif')
                                            <span class="label label-danger">Nonaktif</span>
                                        @else
                                            <span class="label label-warning">{{ now() < \Carbon\Carbon::parse($value->waktu_mulai) ? 'Belum Mulai' : 'Berakhir' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pemilik_data.edit_flash_sale', $value->id_flash_sale) }}"
                                            class="btn btn-xs btn-warning">
                                            <i class="fa fa-fw fa-edit"></i>Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                            data-target="#delete{{ $value->id_flash_sale }}">
                                            <i class="fa fa-fw fa-trash"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($flash_sales as $value)
        <div class="modal fade" id="delete{{ $value->id_flash_sale }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Hapus {{ $title ?? 'Data Flash Sale' }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <h4>Apakah Anda Ingin Menghapus Data&nbsp;<b>{{ $value->nama_flash_sale }}</b>?</h4>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('pemilik_data.delete_flash_sale', $value->id_flash_sale) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-success pull-left btn-flat">Delete</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
