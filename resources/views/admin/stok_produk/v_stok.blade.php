@extends('layouts.template')
@section('content')
    <div class="box">
        <div class="box-header">
            <td>
                <a href="{{ url('admin_data/add_stok') }}" class="btn-sm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
                </a>
            </td>
        </div>

        <div class="box-body">
            {{-- Alert Success Add --}}
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
                            <th>Kode Stok Produk</th>
                            <th>Barcode</th>
                            <th>Tanggal Masuk</th>
                            <th>Nama Produk</th>
                            <th>Foto Produk</th>
                            <th>Jenis Produk</th>
                            <th>Harga Produk Semula</th>
                            <th>Harga Produk Sekarang</th>
                            <th>Jumlah Stok Produk</th>
                            <th>Ukuran Produk</th>
                            <th>Total Harga Produk</th>
                            <th>Total Berat Produk</th>
                            <th>Kebijakan Retur</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_stok as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $value->kode_stok }}</td>
                                <td>
                                    @php
                                        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                        $barcodeData = base64_encode($generator->getBarcode($value->kode_stok, $generator::TYPE_CODE_128));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $barcodeData }}" width="120" height="50"
                                        alt="Barcode {{ $value->kode_stok }}">
                                </td>
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

                                        $timestamp = strtotime($value->tanggal_masuk_produk);

                                        $tanggal = date('d', $timestamp);
                                        $nama_bulan = $bulan[date('n', $timestamp)]; // n = 1-12
                                        $tahun = date('Y', $timestamp);
                                        $jam = date('H:i:s', $timestamp); // Jam:Menit:Detik

                                        echo "$tanggal $nama_bulan $tahun, $jam";
                                    @endphp
                                </td>
                                <td>{{ $value->nama_produk }}</td>
                                <td><img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle"
                                        width="80px" height="80px"></td>
                                <td>{{ $value->jenis_produk }}</td>
                                <td>Rp. {{ number_format($value->harga_produk, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($value->harga_produk_new, 0, ',', '.') }}</td>
                                <td>{{ $value->jumlah_stok_produk }} {{ $value->satuan_produk }}</td>
                                <td>{{ $value->ukuran_produk }}</td>

                                <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                                <td>{{ $value->total_berat }} {{ $value->satuan_berat }}</td>
                                <td>
                                    @if (!empty($value->boleh_retur) && (int) $value->boleh_retur === 1)
                                        <span class="badge bg-green">Boleh Retur</span>
                                    @else
                                        <span class="badge bg-red">Tidak Boleh Retur</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('admin_data/edit_stok/' . $value->id_stok) }}"
                                        class="btn btn-xs btn-warning">
                                        <i class="fa fa-fw fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                        data-target="#delete{{ $value->id_stok }}">
                                        <i class="fa fa-fw fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal delete --}}
    @foreach ($data_stok as $value)
        <div class="modal fade" id="delete{{ $value->id_stok }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete {{ $title ?? 'Stok Produk' }}</h4>
                    </div>
                    <div class="modal-body">
                        <h4>
                            <p class="text-center">Apakah Anda Ingin Menghapus Data {{ $value->nama_produk }}?</p>
                        </h4>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ url('admin_data/delete_stok/' . $value->id_stok) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-success btn-flat pull-left">Delete</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
