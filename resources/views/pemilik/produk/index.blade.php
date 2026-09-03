@extends('layouts.template') {{-- Sesuaikan layout sesuai struktur aplikasi Laravel Anda --}}

@section('content')
    <div class="box">
        <div class="box-header">
            <a href="{{ route('pemilik_data.add_produk') }}" class="btn btn-sm">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
            </a>
            <a href="{{ route('pemilik_data.data_dihapus_produk') }}" class="btn btn-sm">Lihat Data Dihapus</a>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <!-- Alert Success -->
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
                            <th>Kode Produk</th>
                            <th>Barcode</th>
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
                            <th>Pilih Carousel</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_produk as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $value['kode_produk'] }}</td>
                                <td>
                                    @php
                                        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                        $barcodeData = base64_encode($generator->getBarcode($value['kode_produk'], $generator::TYPE_CODE_128));
                                    @endphp
                                    <img src="data:image/png;base64,{{ $barcodeData }}" width="120" height="50"
                                        alt="Barcode {{ $value['kode_produk'] }}">
                                </td>
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
                                <td>
                                    <form action="{{ route('pemilik_data.update_carousel', $value['id_produk']) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <input type="checkbox" name="carousel" value="1" onchange="this.form.submit()"
                                            {{ $value['carousel'] == 1 ? 'checked' : '' }}>
                                    </form>
                                </td>
                                <td>
                                    @if ($value['deleted_at'] == 0)
                                        <!-- Tombol Edit dan Delete -->
                                        <a href="{{ route('pemilik_data.edit_produk', $value['id_produk']) }}"
                                            class="btn btn-xs btn-warning">
                                            <i class="fa fa-fw fa-edit"></i>Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                            data-target="#delete{{ $value['id_produk'] }}">
                                            <i class="fa fa-fw fa-trash"></i>Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    @foreach ($data_produk as $value)
        <div class="modal fade" id="delete{{ $value['id_produk'] }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Hapus {{ $title ?? 'Data Produk' }}</h4>
                    </div>
                    <div class="modal-body text-center">
                        <h4>Apakah Anda Ingin Menghapus Data&nbsp;<b>{{ $value['nama_produk'] }}</b>?</h4>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('pemilik_data.delete_produk', $value['id_produk']) }}" method="POST">
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
