  @extends('layouts.template')
  @section('content')

      <div class="row">
          <div class="col-md-3">
          </div>
          <div class="col-md-6">
              <div class="box">
                  <div class="box-header">
                  </div>

                  <!-- /.box-header -->
                  <div class="box-body">

                      <!-- pop up alert wrong-->
                      @if (session('errors'))
                          <div class="alert alert-danger" role="alert">
                              <ul>
                                  @foreach (session('errors')->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                          </div>
                      @endif

                      <form action="{{ route('superadmin_data.update_bank', $bank->id_bank) }}" method="POST"
                          enctype="multipart/form-data">
                          @csrf

                          <div class="form-group">
                              <label>Sesi User</label>
                              <input type="text" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                                  readonly>
                          </div>

                          <div class="form-group">
                              <label for="produk">Nama Bank:</label>
                              <input type="text" class="form-control" name="nama_bank" id="nama_bank"
                                  value="{{ $bank->nama_bank }}" required>
                          </div>

                          <div class="form-group">
                              <label for="harga">Pemilik Rekening:</label>
                              <input type="text" class="form-control" name="a_n" id="a_n"
                                  value="{{ $bank->a_n }}" required>
                          </div>

                          <div class="form-group">
                              <label for="harga">No Rekening:</label>
                              <input type="number" class="form-control" name="no_rek" id="no_rek"
                                  value="{{ $bank->no_rek }}" required>
                          </div>

                          <a href="{{ route('superadmin_data.bank') }}" class="btn btn-primary">Kembali</a>
                          <button type="submit" class="btn btn-success">Simpan</button>
                      </form>

                  </div>
              </div>

          </div>
          <div class="col-md-3">
          </div>

      @endsection
