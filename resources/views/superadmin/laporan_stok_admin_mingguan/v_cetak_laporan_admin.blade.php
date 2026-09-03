<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Gudang Web | {{ $title3 }}</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="{{ asset('template_admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template_admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template_admin/bower_components/Ionicons/css/ionicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template_admin/dist/css/AdminLTE.min.css') }}">
  <link rel="shortcut icon" href="{{ asset('icon/gudang.ico') }}">

  <!-- HTML5 Shim and Respond.js IE8 support -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- Custom Print Styles -->
  <style>
    @page {
      margin: 0;
    }

    @media print {
      body {
        margin: 0;
        padding: 10mm;
      }

      .no-print {
        display: none !important;
      }
    }

    .kop-surat {
      text-align: center;
      margin: 20px 0;
    }

    .kop-surat h1 {
      margin: 0;
      font-size: 24px;
    }

    .kop-surat h2 {
      margin: 0;
      font-size: 18px;
    }

    .kop-surat p {
      margin: 0;
      font-size: 14px;
    }

    .kop-surat img {
      width: 100px;
      height: auto;
    }

    .page-header {
      margin-top: 20px;
    }

    .report-date {
      float: left;
      width: 100%;
      text-align: left;
    }

    .page-header h2 small {
      display: block;
    }
  </style>
</head>

<body onload="window.print();">
  <div class="wrapper">
    <!-- Kop Surat -->
    <div class="kop-surat">
      @php
        $logo = $toko['logo_website'] ?? 'fotodefault/gudang.png';
        $nama_toko = $toko['nama_toko'] ?? 'Nama Toko Default';
        $alamat = $toko['alamat_pusat'] ?? 'Alamat Default';
        $telpon = $toko['wa_pusat'] ?? 'Nomor Telepon Default';
      @endphp
      <img src="{{ asset('logowebsite/' . $logo) }}" alt="Logo">
      <h1>{{ $nama_toko }}</h1>
      <h2>LAPORAN MUTASI PRODUK BULANAN {{ $nama_toko }}</h2>
      <p>Alamat: {{ $alamat }}</p>
      <p>Telpon: {{ $telpon }}</p>
    </div>

    <!-- Main content -->
    <section class="invoice">
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page">
            <small class="report-date">
              Laporan Stok Produk Dibuat: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            </small>
          </h2>
        </div>
      </div>

      <div class="row invoice-info"></div>

      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col" width="1%">No</th>
                <th>Kode Stok Produk</th>
                <th>Tanggal Masuk</th>
                <th>Nama Produk</th>
                <th>Foto Produk</th>
                <th>Jenis Produk</th>
                <th>Jumlah Stok Produk</th>
                <th>Ukuran Produk</th>
                <th>Total Harga Produk</th>
                <th>Total Berat Produk</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data_laporan_admin as $no => $value)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $value->kode_stok }}</td>
                <td>
                  @php
                    $bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $tanggal = strtotime($value->tanggal_masuk_produk);
                    echo date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
                  @endphp
                </td>
                <td>{{ $value->nama_produk }}</td>
                <td><img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle" width="80px" height="80px"></td>
                <td>{{ $value->jenis_produk }}</td>
                <td>{{ $value->jumlah_stok_produk }} {{ $value->satuan_produk }}</td>
                <td>{{ $value->ukuran_produk }}</td>
                <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                <td>{{ $value->total_berat }} {{ $value->satuan_berat }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-4"></div>
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
          Pekalongan, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
          Penanggung Jawab:<br><br><br><br><br>
          {{ auth()->user()->nama_lengkap ?? 'Nama User' }}
        </div>
      </div>
    </section>
  </div>
</body>

</html>
