@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-8">
                    <a href="{{ route($routes['add']) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah FAQ
                    </a>
                </div>
                <div class="col-md-4 text-right">
                    <form method="GET" action="{{ route($routes['index']) }}" class="form-inline d-flex gap-2 justify-content-end">
                        <select name="kategori" class="form-control input-sm" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategori_list as $kat)
                                <option value="{{ $kat }}" {{ $filter_kategori === $kat ? 'selected' : '' }}>
                                    {{ ucwords($kat) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ $search }}" class="form-control input-sm"
                            placeholder="Cari pertanyaan...">
                        <button type="submit" class="btn btn-default btn-sm">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kategori</th>
                            <th>Pertanyaan</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faq as $key => $value)
                            @php
                                $id = $value->id_tpl_faq ?? $value->id_faq_toko;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="label label-primary">{{ ucwords($value->kategori) }}</span>
                                </td>
                                <td>{{ $value->pertanyaan }}</td>
                                <td>{{ $value->urutan }}</td>
                                <td>
                                    @if ($value->status)
                                        <span class="label label-success">Aktif</span>
                                    @else
                                        <span class="label label-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route($routes['edit'], $id) }}" class="btn btn-xs btn-warning">
                                        <i class="fa fa-fw fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-danger btn-xs btn-delete" data-id="{{ $id }}">
                                        <i class="fa fa-fw fa-trash"></i> Delete
                                    </button>
                                    <form id="delete-form-{{ $id }}" action="{{ route($routes['delete'], $id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection