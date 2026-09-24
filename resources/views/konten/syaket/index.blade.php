@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <a href="{{ route($routes['add']) }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Bagian S&K
            </a>
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
                            <th>Tipe</th>
                            <th>Judul</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($syaket as $key => $value)
                            @php
                                $id = $value->id_tpl_syaket ?? $value->id_syaket_toko;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($value->tipe === 'intro')
                                        <span class="label label-info">Intro</span>
                                    @else
                                        <span class="label label-primary">Pasal</span>
                                    @endif
                                </td>
                                <td>{{ $value->judul ?? '(Intro / Tanpa Judul)' }}</td>
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