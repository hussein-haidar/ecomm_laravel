@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Log Aktivitas</h2>

    <!-- Form Hapus Berdasarkan Waktu -->
    <form action="{{ route('superadmin.deleteByDate') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-danger">Hapus Log</button>
            </div>
        </div>
    </form>

    <!-- Tabel Log -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID User</th>
                <th>Aksi</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->id_log }}</td>
                <td>{{ $log->id_user }}</td>
                <td>{{ $log->aksi }}</td>
                <td>{{ $log->waktu }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection