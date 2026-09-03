@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header">
            <a href="{{ route('pemilik_data.add_kupon') }}" class="btn btn-sm">
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
                            <th>Kode Kupon</th>
                            <th>Nama Kupon</th>
                            <th>Tipe Diskon</th>
                            <th>Nilai Diskon</th>
                            <th>Minimal Pembelian</th>
                            <th>Maks Diskon</th>
                            <th>Terpakai / Kuota</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($kupon as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $value->kode_kupon }}</td>
                                <td>{{ $value->nama_kupon }}</td>
                                <td>{{ ucfirst($value->tipe_diskon) }}</td>
                                <td>
                                    @if ($value->tipe_diskon == 'persen')
                                        {{ $value->nilai_diskon }}%
                                    @else
                                        Rp. {{ number_format($value->nilai_diskon, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>Rp. {{ number_format($value->min_pembelian, 0, ',', '.') }}</td>
                                <td>
                                    @if ($value->max_diskon > 0)
                                        Rp. {{ number_format($value->max_diskon, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $value->terpakai ?? 0 }} / {{ $value->kuota }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($value->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }}
                                    s/d
                                    {{ \Carbon\Carbon::parse($value->tanggal_akhir)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td>
                                    @if ($value->status_aktif == 'Aktif')
                                        <span class="label label-success">Aktif</span>
                                    @else
                                        <span class="label label-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pemilik_data.edit_kupon', $value->id_kupon) }}"
                                        class="btn btn-xs btn-warning">
                                        <i class="fa fa-fw fa-edit"></i>Edit
                                    </a>
                                    <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                        data-target="#delete{{ $value->id_kupon }}">
                                        <i class="fa fa-fw fa-trash"></i>Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($data_kupon as $value)
        <div class="modal fade" id="delete{{ $value->id_kupon }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Hapus {{ $title ?? 'Data Kupon' }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <h4>Apakah Anda Ingin Menghapus Data&nbsp;<b>{{ $value->nama_kupon }}</b>?</h4>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('pemilik_data.delete_kupon', $value->id_kupon) }}" method="POST">
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
