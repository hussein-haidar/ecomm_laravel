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
            <!-- Alert Success -->  
            @if (session('pesan'))  
                <div class="alert alert-success" role="alert">  
                    {{ session('pesan') }}  
                </div>  
            @endif  

            <center>  
                <div class="form-group">  
                    <label>Foto Profil</label>  
                    <p></p>  
                    <img src="{{ asset('fotouser/' . session('foto_user')) }}" class="img-circle" id="gambar_load" width="100px" height="100px">  
                </div>  
            </center>  

            <div class="form-group">  
                <label>Username</label>  
                <input name="username" value="{{ session('username') }}" class="form-control" readonly>  
            </div>  

            <div class="form-group">  
                <label>Password</label>  
                <input name="password" type="password" id="ShowPass" value="{{ session('password') }}" class="form-control" readonly>  
                <input type="checkbox" onclick="myFunction()">&nbsp; Show Password  
            </div>  

            <div class="form-group">  
                <label>Nama Lengkap</label>  
                <input name="fullname" value="{{ session('fullname') }}" class="form-control" readonly>  
            </div>  

          <div class="form-group">  
    <label>Level</label>  
    <input type="text" name="level" value="{{ session('level') == 'pemilik' ? 'Pemilik' : (session('level') == 'admin' ? 'Admin' : '') }}" class="form-control" readonly>  
</div>  

            <div class="form-group">  
                <label>Terakhir Login</label>  
                @php  
                    setlocale(LC_TIME, 'id_ID');  
                    $tanggal = strtotime(session('last_login'));  
                    $bulan = [  
                        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'  
                    ];  
                    $formattedDate = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y H:i:s', $tanggal);  
                @endphp  
                <input type="text" value="{{ $formattedDate }}" class="form-control" readonly>  
            </div>  

            <div>  
           
            </div>  

        </div>  
    </div>  

    <div class="col-md-3">
        </div>  

@endsection  