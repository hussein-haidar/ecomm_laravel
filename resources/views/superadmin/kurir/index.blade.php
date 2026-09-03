@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header">
            <a href="{{ route('superadmin_data.add_kurir') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Data
            </a>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success">
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Kurir</th>
                            <th>Tipe Kurir</th>
                            <th>Ongkir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
          foreach ($kurir as $key => $value) {
          ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>{{ $value->jenis_kurir }}</td>
                                <td>{{ $value->tipe_kurir }}</td>
                           <td>Rp. {{ number_format($value->ongkir, 0, ',', '.') }}</td>
                            <td>
                               <a href="{{ route('superadmin_data.edit_kurir', $value->id_kurir) }}"
   class="btn btn-xs btn-warning">
   <i class="fa fa-fw fa-edit"></i>Edit
</a>

<!-- Tombol Delete dengan SweetAlert -->
<button class="btn btn-danger btn-sm btn-xs btn-delete" 
        data-id="{{ $value->id_kurir }}"
        data-name="{{ $value->jenis_kurir }}">
   <i class="fa fa-fw fa-trash"></i> Delete
</button>

<!-- Form Delete -->
<form id="delete-form-{{ $value->id_kurir }}"
      action="{{ route('superadmin_data.delete_kurir', $value->id_kurir) }}" 
      method="POST"
      style="display: none;">
   @csrf
   @method('DELETE')
</form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data '" + name + "' tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + id).submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
