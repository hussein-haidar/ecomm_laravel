@extends('layouts.template')  
@section('content') 

<div class="box">
  <div class="box-header">
    <a href="{{ url('admin_data/stok') }}" class="btn-sm">
      <i class="fa fa-arrow-right"></i>&nbsp;Stok Produk
    </a>
  </div>

  <!-- /.box-header -->
  <div class="box-body">
    <!-- alert success add data -->
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
            <th>Nama Produk</th>
            <th>Foto Produk</th>
            <th>Total Stok Produk</th>
            <th>Total Harga Stok</th>
          </tr>
        </thead>
        <tbody>
          @php $no = 1; @endphp
          @foreach ($data_stok as $value)
            <tr>
              <td>{{ $no++ }}</td>
              <td>{{ $value->nama_produk }}</td>
              <td>
                <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle" width="80px" height="80px">
              </td>
              <td>
                {{ $value->total_stok }} {{ $value->satuan_produk }}
              </td>
              <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection  
