@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $title }}</h3>
                </div>
                <div class="box-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route($routes['save']) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori_list as $kat)
                                    <option value="{{ $kat }}">{{ ucwords($kat) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <input type="text" name="pertanyaan" class="form-control"
                                placeholder="Contoh: Bagaimana cara pemesanan?" required>
                        </div>
                        <div class="form-group">
                            <label>Jawaban</label>
                            <textarea name="jawaban" class="form-control" rows="5"
                                placeholder="Tulis jawaban. Bisa pakai HTML sederhana (<strong>, <br>, <ul>)." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="number" name="urutan" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="status" checked> Aktif
                                </label>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ route($routes['index']) }}" class="btn btn-primary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
@endsection