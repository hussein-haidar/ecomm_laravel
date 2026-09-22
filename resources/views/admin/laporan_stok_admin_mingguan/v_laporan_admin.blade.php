@extends('layouts.template')  
@section('content') 

        <div class="box">
            <div class="box-header">
            <form method="GET" action="{{ route('admin_laporan_mingguan.filterStokByDate') }}">
                @csrf
                <div class="row">
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}" required>
                        </div>
                    </div>
    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}" required>
                        </div>
                    </div>
    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">Filter</button>
                        </div>
                    </div>

                </div>
    
                @if ($start_date && $end_date)
                    <a href="{{ url('admin_laporan_mingguan/cetakLaporanStok') }}?start_date={{ $start_date }}&end_date={{ $end_date }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="fa fa-print" aria-hidden="true"></i> Cetak Data
                    </a>
                    <a href="{{ url('admin_laporan_mingguan/exportExcel') }}?start_date={{ $start_date }}&end_date={{ $end_date }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
                    </a>
                    <a href="{{ url('admin_laporan_mingguan/resetFilterStok') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-refresh" aria-hidden="true"></i> Reset Filter
                    </a>
                @endif
            </form>    
    
    <div class="table-responsive">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col" width="1%">No</th>
                    <th>Kode Stok Produk</th>
                    <th>Tanggal Masuk</th>
                    <th>Nama Produk</th>
                    <th>Foto Produk</th>
                    <th>Jenis Produk</th>
                    <th>Jumlah Stok Produk</th>
                    <th>Ukuran Produk</th>
                    <th>Total Harga Produk</th>
                    <th>Total Berat Produk</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($data_laporan_admin as $value)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $value->kode_stok }}</td>
                    <td>
                      @php
                        $bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        $tanggal = strtotime($value->tanggal_masuk_produk);
                        echo date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
                      @endphp
                    </td>
                    <td>{{ $value->nama_produk }}</td>
                    <td><img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle" width="80px" height="80px"></td>
                    <td>{{ $value->jenis_produk }}</td>
                    <td>{{ $value->jumlah_stok_produk }} {{ $value->satuan_produk }}</td>
                    <td>{{ $value->ukuran_produk }}</td>
                    <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $value->total_berat }} {{ $value->satuan_berat }}</td>
                  </tr>
                @endforeach
            </tbody>
        </table>
    
            </div>
        </div>

        </div>
</div>

@endsection  