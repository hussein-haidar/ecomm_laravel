<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'Lupa Password' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template_admin/bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template_admin/plugins/iCheck/square/blue.css') }}">
    <link href="{{ asset('icon/SugarCRM-Outright.ico') }}" rel="shortcut icon">

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">

    <style>
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

            @if (session('pesan'))
                <div class="alert alert-success">{{ session('pesan') }}</div>
            @endif

            @if (session('pesan_warning'))
                <div class="alert alert-warning">{{ session('pesan_warning') }}</div>
            @endif

            @if (session('dev_reset_link'))
                <div class="alert alert-info">
                    <strong>Tautan Reset (Mode Development):</strong><br>
                    <a href="{{ session('dev_reset_link') }}">{{ session('dev_reset_link') }}</a>
                </div>
            @endif

            <form action="{{ route('auth.cek_proses_user') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username / Email</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username atau Email" value="{{ old('username') }}">
                    @error('username')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-xs-8">
                        <a href="{{ route('auth.login_user') }}" class="btn btn-link">Login</a>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block">Kirim Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('template_admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template_admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template_admin/plugins/iCheck/icheck.min.js') }}"></script>

    <script>
        window.setTimeout(function () {
            $(".alert").fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 3000);
    </script>
</body>
</html>