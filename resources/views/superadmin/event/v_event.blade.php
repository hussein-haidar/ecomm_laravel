  @extends('layouts.template')
  @section('content')
      <div class="box">
          <div class="box-header">
              <td> <a href="{{ route('superadmin_data.add_event') }}" class="btn-sm"><i class="fa fa-plus-circle"
                          aria-hidden="true"></i>
                      Tambah Data</a></td>
          </div>

          <div class="box-body">
              <!-- alert success add data -->
              @if (session('success'))
                  <div class="alert alert-success">
                      {{ session('success') }}
                  </div>
              @endif

              <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped ">
                      <thead>
                          <tr>
                              <th scope="col" width="1%">No</th>
                              <th>Nama Promo</th>
                              <th>Persentase</th>
                              <th>Deskripsi</th>
                              <th>Waktu Mulai Event</th>
                              <th>Waktu Berakhir Event</th>
                              <th>Status Event</th>
                              <th>Gambar Event</th>
                                 <th>Gambar Event2</th>
                                    <th>Gambar Event3</th>
                              <th scope="col" width="auto">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $no = 1;
          foreach ($event as $key => $value) {
          ?>
                          <tr>
                              <td><?= $no++ ?></td>
                              <td>{{ $value->nama_promo }}</td>
                              <td>{{ $value->persentase }}</td>
                              <td>{{ $value->deskripsi }}</td>
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

                                      $timestamp = strtotime($value->waktu_mulai);

                                      $tanggal = date('d', $timestamp);
                                      $nama_bulan = $bulan[date('n', $timestamp)]; // n = 1-12
                                      $tahun = date('Y', $timestamp);
                                      $jam = date('H:i:s', $timestamp); // Jam:Menit:Detik

                                      echo "$tanggal $nama_bulan $tahun, $jam";
                                  @endphp
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

                                      $timestamp = strtotime($value->waktu_berakhir);

                                      $tanggal = date('d', $timestamp);
                                      $nama_bulan = $bulan[date('n', $timestamp)]; // n = 1-12
                                      $tahun = date('Y', $timestamp);
                                      $jam = date('H:i:s', $timestamp); // Jam:Menit:Detik

                                      echo "$tanggal $nama_bulan $tahun, $jam";
                                  @endphp
                              </td>
                              <td>
                                  <form method="POST" action="{{ route('superadmin_data.konfirmStatusEvent_By_Sesi') }}"
                                      class="d-inline">
                                      @csrf

                                      <input type="hidden" name="id_promo"
                                          value="{{ $value->id_promo ?? $value['id_promo'] }}">

                                      <!-- Tentukan status baru: jika sekarang Aktif → kirim Non-aktif, dan sebaliknya -->
                                      @php
                                          $currentStatus = $value->status_event ?? $value['status_event'];
                                          $newStatus = $currentStatus == 'Aktif' ? 'Non-aktif' : 'Aktif';
                                      @endphp
                                      <input type="hidden" name="status_event" value="{{ $newStatus }}">

                                      <button type="submit" class="btn btn-xs"
                                          style="cursor: pointer;
                    background-color: {{ $currentStatus == 'Aktif' ? '#5cb85c' : '#d9534f' }};
                    color: white;
                    border: none;
                    padding: 5px 10px;
                    margin-right: 8px;">
                                          {{ $currentStatus == 'Aktif' ? 'Aktif' : 'Non-aktif' }}
                                      </button>
                                  </form>
                              </td>
                              <td>
                                  <img src="{{ asset('gambarevent/' . $value['gambar_event']) }}" class="img-circle"
                                      width="80px" height="80px">
                              </td>
                                  <td>
                                  <img src="{{ asset('gambarevent/' . $value['gambar_event2']) }}" class="img-circle"
                                      width="80px" height="80px">
                              </td>
                                  <td>
                                  <img src="{{ asset('gambarevent/' . $value['gambar_event3']) }}" class="img-circle"
                                      width="80px" height="80px">
                              </td>
                              <td>
                                  <!-- Link untuk Edit -->
                                  <a href="{{ route('superadmin_data.edit_event', $value->id_promo) }}"
                                      class="btn btn-xs btn-warning"><i class="fa fa-fw fa-edit"></i>Edit</a>
                                  <!-- Link untuk Delete -->
                                  <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                      data-target="#delete<?= $value['id_promo'] ?>"><i
                                          class="fa fa-fw fa-trash"></i>Delete</button>
                              </td>
                          </tr>
                          <?php } ?>
                      </tbody>
                  </table>
              </div>
          </div>

          <!-- Modal delete-->
          <?php foreach ($event as $key => $value) { ?>
          <div class="modal fade" id="delete<?= $value['id_promo'] ?>">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Delete {{ $title ?? 'Data event' }}</h4>
                      </div>
                      <div class="modal-body">
                          <h4>
                              <p>
                                  <center>Apakah Anda Ingin Menghapus Data&nbsp;<?= $value['nama_promo'] ?> ?</center>
                              </p>
                          </h4>
                      </div>

                      <div class="modal-footer">
                          <!-- Form untuk menghapus data dengan metode DELETE -->
                          <form action="{{ route('superadmin_data.delete_event', $value['id_promo']) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-success pull-left btn-flat">Delete</button>
                          </form>
                          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Close</button>
                      </div>
                  </div>
                  <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
          </div>
          <?php } ?>
      @endsection
