@extends('layouts.template')
@section('content')
    <div class="box">
        <div class="box-header">
        </div>

        <div class="box-body">

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">No</th>
                            <th>Kode Produk</th>
                            <th>Jenis Produk</th>
                            <!-- Cek apakah ada varian produk -->
                            @if (isset($data_produk[0]['varian_produk']) && !empty($data_produk[0]['varian_produk']))
                                <th>Varian Produk</th>
                            @endif
                            <th>Nama Produk</th>
                            <th>Ukuran Produk</th>
                            <th>Berat Produk</th>
                            <th>Harga Produk</th>
                            <th>Foto Produk</th>
                            <th>Deskripsi Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_produk as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $value['kode_produk'] }}</td>
                                <td>{{ $value['jenis_produk'] }}</td>
                                @if (!empty($value['varian_produk']))
                                    <td>{{ $value['varian_produk'] }}</td>
                                @endif
                                <td>{{ $value['nama_produk'] }}</td>
                                <td>{{ $value['ukuran_produk'] }}</td>
                                <td>{{ $value['berat_produk'] }} {{ $value['satuan_berat'] }}</td>
                                <td>Rp. {{ number_format($value['harga_produk'], 0, ',', '.') }}</td>
                                <td>
                                    <img src="{{ asset('fotoproduk/' . $value['foto_produk']) }}" class="img-circle"
                                        width="80px" height="80px">
                                </td>
                                <td>{{ $value['deskripsi_produk'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
