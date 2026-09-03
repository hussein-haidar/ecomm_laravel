  @extends('layouts.template')

  @section('content')
      <div class="box">
          <div class="box-header">

          </div>

          <div class="box-body">
              <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Nama Toko</th>
                              <th>Pemilik</th>
                              <th>No Telpon Platform</th>
                              <th>Logo Website</th>
                              <th>Background Website</th>
                              <th>Status Website</th>
                          </tr>
                      </thead>
                      <tbody>
                      
                          @foreach ($website as $key => $value)
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $value['nama_toko'] }}</td>
                            <td>{{ $value->sesi_user }}</td>
                                  <td>{{ $value['wa_pusat'] }}</td>
                                  <td>
                                      <img src="{{ asset('logowebsite/' . $value['logo_website']) }}" class="img-circle"
                                          width="80px" height="80px">
                                  </td>
                                  <td>
                                      <img src="{{ asset('bgdweb/' . $value['bgd_web']) }}" class="img-circle"
                                          width="80px" height="80px">
                                  </td>
                                  <td>
                                      <form method="POST" action="{{ route('superadmin_data.konfirmStatusWebAll') }}"
                                          class="d-inline">
                                          @csrf
                                          @method('POST')

                                          <input type="hidden" name="id_website" value="{{ $value->id_website }}">

                                          <select name="status_website" class="form-control d-inline w-auto"
                                              onchange="this.form.submit()"
                                              style="display: inline-block; width: auto;
                    @if ($value->status_website == 'Aktif') background-color: #5cb85c; color: white;
                    @elseif($value->status_website == 'Non-aktif') background-color: #d9534f; color: white;
                    @else background-color: #f0ad4e; color: white; @endif">
                                              <option value="Aktif"
                                                  {{ $value->status_website == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                              <option value="Non-aktif"
                                                  {{ $value->status_website == 'Non-aktif' ? 'selected' : '' }}>Non-aktif
                                              </option>
                                          </select>

                                          <div id="alasan-div-{{ $value->id_website }}"
                                              style="margin-top: 5px; display: {{ $value->status_website == 'Non-aktif' ? 'block' : 'none' }};">
                                              <input type="text" name="alasan_nonaktif" class="form-control mt-2"
                                                  placeholder="Masukkan alasan nonaktif"
                                                  value="{{ old('alasan_nonaktif', $value->alasan_nonaktif) }}">
                                              <button type="submit" class="btn btn-danger btn-sm mt-1">Kirim</button>
                                      </form>
                                  </td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
              </div>
      </div>

        <!-- Script Toggle Alasan -->
  <script>
      function toggleAlasan(select, id) {
          const div = document.getElementById('alasan-div-' + id);
          if (select.value === 'Non-aktif') {
              div.style.display = 'block';
          } else {
              div.style.display = 'none';
              select.form.submit(); // Submit otomatis jika bukan dibatalkan
          }
      }
  </script>
  
  @endsection


