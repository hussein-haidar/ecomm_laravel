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

                      <form action="{{ route('pemilik_data.update_satuan', $satuan->id_satuan) }}" method="POST"
                          enctype="multipart/form-data">
                          @csrf

                          <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                              readonly>

                          <div class="form-group">
                              <label for="name">satuan Produk:</label>
                              <input type="text" class="form-control" name="satuan_produk" id="satuan_produk"
                                  value="{{ $satuan->satuan_produk }}" required>
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
