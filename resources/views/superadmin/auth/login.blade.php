<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/font-awesome/css/font-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/Ionicons/css/ionicons.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">

    <link rel="stylesheet" href="{{ asset('template_admin/plugins/iCheck/square/blue.css') }}">
    <!-- Icon -->
    <link href="{{ asset('icon/SugarCRM-Outright.ico') }}" rel="shortcut icon">

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"
        rel="stylesheet">

    <style>
        /* Gunakan data dari controller */
        .login-page {
            background: url("{{ asset($bgd_to_use ?? 'bgdweb/SugarCRM-Outright.jpg') }}") no-repeat center center fixed;
            background-size: cover;
        }

        .login-logo {
            text-align: center;
            font-size: 24px;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-box-body">
               <div class="login-logo">
                <b>  {{ $dataWebsite['nama_toko'] }}</b>
            </div>

            <div class="register-box-body">

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Display error hak akses halaman -->
                @if (session()->has('pesan_akses'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('pesan_akses') }}
                    </div>
                @endif

                <!-- Display error username dan password salah -->
                @if (session()->has('pesan_warning'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('pesan_warning') }}
                    </div>
                @endif

                <!-- Display logout success -->
                @if (session()->has('pesan_success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('pesan_success') }}
                    </div>
                @endif

                <form action="{{ route('auth.cek_login_superadmin') }}" method="POST">
                    @csrf

                    <div class="form-group has-feedback">
                        <input type="text" name="username" class="form-control" placeholder="Masukkan Nama Pengguna"
                            required>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control" placeholder="Masukkan Password"
                            required>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>

                    <div class="row">
                        <div class="col-xs-8">
                            <a href="{{ route('auth.register_superadmin') }}" class="btn btn-link">Belum Punya Akun?</a>
                        </div>

                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">SIGN IN</button>
                        </div>
                
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <a href="{{ route('auth.lupa_password_user') }}" class="btn btn-link">Lupa Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="{{ asset('template_admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('template_admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script>
            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });

            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 3000);
        </script>
</body>

</html>
