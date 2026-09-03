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

                      <form form_open_multipart action="{{ route('pemilik_data.save_user') }}" method="POST"
                          enctype="multipart/form-data">
                          @csrf

                          <div class="form-group">
                              <label for="username">Username:</label>
                              <input type="text" class="form-control" name="username" id="username" required>
                          </div>

                          <div class="form-group">
                              <label for="fullname">Fullname:</label>
                              <input type="text" class="form-control" name="fullname" id="fullname" required>
                          </div>

                          <div class="form-group">
                              <label>Sesi User</label>
                              <input type="text" name="sesi_user" value="{{ $sesi_user }}" class="form-control"
                                  readonly>
                          </div>

                          <div class="form-group">
                              <label for="nama_title">Nama Title:</label>
                              <input type="text" class="form-control" name="nama_title" id="nama_title" required>
                          </div>

                          <div class="form-group">
                              <label for="password">Password:</label>
                              <input type="text" class="form-control" name="password" id="password" required>
                          </div>

                          <div class="form-group">
                              <label>Level User</label>
                              <select id="level_user" name="level" class="form-control">
                                  <option value="">--Pilih Level--</option>
                                  <option value="pemilik">Pemilik</option>
                                  <option value="admin">Admin</option>
                              </select>
                          </div>

                          <div class="form-group">
                              <label>Pilih Foto User</label>
                              <input type="file" class="form-control" name="foto_user" id="preview_gambar">
                          </div>

                          <a href="{{ route('pemilik_data.user') }}" class="btn btn-primary">Kembali</a>
                          <button type="submit" class="btn btn-success">Simpan</button>
                      </form>

                  </div>
              </div>
          </div>

          <div class="col-md-3">
          </div>
      @endsection
