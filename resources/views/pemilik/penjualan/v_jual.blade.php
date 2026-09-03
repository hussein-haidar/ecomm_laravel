  @extends('layouts.template')
  @section('content')
  
      <div class="box">
          <div class="box-header">
              <!-- Header kosong -->
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
                              <th>Kode Transaksi</th>
                              <th>Waktu Transaksi</th>
                              <th>Produk</th>
                              <th>Foto Produk</th>
                              <th>Jumlah Produk</th>
                              <th>Total Harga</th>
                              <th>Total Berat</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $no = 1;
        foreach ($pembelian as $key => $value) {
        ?>
                          <tr>
                              <td><?= $no++ ?></td>
                              <td>{{ $value->kode_beli }}</td>
                              <td>
                                  {{ \Carbon\Carbon::parse($value->waktu_pembelian)->locale('id')->translatedFormat('d F Y H:i:s') }}
                              </td>
                              <td>{{ $value->nama_produk }}</td>
                              <td>
                                  <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle"
                                      width="80px" height="80px">
                              </td>
                     <td>{{ $value->jumlah_produk }} {{ $value->satuan_produk }}</td>
                              <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                              <td>{{ $value->total_berat }}</td>
                          </tr>
                          <?php } ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  @endsection
