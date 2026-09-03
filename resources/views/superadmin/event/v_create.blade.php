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

                      <!-- Popup Alert for Errors -->
                      @if ($errors->any())
                          <div class="alert alert-danger" role="alert">
                              <ul>
                                  @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                          </div>
                      @endif

                      <form action="{{ route('superadmin_data.save_event') }}" method="POST" enctype="multipart/form-data">
                          @csrf

                          <div class="form-group">
                              <label>Sesi User</label>
                              <input type="text" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                                  readonly>
                          </div>

                          <div class="form-group">
                              <label for="nama_promo">Nama Promo:</label>
                              <input type="text" class="form-control" name="nama_promo" id="nama_promo"
                                  placeholder="Nama Promo" required>
                          </div>

                          <div class="form-group">
                              <label for="persentase">Persentase:</label>
                              <input type="text" class="form-control" name="persentase" id="persentase"
                                  placeholder="Masukkan Persentase" required>
                          </div>

                          <div class="form-group">
                              <label for="deskripsi">Deskripsi:</label>
                              <input type="text" class="form-control" name="deskripsi" id="deskripsi" required>
                          </div>

                          <!-- Input Waktu Mulai -->
                          <div class="form-group">
                              <label for="waktu_mulai">Waktu Mulai Event:</label>
                              <input type="datetime-local" class="form-control" name="waktu_mulai" id="waktu_mulai"
                                  required>
                          </div>

                          <!-- Input Waktu Berakhir -->
                          <div class="form-group">
                              <label for="waktu_berakhir">Waktu Berakhir Event:</label>
                              <input type="datetime-local" class="form-control" name="waktu_berakhir" id="waktu_berakhir"
                                  required>
                          </div>

                          <div class="form-group">
                              <label for="status_event">Status Event:</label>
                              <select class="form-control" name="status_event" id="status_event" required>
                                  <option value="" disabled selected>Pilih status</option>
                                  <option value="Aktif">Aktif</option>
                                  <option value="Nonaktif">Nonaktif</option>
                              </select>
                          </div>

                          <div class="form-group">
                              <label for="gambar_event">Unggah Gambar Event (Opsional)</label>
                              <input type="file" name="gambar_event" id="event_input" class="form-control">
                              <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                          </div>

                             <div class="form-group">
                              <label for="gambar_event">Unggah Gambar Event2 (Opsional)</label>
                              <input type="file" name="gambar_event2" id="event_input2" class="form-control">
                              <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                          </div>

                             <div class="form-group">
                              <label for="gambar_event">Unggah Gambar Event3 (Opsional)</label>
                              <input type="file" name="gambar_event3" id="event_input3" class="form-control">
                              <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                          </div>

                          <a href="{{ route('superadmin_data.event') }}" class="btn btn-primary">Kembali</a>
                          <button type="submit" class="btn btn-success">Simpan</button>
                      </form>

                  </div>
              </div>

          </div>
          <div class="col-md-3">
          </div>

      @endsection

      <script>
          document.getElementById('waktu_berakhir').addEventListener('change', function() {
              const mulai = new Date(document.getElementById('waktu_mulai').value);
              const berakhir = new Date(this.value);

              if (berakhir < mulai) {
                  alert('Waktu berakhir tidak boleh lebih awal dari waktu mulai!');
                  this.value = '';
              }
          });
      </script>
