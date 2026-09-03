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

                      <form action="{{ route('superadmin_data.update_event', $event->id_promo) }}" method="POST"
                          enctype="multipart/form-data">
                          @csrf

                          <div class="form-group">
                              <label>Sesi User</label>
                              <input type="text" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                                  readonly>
                          </div>

                          <div class="form-group">
                              <label for="nama_promo">Nama Promo:</label>
                              <input type="text" class="form-control" name="nama_promo" id="nama_promo"
                                  value="{{ $event->nama_promo }}" required>
                          </div>

                          <div class="form-group">
                              <label for="persentase">Persentase:</label>
                              <input type="text" class="form-control" name="persentase" id="persentase"
                                  value="{{ $event->persentase }}" required>
                          </div>

                          <div class="form-group">
                              <label for="deskripsi">Deskripsi:</label>
                              <input type="deskripsi" class="form-control" name="deskripsi" id="deskripsi"
                                  value="{{ $event->deskripsi }}" required>
                          </div>

                          <!-- Input Waktu Mulai -->
                          <div class="form-group">
                              <label for="waktu_mulai">Waktu Mulai Event:</label>
                              <input type="datetime-local" class="form-control" name="waktu_mulai" id="waktu_mulai"
                                  value="{{ $event->waktu_mulai }}" required>
                          </div>

                          <!-- Input Waktu Berakhir -->
                          <div class="form-group">
                              <label for="waktu_berakhir">Waktu Berakhir Event:</label>
                              <input type="datetime-local" class="form-control" name="waktu_berakhir" id="waktu_berakhir"
                                  value="{{ $event->waktu_berakhir }}" required>
                          </div>

                          <!-- Logo Preview 1 -->
                          <div class="form-group">
                              <label>Gambar Event Terkini</label>
                              <p></p>
                              @if ($event->gambar_event)
                                  <img src="{{ asset('gambarevent/' . $event->gambar_event) }}" id="event_preview"
                                      width="100px">
                              @else
                                  <span>Tidak ada gambar</span>
                              @endif
                          </div>

                          <div class="form-group">
                              <label for="gambar_event">Ganti Gambar Event (Opsional)</label>
                              <input type="file" name="gambar_event" id="input_event" class="form-control">
                              <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                          </div>

                          <!-- Logo Preview 2 -->
                          <div class="form-group">
                              <label>Gambar Event Terkini2</label>
                              <p></p>
                              @if ($event->gambar_event2)
                                  <img src="{{ asset('gambarevent/' . $event->gambar_event2) }}" id="event_preview2"
                                      width="100px">
                              @else
                                  <span>Tidak ada gambar</span>
                              @endif
                          </div>

                          <div class="form-group">
                              <label for="gambar_event2">Ganti Gambar Event (Opsional)</label>
                              <input type="file" name="gambar_event2" id="input_event2" class="form-control">
                              <!-- Perbaiki id dan name -->
                              <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                          </div>

                          <!-- Logo Preview 3 -->
                          <div class="form-group">
                              <label>Gambar Event Terkini3</label>
                              <p></p>
                              @if ($event->gambar_event3)
                                  <img src="{{ asset('gambarevent/' . $event->gambar_event3) }}" id="event_preview3"
                                      width="100px">
                              @else
                                  <span>Tidak ada gambar</span>
                              @endif
                          </div>

                          <div class="form-group">
                              <label for="gambar_event3">Ganti Gambar Event (Opsional)</label>
                              <input type="file" name="gambar_event3" id="input_event3" class="form-control">
                              <!-- Perbaiki name -->
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

          <script>
              document.addEventListener('DOMContentLoaded', function() {
                  function previewImage(inputId, imgId) {
                      const input = document.getElementById(inputId);
                      const img = document.getElementById(imgId);

                      if (!input || !img) {
                          console.error(`Input or image element not found: ${inputId}, ${imgId}`);
                          return;
                      }

                      input.addEventListener('change', function() {
                          const file = this.files[0];
                          if (file) {
                              const reader = new FileReader();
                              reader.onload = function(e) {
                                  img.src = e.target.result;
                              };
                              reader.readAsDataURL(file);
                          }
                      });
                  }

                  // Panggil dengan ID yang benar
                  previewImage('input_event', 'event_preview');
                  previewImage('input_event2', 'event_preview2');
                  previewImage('input_event3', 'event_preview3');
              });
          </script>
      @endsection
