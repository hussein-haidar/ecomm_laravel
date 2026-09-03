@extends('layouts.template')
@section('content')
    <div class="box">
        <div class="box-header">
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
                            <th>Tanggal Ulasan</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Produk</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Status Ulasan</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_ulasan as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
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

                                        $timestamp = strtotime($value->tanggal_ulasan);

                                        $tanggal = date('d', $timestamp);
                                        $nama_bulan = $bulan[date('n', $timestamp)];
                                        $tahun = date('Y', $timestamp);
                                        $jam = date('H:i:s', $timestamp);

                                        echo "$tanggal $nama_bulan $tahun, $jam";
                                    @endphp
                                </td>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->nama_produk }}</td>
                                <td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $value->rating)
                                            <i class="fa fa-star text-yellow"></i>
                                        @else
                                            <i class="fa fa-star-o text-yellow"></i>
                                        @endif
                                    @endfor
                                </td>
                                <td>{{ $value->komentar }}</td>
                                <td>
                                    @if ($value->status_ulasan == 'Disetujui')
                                        <span class="label label-success">{{ $value->status_ulasan }}</span>
                                    @elseif ($value->status_ulasan == 'Ditolak')
                                        <span class="label label-danger">{{ $value->status_ulasan }}</span>
                                    @else
                                        <span class="label label-warning">{{ $value->status_ulasan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ url('admin_data/moderasi_ulasan/' . $value->id_ulasan) }}" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}">

                                        <select name="status_ulasan" class="form-control d-inline w-auto" onchange="this.form.submit()" style="display: inline-block; width: auto;">
                                            <option value="Menunggu" {{ $value->status_ulasan == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="Disetujui" {{ $value->status_ulasan == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="Ditolak" {{ $value->status_ulasan == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
