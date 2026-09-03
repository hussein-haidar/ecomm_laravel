@extends('layouts.template')  
@section('content') 

<div class="box">
  <div class="box-header">
    <a href="{{ url('admin_data/view_benefit') }}" class="btn-sm">
      <i class="fa fa-arrow-right"></i>&nbsp;Keuntungan Penjualan
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
                            <th>Foto</th>
                            <th>Toko</th>
                            <th>Pemilik</th>
                            <th>Total Transaksi</th>
                            <th>Total Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
           @foreach ($benefit as $key => $value)
                           <tr>
                               <td>{{ $no++ }}</td>
                            <td>
                                <img src="{{ asset('logowebsite/' . $value->logo_website) }}" class="img-circle"
                                    width="80px" height="80px">
                            </td>
                            <td>{{ $value->nama_toko }}</td>
                            <td>{{ $value->sesi_user }}</td>
                            <td>{{ $value->jumlah_transaksi }}</td>
                            <td>Rp. {{ number_format($value->total_keuntungan_bersih, 0, ',', '.') }}</td>
                           </tr>
           @endforeach
                    </tbody>
                </table>
              </div>
          </div>
      </div>
  @endsection
