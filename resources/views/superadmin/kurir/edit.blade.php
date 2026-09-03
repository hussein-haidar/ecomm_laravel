@extends('layouts.template')

@section('content')

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">

                </div>

                <div class="box-body">
                    <!-- Display errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                <form action="{{ route('superadmin_data.update_kurir', $data_kurir->id_kurir) }}" method="POST" enctype="multipart/form-data">  
                    @csrf  

                    <input type="hidden" name="sesi_user" value="{{session('sesi_user') }}" class="form-control" readonly>

                    <div class="form-group mb-3">
                        <label for="jenis_kurir" class="form-label">Jenis Kurir</label>
                        <input type="text" name="jenis_kurir" id="jenis_kurir" class="form-control"
                               value="{{ $data_kurir->jenis_kurir }}"
                               placeholder="Masukkan Jenis Kurir" required>
                    </div>

                      <div class="form-group mb-3">
                        <label for="tipe_kurir" class="form-label">Tipe Kurir</label>
                       <select id="tipe_kurir" name="tipe_kurir" class="form-control">
                            <option value="">--Pilih Tipe--</option>
                            @foreach ($data_kurir as $value)
                                <option value="{{ $value->data_kurir }}" {{  $data_kurir->tipe_kurir == $value->tipe_kurir ? 'selected' : '' }}>
                                    {{ $value->tipe_kurir }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ongkir" class="form-label">Ongkir</label>
                        <input type="number" name="ongkir" id="ongkir" class="form-control"
                               value="{{ $data_kurir->ongkir }}"
                               placeholder="Masukkan Ongkir" required>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('superadmin_data.kurir') }}" class="btn btn-primary">Kembali</a>
                    </div>
                </form>
      </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>
@endsection