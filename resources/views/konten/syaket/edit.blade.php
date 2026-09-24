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

                    @php
                        $id = $syaket->id_tpl_syaket ?? $syaket->id_syaket_toko;
                    @endphp

                    <form action="{{ route($routes['update'], $id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Tipe Bagian</label>
                            <select name="tipe" class="form-control" required>
                                <option value="pasal" {{ $syaket->tipe === 'pasal' ? 'selected' : '' }}>
                                    Pasal (dengan judul & nomor)
                                </option>
                                <option value="intro" {{ $syaket->tipe === 'intro' ? 'selected' : '' }}>
                                    Intro (paragraf pembuka tanpa judul)
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Judul Bagian (kosongkan jika tipe Intro)</label>
                            <input type="text" name="judul" value="{{ $syaket->judul }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Isi (mendukung HTML)</label>
                            <textarea name="isi" class="form-control" rows="10" required>{{ $syaket->isi }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Urutan</label>
                            <input type="number" name="urutan" value="{{ $syaket->urutan }}" class="form-control" min="0">
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="status" {{ $syaket->status ? 'checked' : '' }}> Aktif
                                </label>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ route($routes['index']) }}" class="btn btn-primary">Kembali</a>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection