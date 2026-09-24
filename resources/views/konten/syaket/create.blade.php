@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-8">
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
                            <label>Tipe Bagian</label>
                            <select name="tipe" class="form-control" required>
                                <option value="pasal">Pasal (dengan judul & nomor)</option>
                                <option value="intro">Intro (paragraf pembuka tanpa judul)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Judul Bagian (kosongkan jika tipe Intro)</label>
                            <input type="text" name="judul" class="form-control"
                                placeholder="Contoh: Informasi Produk">
                        </div>
                        <div class="form-group">
                            <label>Isi (mendukung HTML: &lt;strong&gt;, &lt;br&gt;, &lt;ul&gt; &lt;li&gt;)</label>
                            <textarea name="isi" class="form-control" rows="10" required></textarea>
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
    </div>
@endsection