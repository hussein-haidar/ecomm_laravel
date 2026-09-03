  @extends('layouts.template')

  @section('content')
      <div class="box">
          <div class="box-header">
              <td>
                  <a href="{{ route('superadmin_data.add_website') }}" class="btn-sm btn-primary">
                      <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
                  </a>
              </td>
          </div>

          <div class="box-body">
              <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Nama Platform</th>
                              <th>Event Promo</th>
                              <th>Alamat Platform</th>
                              <th>No Telpon Platform</th>
                              <th>Logo Platform</th>
                              <th>Background Platform</th>
                              <th>Status Platform</th>
                              <th>Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($website as $key => $value)
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $value['nama_toko'] }}</td>
                                  <td>{{ $value['nama_promo'] }}</td>
                                  <td>{{ $value['alamat_pusat'] }}</td>
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
                                      <form method="POST" action="{{ route('superadmin_data.konfirmStatusWeb_By_Sesi') }}"
                                          class="d-inline">
                                          @csrf

                                          <input type="hidden" name="id_website"
                                              value="{{ $value->id_website ?? $value['id_website'] }}">

                                          <select name="status_website" class="form-control form-control-sm"
                                              onchange="this.form.submit()"
                                              style="display: inline-block; width: auto; background-color: {{ ($value->status_website ?? $value['status_website']) == 'Aktif' ? '#5cb85c' : '#d9534f' }}; color: white; border: none; padding: 2px 8px;">

                                              <option value="Aktif"
                                                  {{ ($value->status_website ?? $value['status_website']) == 'Aktif' ? 'selected' : '' }}>
                                                  Aktif
                                              </option>
                                              <option value="Non-aktif"
                                                  {{ ($value->status_website ?? $value['status_website']) == 'Non-aktif' ? 'selected' : '' }}>
                                                  Non-aktif
                                              </option>
                                          </select>
                                      </form>
                                  </td>
                                  <td>
                                      <!-- Tombol status dengan warna berdasarkan nilai terbaru -->
                                      <a href="{{ route('superadmin_data.edit_website', $value['id_website']) }}"
                                          class="btn btn-xs btn-warning">
                                          <i class="fa fa-fw fa-edit"></i> Edit
                                      </a>

                                      <!-- Tombol Delete dengan SweetAlert -->
                                      <button class="btn btn-danger btn-sm btn-xs btn-delete"
                                          data-id="{{ $value['id_website'] }}">
                                          <i class="fa fa-fw fa-trash"></i> Delete
                                      </button>

                                      <!-- Form Delete -->
                                      <form id="delete-form-{{ $value['id_website'] }}"
                                          action="{{ route('superadmin_data.delete_website', $value['id_website']) }}"
                                          method="POST" style="display: none;">
                                          @csrf
                                          @method('DELETE')
                                      </form>
                                  </td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  @endsection
