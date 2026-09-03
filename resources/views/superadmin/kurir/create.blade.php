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

                <form action="{{ route('superadmin_data.save_kurir') }}" method="POST"> <!-- Ganti route sesuai -->
                    @csrf

                    <input type="hidden" name="sesi_user" value="{{session('sesi_user') }}" class="form-control" readonly>

                    <div class="form-group mb-3">
                        <label for="jenis_kurir" class="form-label">Jenis Kurir</label>
                        <input type="text" name="jenis_kurir" class="form-control" id="jenis_kurir"
                               placeholder="Masukkan Jenis Kurir" required>
                    </div>

                       <div class="form-group mb-3">
                        <label for="tipe_kurir" class="form-label">Tipe Kurir</label>
                       <select name="tipe_kurir" class="form-control" required>
                            <option value="" disabled selected>Pilih Tipe</option>
                           <option value="instan" selected>Instan</option>
                              <option value="reguler" selected>Reguler</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ongkir" class="form-label">Ongkir</label>
                        <input type="number" name="ongkir" class="form-control" id="ongkir"
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