@extends('layouts.template')  
@section('content')   

<div class="row">  
    <div class="col-md-3">
        </div> 

    <div class="col-md-6">  
        <div class="box">  
            <div class="box-header">
                </div>  

                 <!-- /.box-header -->
            <div class="box-body">
          @if ($errors->any())  
    <div class="alert alert-danger" role="alert">  
        <ul>  
            @foreach ($errors->all() as $error)  
                <li>{{ $error }}</li>  
            @endforeach  
        </ul>  
    </div>  
@endif  

<form action="{{ url('home_pemilik/update_profile/' . $profil['id_user']) }}" method="POST" enctype="multipart/form-data">  
    @csrf   

          <div class="form-group">  
                <label>Username</label>  
                <input name="username" value="{{ session('username') }}" class="form-control" required>  
            </div>  

            <div class="form-group">  
                <label>Password</label>  
                <input name="password" type="text" id="ShowPass" value="{{ session('password') }}" class="form-control" required>  
            </div> 

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input name="fullname"  value="{{ session('fullname') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nama_title">Sesi User:</label>  
                    <input type="text" class="form-control" name="sesi_user" id="sesi_user" value="{{session('sesi_user') }}" required>  
                </div>

                <div class="form-group">
                    <label>Foto Profil Terkini</label>
                    <p></p>
                  <img src="{{ asset('fotouser/' . session('foto_user')) }}" class="img-circle" id="gambar_load" width="100px" height="100px">  
                </div>

                <div class="form-group">
                    <label>Upload Foto Profil Baru</label>
                    <p></p>
                    <input type="file" class="form-control" name="foto_user" id="preview_gambar">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                 <a href="{{ route('pemilik_data.profil') }}" class="btn btn-primary">Kembali</a>  
                </div>

</form>  


        </div>  
    </div>  

    <div class="col-md-3">
        </div>  

@endsection