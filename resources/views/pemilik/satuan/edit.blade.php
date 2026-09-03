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

                      <form action="{{ route('pemilik_data.update_satuan', $satuan_produk->id_satuan) }}" method="POST"
                          enctype="multipart/form-data">
                          @csrf

                          <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                              readonly>

                          <div class="form-group">
                              <label for="name">Satuan Produk:</label>
                              <input type="text" class="form-control" name="satuan_produk" id="satuan_produk"
                                  value="{{ $satuan_produk->satuan_produk }}" required>
                          </div>
                          <div class="form-group">
                              <label for="jenis_satuan">Jenis Satuan:</label>
                              <select class="form-control" name="jenis_satuan" id="jenis_satuan" required>
                                  <option value="">Pilih Jenis Satuan</option>
                                  <option value="produk" {{ $satuan_produk->jenis_satuan == 'produk' ? 'selected' : '' }}>
                                      Produk</option>
                                  <option value="berat" {{ $satuan_produk->jenis_satuan == 'berat' ? 'selected' : '' }}>
                                      Berat</option>
                              </select>
                          </div>

                          <a href="{{ route('pemilik_data.satuan') }}" class="btn btn-primary">Kembali</a>
                          <button type="submit" class="btn btn-success">Simpan</button>
                      </form>

                  </div>
              </div>

          </div>
          <div class="col-md-3">
          </div>

      @endsection
