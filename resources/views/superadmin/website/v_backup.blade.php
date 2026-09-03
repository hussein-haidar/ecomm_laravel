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

                <!-- Popup Alert for Errors -->  
@if ($errors->any())  
    <div class="alert alert-danger" role="alert">  
        <ul>  
            @foreach ($errors->all() as $error)  
                <li>{{ $error }}</li>  
            @endforeach  
        </ul>  
    </div>  
@endif  

        <!-- Form untuk trigger proses backup -->
     <form action="{{ route('superadmin_data.proses_db') }}" method="post">  
    @csrf  
    <button type="submit" class="btn btn-sm btn-success">  
        <i class="fa fa-plus-circle" aria-hidden="true"></i> Backup Database  
    </button>  
</form>  

            </div>
        </div>

        <div class="col-md-3">
        </div>

        @endsection