@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-3"></div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header"></div>

                <div class="box-body">
                    @if (session('errors'))
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach (session('errors')->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pemilik_data.save_kupon') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="kode_kupon">Kode Kupon</label>
                            <input type="text" id="kode_kupon" name="kode_kupon" class="form-control"
                                placeholder="Masukkan Kode Kupon" value="{{ old('kode_kupon') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_kupon">Nama Kupon</label>
                            <input type="text" id="nama_kupon" name="nama_kupon" class="form-control"
                                placeholder="Masukkan Nama Kupon" value="{{ old('nama_kupon') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="tipe_diskon">Tipe Diskon</label>
                            <select id="tipe_diskon" name="tipe_diskon" class="form-control" required>
                                <option value="">--Pilih Tipe--</option>
                                <option value="persen" {{ old('tipe_diskon') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                                <option value="nominal" {{ old('tipe_diskon') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nilai_diskon">Nilai Diskon</label>
                            <input type="number" id="nilai_diskon" name="nilai_diskon" class="form-control"
                                placeholder="Masukkan Nilai Diskon" value="{{ old('nilai_diskon') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="min_pembelian">Minimal Pembelian</label>
                            <input type="number" id="min_pembelian" name="min_pembelian" class="form-control"
                                placeholder="Masukkan Minimal Pembelian" value="{{ old('min_pembelian') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="kuota">Kuota</label>
                            <input type="number" id="kuota" name="kuota" class="form-control"
                                placeholder="Masukkan Kuota Penggunaan" value="{{ old('kuota') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="masa_berlaku">Masa Berlaku</label>
                            <input type="date" id="masa_berlaku" name="masa_berlaku" class="form-control"
                                value="{{ old('masa_berlaku') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('pemilik_data.kupon') }}" class="btn btn-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>
@endsection
