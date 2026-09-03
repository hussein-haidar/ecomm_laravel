@extends('layouts.template')
@section('content')
    <div class="box">
        <div class="box-header">
            <td>
                <a href="{{ url('superadmin_data/add_komisi') }}" class="btn-sm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
                </a>
            </td>
        </div>

        <div class="box-body">
            {{-- Alert Success Add --}}
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">No</th>
                            <th>Persentase</th>
                            <th>Deskripsi</th>
                            <th scope="col" width="auto">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
          foreach ($biaya as $key => $value) {
          ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>{{ $value->persentase }}</td>
                            <td>{{ $value->deskripsi }}</td>
                            <td>
                                <!-- Link untuk Edit satuan -->
                                <a href="{{ route('superadmin_data.edit_komisi', $value->id_biaya) }}"
                                    class="btn btn-xs btn-warning">
                                    <i class="fa fa-fw fa-edit"></i>Edit
                                </a>
                                <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal"
                                    data-target="#delete{{ $value->id_biaya }}">
                                    <i class="fa fa-fw fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                          <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal delete --}}
    @foreach ($biaya as $value)
        <div class="modal fade" id="delete{{ $value->id_biaya }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete {{ $title ?? 'Data Biaya' }}</h4>
                    </div>
                    <div class="modal-body">
                        <h4>
                            <p class="text-center">Apakah Anda Ingin Menghapus Data {{ $value->persentase }}?</p>
                        </h4>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ url('superadmin_data/delete_komisi/' . $value->id_biaya) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-success btn-flat pull-left">Delete</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
