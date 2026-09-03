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

                    <form action="{{ route('superadmin_data.save_komisi') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                            readonly>

                        <div class="form-group">
                            <label for="persentase">Persentase</label>
                            <input type="text" step="0.01" class="form-control" id="persentase" name="persentase" placeholder="Masukkan Persentase" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" id="deskripsi" name="deskripsi" value="" class="form-control" placeholder="Masukkan Deskripsi"
                                required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ url('superadmin_data/komisi') }}" class="btn btn-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>

@endsection
