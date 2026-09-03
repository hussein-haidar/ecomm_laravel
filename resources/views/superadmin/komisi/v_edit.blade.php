@extends('layouts.template')  

@section('content')  

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="box">
            <div class="box-header">
            </div>

            <div class="box-body">
                <!-- alert error -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('superadmin_data.update_komisi', ['id_biaya' => $biaya->id_biaya]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Untuk method PUT sesuai dengan RESTful -->

                    <input type="hidden" name="sesi_user" value="{{session('sesi_user') }}" class="form-control" required>

                    <div class="form-group">
                        <label for="persentase">Persentase</label>
                        <input type="text" class="form-control" id="persentase" name="persentase" value="{{ $biaya->persentase }}" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <input type="text" id="" name="deskripsi" value="{{ $biaya->deskripsi }}" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('superadmin_data.komisi') }}" class="btn btn-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

@endsection  