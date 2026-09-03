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
                
                <!-- pop up alert wrong-->
              @if(session('errors'))  
    <div class="alert alert-danger" role="alert">  
        <ul>  
            @foreach(session('errors')->all() as $error)  
                <li>{{ $error }}</li>  
            @endforeach  
        </ul>  
    </div>  
@endif  

                <form action="{{ route('pemilik_data.update_user', $user->id_user) }}" method="POST" enctype="multipart/form-data">  
        @csrf  

              <div class="form-group">
        <label for="username">Username:</label>  
        <input type="text" class="form-control" name="username" id="username" value="{{ $user->username }}" required>  
</div>

 <div class="form-group">
        <label for="fullname">Fullname:</label>  
        <input type="text" class="form-control" name="fullname" id="fullname" value="{{ $user->fullname }}" required>  
</div>

<div class="form-group">
    <label>Sesi User</label>
    <input type="text" name="sesi_user" value="{{ $sesi_user }}" class="form-control" readonly>
</div>

<div class="form-group">
    <label for="nama_title">Nama Title:</label>  
    <input type="text" class="form-control" name="nama_title" value="{{ $user->nama_title }}" id="fullname" required>  
</div>

 <div class="form-group">
        <label for="password">Password:</label>  
        <input type="text" class="form-control" name="password" id="password" value="{{ $user->password }}"required>  
</div>

  <div class="form-group">  
    <label>Level User</label>  
    <select id="level_user" name="level" class="form-control">  
        <!-- Menampilkan level yang sudah dipilih sebelumnya -->  
          <option value="superadmin" {{ old('level', $user['level']) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
        <option value="pemilik" {{ old('level', $user['level']) == 'pemilik' ? 'selected' : '' }}>Pemilik</option>  
        <option value="admin" {{ old('level', $user['level']) == 'admin' ? 'selected' : '' }}>Admin</option>  
    </select>  
</div>  

      <div class="form-group">  
                        <label>Foto User Terkini</label>  
                        <p></p>  
                        <img src="{{ asset('fotouser/' . $user->foto_user) }}" id="gambar_load" width="100px">  
                    </div>  

                    <div class="form-group">  
                        <label>Upload Foto User Baru</label>  
                        <p></p>  
                        <input type="file" class="form-control" name="foto_user" id="preview_gambar">  
                    </div>  

        <a href="{{ route ('pemilik_data.user') }}" class="btn btn-primary">Kembali</a>
          <button type="submit" class="btn btn-success">Simpan</button>
    </form>  

            </div>
        </div>

    </div>
    <div class="col-md-3">
    </div>

@endsection