  @extends('layouts.template')
  @section('content')
      <div class="box">
          <div class="box-header">
              <td> <a href="{{ route('superadmin_data.add_bank') }}" class="btn-sm"><i class="fa fa-plus-circle"
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
                              <th>Nama Bank</th>
                              <th>Pemilik Rekening</th>
                              <th>No Rekening</th>
                              <th scope="col" width="auto">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $no = 1;
          foreach ($bank as $key => $value) {
          ?>
                          <tr>
                              <td><?= $no++ ?></td>
                              <td>{{ $value->nama_bank }}</td>
                              <td>{{ $value->a_n }}</td>
                              <td>{{ $value->no_rek }}</td>
                              <td>
                                  <!-- Link untuk Edit Produk -->
                                  <a href="{{ route('superadmin_data.edit_bank', $value->id_bank) }}"
                                      class="btn btn-xs btn-warning"><i class="fa fa-fw fa-edit"></i>Edit</a>
                                  <!-- Link untuk Delete Produk -->
                                  <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                      data-target="#delete<?= $value['id_bank'] ?>"><i
                                          class="fa fa-fw fa-trash"></i>Delete</button>
                              </td>
                          </tr>
                          <?php } ?>
                      </tbody>
                  </table>
              </div>
          </div>

          <!-- Modal delete-->
          <?php foreach ($bank as $key => $value) { ?>
          <div class="modal fade" id="delete<?= $value['id_bank'] ?>">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title">Delete {{ $title ?? 'Data Bank' }}</h4>
                      </div>
                      <div class="modal-body">
                          <h4>
                              <p>
                                  <center>Apakah Anda Ingin Menghapus Data&nbsp;<?= $value['nama_bank'] ?> ?</center>
                              </p>
                          </h4>
                      </div>

                      <div class="modal-footer">
                          <!-- Form untuk menghapus data dengan metode DELETE -->
                          <form action="{{ route('superadmin_data.delete_bank', $value['id_bank']) }}" method="POST">
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
