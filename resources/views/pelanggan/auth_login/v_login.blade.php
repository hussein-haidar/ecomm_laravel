<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title2 }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('template_admin/plugins/iCheck/square/blue.css') }}">
    <!-- Icon -->
    <link href="{{ asset('icon/SugarCRM-Outright.ico') }}" rel="shortcut icon">

    <!-- HTML5 Shim and Respond.js for IE8 support -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js "></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js "></script>
    <![endif]-->

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">

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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('pesan'))
                <div class="alert alert-warning">{{ session('pesan') }}</div>
            @endif

            @if (session('pesan_warning'))
                <div class="alert alert-warning">{{ session('pesan_warning') }}</div>
            @endif

            @if (session('pesan_success'))
                <div class="alert alert-success">{{ session('pesan_success') }}</div>
            @endif

            <div class="col-mt-4 text-center">
                <img class="img-responsive" src="{{ asset($logo_to_use ?? 'bgimage/register.png') }}" alt="Logo Toko">
                <p></p>
            </div>

            <form action="{{ route('auth.cek_login_pelanggan') }}" method="POST">  
              @csrf  

            <div class="form-group has-feedback">  
              <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>  
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>  
          </div>  

            <div class="form-group has-feedback">  
              <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>  
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>  
          </div>  

            <div class="row">
                <div class="col-xs-8">
                    <a href="{{ route('auth.lupa_password') }}" class="btn btn-link">Lupa Password?</a>
                </div>
                
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">SIGN IN</button>
                </div>
                <div class="col-xs-8">
                    <a href="{{ route('auth.register_pelanggan') }}" class="btn btn-link">Belum Punya Akun?</a>
                </div>
                  <div class="col-xs-8">
                    <a href="{{ route('auth.register_user') }}" class="btn btn-link">Buka Toko?</a>
                </div>
            </div>
          </form>

          <div class="row" style="margin-top: 10px;">
              <div class="col-xs-12">
                  <a href="{{ route('auth.google.redirect') }}" class="btn btn-block btn-danger btn-flat">
                      <i class="fa fa-google" aria-hidden="true"></i> Login dengan Google
                  </a>
              </div>
          </div>
        </div>
    </div>

    <!-- jQuery 3 -->
    <script src="{{ asset('template_admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('template_admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ asset('template_admin/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- Slide up alerts -->
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 3000);
    </script>
</body>
</html>