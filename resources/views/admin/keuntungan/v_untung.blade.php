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
                              <th>Foto</th>
                              <th>Toko</th>
                              <th>Pemilik</th>
                              <th>Tanggal Keuntungan</th>
                              <th>Nilai Keuntungan Sebelum Potongan</th>
                              <th>Biaya Platform</th>
                              <th>Potongan Biaya</th>
                              <th>Nilai Keuntungan Sekarang</th>
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

                                      $timestamp = strtotime($value->waktu_pembayaran);

                                      $tanggal = date('d', $timestamp);
                                      $nama_bulan = $bulan[date('n', $timestamp)]; // n = 1-12
                                      $tahun = date('Y', $timestamp);
                                      $jam = date('H:i:s', $timestamp); // Jam:Menit:Detik

                                      echo "$tanggal $nama_bulan $tahun, $jam";
                                  @endphp
                              </td>
                              <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                              <td>{{ $value->biaya_platform }}%</td>
                              <td>Rp. {{ number_format($value->potongan_biaya, 0, ',', '.') }}</td>
                              <td>Rp. {{ number_format($value->total_harga_new, 0, ',', '.') }}</td>
                           </tr>
                       @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  @endsection
