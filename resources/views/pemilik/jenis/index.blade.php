  @extends('layouts.template')  
@section('content') 
  
        <div class="box">
            <div class="box-header">
               <td> <a href="{{ route('pemilik_data.add_jenis') }}" class="btn-sm"><i class="fa fa-plus-circle" aria-hidden="true"></i>
        Tambah Data</a></td>
            </div>

               <div class="box-body">
    <!-- alert success add data -->
  @if (session('success'))  
    <div class="alert alert-success">  
        {{ session('success') }}  
    </div>  
@endif

    <div class="table-responsive">
      <table id="example1" class="table table-bordered table-striped ">
        <thead>
          <tr>
            <th scope="col" width="1%">No</th>
              <th>Jenis jenis</th>  
                      <th scope="col" width="auto">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          foreach ($jenis as $key => $value) {
          ?>
            <tr>
              <td><?= $no++; ?></td>
               <td>{{ $value->jenis_produk }}</td>  
                               <td>
               <!-- Link untuk Edit jenis -->  
    <a href="{{ route('pemilik_data.edit_jenis', $value->id_jenis) }}" class="btn btn-xs btn-warning"><i class="fa fa-fw fa-edit"></i>Edit</a>
              <!-- Link untuk Delete jenis -->  
                <button class="btn btn-danger btn-sm btn-xs" data-toggle="modal" data-target="#delete<?= $value['id_jenis'] ?>"><i class="fa fa-fw fa-trash"></i>Delete</button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table> 
    
            </div>
        </div>

      <!-- Modal delete-->  
<?php foreach ($jenis as $key => $value) { ?>  
  <div class="modal fade" id="delete<?= $value['id_jenis'] ?>">  
    <div class="modal-dialog">  
      <div class="modal-content">  
        <div class="modal-header">  
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">  
            <span aria-hidden="true">&times;</span></button>  
          <h4 class="modal-title">Delete <?= $title ?></h4>  
        </div>  
        <div class="modal-body">  
          <h4>  
            <p>  
              <center>Apakah Anda Ingin Menghapus Data&nbsp;<?= $value['jenis_produk'] ?> ?</center>  
            </p>  
          </h4>  
        </div>  

        <div class="modal-footer">  
          <!-- Form untuk menghapus data dengan metode DELETE -->  
          <form action="{{ route('pemilik_data.delete_jenis', $value['id_jenis']) }}" method="POST">  
            @csrf  
            @method('DELETE')  
            <button type="submit" class="btn btn-success pull-left btn-flat">Delete</button>  
          </form>  
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Close</button>  
        </div>  
      </div>  
      <!-- /.modal-content -->  
    </div>  
    <!-- /.modal-dialog -->  
  </div>  
<?php } ?>

@endsection