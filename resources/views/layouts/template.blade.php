<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $title }}</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="{{ asset('template_admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('template_admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{ asset('template_admin/bower_components/Ionicons/css/ionicons.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="{{ asset('template_admin/dist/css/skins/_all-skins.min.css') }}">
<!-- DataTables -->
<link rel="stylesheet"
    href="{{ asset('template_admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<!-- Icon -->
<link rel="shortcut icon" href="{{ !empty($dataWebsite['logo_website'] ?? null) ? asset('logo_website/' . $dataWebsite['logo_website']) : asset('icon/SugarCRM-Outright.ico') }}">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js d oesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

<link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>

<body class="hold-transition skin-blue sidebar-mini">

    <!-- Site wrapper -->
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="../../index2.html" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>A</b>LT</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>Admin</b>LTE</span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        @if (session()->get('level') == 'superadmin')
                          <!-- Notifications -->
       
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}" class="user-image"
                                        alt="User Image">
                                    <span class="hidden-xs">{{ session()->get('fullname') }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}"
                                            class="img-circle" alt="User Image">
                                        <p>
                                            {{ session()->get('fullname') }}
                                            <small>Level :
                                                @if (session()->get('level') == 'superadmin')
                                                    Superadmin
                                                @endif
                                                <br>
                                                Last Login : {{ session()->get('last_login') }}
                                            </small>
                                        </p>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ url('superadmin_data/profil') }}"
                                                class="btn btn-default btn-flat">
                                                <i class="fa fa-fw fa-user"></i>&nbsp;Profile
                                            </a>
                                        </div>
                                        <div class="pull-right">
                                            <form id="logout-form" action="{{ url('auth/logout_user') }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" class="btn btn-default btn-flat" id="logout-btn">
                                                    <i class="fa fa-fw fa-key"></i>&nbsp;Sign Out
                                                </button>
                                            </form>

                                        </div>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (session()->get('level') == 'pemilik')
               
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}"
                                        class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{ session()->get('fullname') }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}"
                                            class="img-circle" alt="User Image">
                                        <p>
                                            {{ session()->get('fullname') }}
                                            <small>Level :
                                                @if (session()->get('level') == 'pemilik')
                                                    Pemilik
                                                @endif
                                                <br>
                                                Last Login : {{ session()->get('last_login') }}
                                            </small>
                                        </p>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ url('pemilik_data/profil') }}"
                                                class="btn btn-default btn-flat">
                                                <i class="fa fa-fw fa-user"></i>&nbsp;Profile
                                            </a>
                                        </div>
                                        <div class="pull-right">
                                            <form id="logout-form" action="{{ url('auth/logout_user') }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" class="btn btn-default btn-flat" id="logout-btn">
                                                    <i class="fa fa-fw fa-key"></i>&nbsp;Sign Out
                                                </button>
                                            </form>

                                        </div>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (session()->get('level') == 'admin')

    @php
    // Fallback jika variabel tidak dikirim
    $jumlahBayar = $jumlahBayar ?? 0;
    $daftarNotifikasi = $daftarNotifikasi ?? collect();
@endphp

<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        @if($jumlahBayar > 0)
            <span class="label label-warning">{{ $jumlahBayar }}</span>
        @endif
    </a>
    <ul class="dropdown-menu">
        <li class="header">Anda memiliki {{ $jumlahBayar }} notifikasi pembayaran</li>
        <li>
            <ul class="menu">
                @forelse($daftarNotifikasi as $notif)
                    <li>
                            <a href="{{ route('admin_data.view_bayar') }}">
                                <i class="fa fa-money text-yellow"></i>
                            {{ $notif['nama_pelanggan'] ?? 'Pelanggan' }} - {{ $notif['status_bayar'] ?? 'Status tidak diketahui' }}
                            @if(!empty($notif['waktu_pembayaran']))
                                ({{ \Carbon\Carbon::parse($notif['waktu_pembayaran'])->format('d/m/Y H:i:s') }})
                            @endif
                        </a>
                    </li>
                @empty
                    <li>
                        <a href="#">
                            <i class="fa fa-info-circle text-muted"></i> Tidak ada notifikasi
                        </a>
                    </li>
                @endforelse
            </ul>
        </li>
        <li class="footer">
            <a href="{{ route('admin_data.view_bayar') }}">Lihat semua</a>
        </li>
    </ul>
</li>

                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}"
                                        class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{ session()->get('fullname') }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}"
                                            class="img-circle" alt="User Image">
                                        <p>
                                            {{ session()->get('fullname') }}
                                            <small>Level :
                                                @if (session()->get('level') == 'admin')
                                                    Admin
                                                @endif
                                                <br>
                                                Last Login : {{ session()->get('last_login') }}
                                            </small>
                                        </p>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ url('admin_data/profil') }}"
                                                class="btn btn-default btn-flat">
                                                <i class="fa fa-fw fa-user"></i>&nbsp;Profile
                                            </a>
                                        </div>
                                        <div class="pull-right">
                                            <form id="logout-form" action="{{ url('auth/logout_user') }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" class="btn btn-default btn-flat"
                                                    id="logout-btn">
                                                    <i class="fa fa-fw fa-key"></i>&nbsp;Sign Out
                                                </button>
                                            </form>

                                        </div>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>

        <!-- =============================================== -->

        <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ asset('fotouser/' . session()->get('foto_user')) }}" class="img-circle"
                            alt="User Image">
                    </div>

                    <div class="pull-left info">
                        <p>{{ session()->get('fullname') }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>

                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">MAIN NAVIGATION</li>
                    @if (session()->get('level') == 'superadmin')
                        <li>
                            <a href="{{ url('home_superadmin') }}">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                    @endif

                    @if (session()->get('level') == 'pemilik')
                        <li>
                            <a href="{{ url('home_pemilik') }}">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                    @endif

                    @if (session()->get('level') == 'admin')
                        <li>
                            <a href="{{ url('home_admin') }}">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>

                           <li class="header">CHAT NAVIGATION</li>
                        <li>
                            <a href="{{ route('admin_data.chat') }}">
                                <i class="fa fa-comments"></i> <span>Chat Pelanggan</span>
                                @php
                                    $jumlahChatAdmin = \App\Models\M_Chat::hitungBelumDibacaAdmin(session()->get('sesi_user'));
                                @endphp
                                @if ($jumlahChatAdmin > 0)
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{ $jumlahChatAdmin }}</span>
                                    </span>
                                @endif
                            </a>
                        </li>
                        
                    @endif

                    @if (session()->get('level') == 'superadmin')
                        <li class="header">BENEFIT NAVIGATION</li>
                        <li>
                            <a href="{{ url('superadmin_data/view_totbenefit') }}">
                                <i class="fa fa-folder"></i> <span>Data Benefit</span>
                            </a>
                        </li>

                        <li class="header">MASTER DATA NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Master Data</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('superadmin_data/komisi') }}"><i
                                            class="fa fa-circle-o"></i>Komisi Platform
                                    </a></li>
                                <li><a href="{{ url('superadmin_data/bank') }}"><i class="fa fa-circle-o"></i>Data
                                        Bank</a></li>
                                <li><a href="{{ url('superadmin_data/kurir') }}"><i class="fa fa-circle-o"></i>Data
                                        Kurir</a></li>
                                <li><a href="{{ url('superadmin_data/backup_db') }}"><i
                                            class="fa fa-circle-o"></i>Backup DB</a></li>
                            </ul>
                        </li>

                        <li class="header">SETTING NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Setting Website</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('superadmin_data/website') }}"><i
                                            class="fa fa-circle-o"></i>Setting
                                        Website</a></li>
                                <li><a href="{{ url('superadmin_data/event') }}"><i class="fa fa-circle-o"></i>Data
                                        Event</a></li>
                                <li><a href="{{ url('superadmin_data/view_website') }}"><i
                                            class="fa fa-circle-o"></i>Daftar
                                        Toko</a></li>
                            </ul>
                        </li>

                        <li class="header">LOG NAVIGATION</li>
                        <li>
                            <a href="{{ url('superadmin_data/log_aktivis') }}">
                                <i class="fa fa-folder"></i> <span>Log Aktivitas</span>
                            </a>
                        </li>
                    @endif

                    @if (session()->get('level') == 'pemilik')
                        <li class="header">MASTER DATA NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Master</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('pemilik_data/varian') }}"><i class="fa fa-circle-o"></i>Data
                                        Varian</a></li>
                                <li><a href="{{ url('pemilik_data/jenis') }}"><i class="fa fa-circle-o"></i>Data
                                        Jenis</a></li>
                                <li><a href="{{ url('pemilik_data/satuan') }}"><i class="fa fa-circle-o"></i>Data
                                        Satuan</a></li>
                                <li><a href="{{ url('pemilik_data/produk') }}"><i class="fa fa-circle-o"></i>Data
                                        Produk</a></li>
                            </ul>
                        </li>

                              <li class="header">SELLING NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Penjualan Produk</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('pemilik_data/view_jual') }}"><i
                                            class="fa fa-circle-o"></i>Penjualan Produk</a></li>
                                <li><a href="{{ url('pemilik_data/view_totjual') }}"><i
                                            class="fa fa-circle-o"></i><span>Total Penjualan Produk</span></a></li>
                            </ul>
                        </li>

                          <li class="header">BENEFIT NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Keuntungan Toko</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('pemilik_data/view_benefit') }}"><i
                                            class="fa fa-circle-o"></i>Keuntungan Toko</a></li>
                                <li><a href="{{ url('pemilik_data/view_totbenefit') }}"><i
                                            class="fa fa-circle-o"></i><span>Total Keuntungan Toko</span></a></li>
                            </ul>
                        </li>

                        <li class="header">SETTING NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Setting Toko</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('pemilik_data/website') }}"><i class="fa fa-circle-o"></i>Setting
                                        Toko</a></li>
                            </ul>
                        </li>

                        <li class="header">USERS NAVIGATION</li>
                        <li>
                            <a href="{{ url('pemilik_data/user') }}">
                                <i class="fa fa-users"></i> <span>Data Pengguna Sistem</span>
                            </a>
                        </li>
                    @endif

                    @if (session()->get('level') == 'admin')
                        <li class="header">PRODUCT NAVIGATION</li>
                        <li>
                            <a href="{{ url('admin_data/view_produk') }}">
                                <i class="fa fa-folder"></i> <span>Produk Batik</span>
                            </a>
                        </li>

                        <li class="header">STOCK NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Stok Produk</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('admin_data/stok') }}"><i class="fa fa-circle-o"></i>Stok
                                        Produk</a></li>
                                <li><a href="{{ url('admin_data/tot_stok') }}"><i
                                            class="fa fa-circle-o"></i><span>Total Stok Produk</span></a></li>
                            </ul>
                        </li>

     <li class="header">REPORT NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Laporan Stok Produk</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('admin_laporan_mingguan/laporanStok') }}"><i
                                            class="fa fa-circle-o"></i>Laporan Stok Mingguan</a></li>
                                <li><a href="{{ url('admin_laporan_bulanan/laporanStok') }}"><i
                                            class="fa fa-circle-o"></i>Laporan Stok Bulanan</a></li>
                            </ul>
                        </li>
                        <li class="header">SELLING NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Penjualan Produk</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('admin_data/view_jual') }}"><i
                                            class="fa fa-circle-o"></i>Penjualan Produk</a></li>
                                <li><a href="{{ url('admin_data/view_totjual') }}"><i
                                            class="fa fa-circle-o"></i><span>Total Penjualan Produk</span></a></li>
                            </ul>
                        </li>

                        <li class="header">PAYMENT NAVIGATION</li>
                        <li>
                            <a href="{{ url('admin_data/view_bayar') }}">
                                <i class="fa fa-folder"></i> <span>Pembayaran</span>
                            </a>
                        </li>

                        <li class="header">RETURN NAVIGATION</li>
                        <li>
                            <a href="{{ route('admin_data.view_retur') }}">
                                <i class="fa fa-undo"></i> <span>Retur & Pengembalian</span>
                                @php
                                    $jumlahRetur = \App\Models\M_Retur::countReturAktif(session()->get('sesi_user'));
                                @endphp
                                @if ($jumlahRetur > 0)
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{ $jumlahRetur }}</span>
                                    </span>
                                @endif
                            </a>
                        </li>

                        <li class="header">BENEFIT NAVIGATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i>
                                <span>Data Keuntungan Toko</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('admin_data/view_benefit') }}"><i
                                            class="fa fa-circle-o"></i>Keuntungan Toko</a></li>
                                <li><a href="{{ url('admin_data/view_totbenefit') }}"><i
                                            class="fa fa-circle-o"></i><span>Total Keuntungan Toko</span></a></li>
                            </ul>
                        </li>

                   
                    @endif

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- =============================================== -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <small></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i>{{ $title2 }}</a></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                @yield('content')

            </section>
            <!-- /.content -->

        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <strong>Copyright &copy; <?php echo date('Y'); ?></strong> All rights
            reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Create the tabs -->
            <ul class="nav nav-tabs nav-justified control-sidebar-tabs">

                <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Home tab content -->
                <div class="tab-pane" id="control-sidebar-home-tab">
                    <!-- /.control-sidebar-menu -->

                </div>
                <!-- /.tab-pane -->
                <!-- Stats tab content -->

        </aside>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('template_admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('template_admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('template_admin/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('template_admin/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('template_admin/dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('template_admin/dist/js/demo.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('template_admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template_admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert CDN -->
    <script>
        $(document).ready(function() {
            $('.sidebar-menu').tree()
        })
    </script>

    @if (session('pesan_profil'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('pesan_profil') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('pesan_website'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('pesan_website') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data tidak dapat dikembalikan setelah dihapus!",
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
    </script>

    @if (session('pesan_website'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('pesan_website') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        document.getElementById('logout-btn').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah form untuk submit langsung  

            // Menampilkan modal SweetAlert  
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to log out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out!',
                cancelButtonText: 'No, keep me logged in!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengonfirmasi, submit form  
                    document.getElementById('logout-form').submit();
                }
            });
        });
    </script>

    <!-- Script slide up alert window otomatis -->
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 3000);
    </script>

    <!-- Script tampil gambar -->
    <script>
        function bacaGambar(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#gambar_load').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('#preview_gambar').change(function() {
            bacaGambar(this);
        });
    </script>
    <!-- Script hide password -->
    <script>
        function myFunction() {
            var x = document.getElementById("ShowPass");
            if (x.type === "password") {
                x.type = "text";

            } else {
                x.type = "password";
            }
        }
    </script>

    <!-- AdminLTE data tables -->
    <script>
        $(document).ready(function() {
            var table = $('#example1').DataTable({
                "stateSave": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "pageLength": 10,
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            // Optional: Tombol edit arahkan langsung ke halaman edit
            $('#example1 tbody').on('click', '.btn-edit', function() {
                var row = $(this).closest('tr');
                var idPoli = row.find('td:eq(0)').text();
                window.location.href = "<?= url('pemilik_data/edit_user/') ?>" + idUser;
            });
        });
    </script>

</body>

</html>
