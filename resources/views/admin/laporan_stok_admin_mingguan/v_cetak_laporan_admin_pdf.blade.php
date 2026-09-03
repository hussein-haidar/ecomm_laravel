<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $title3 }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111;
            font-size: 11px;
        }

        .kop-surat {
            text-align: center;
            margin: 10px 0 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
        }

        .kop-surat img {
            width: 70px;
            height: auto;
        }

        .kop-surat h1 {
            font-size: 20px;
            margin: 4px 0 0;
        }

        .kop-surat h2 {
            font-size: 14px;
            margin: 2px 0;
        }

        .kop-surat p {
            font-size: 11px;
            margin: 1px 0;
        }

        .report-date {
            text-align: left;
            margin: 6px 0 10px;
            font-size: 11px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background: #ddd;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .ttd {
            text-align: right;
            margin-top: 34px;
            line-height: 1.8;
        }

        .ttd .space {
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <div class="kop-surat">
        @php
            $toko = is_object($toko) ? (array) $toko : (array) ($toko ?? []);
            $logo = $toko['logo_website'] ?? 'fotodefault/gudang.png';
            $nama_toko = $toko['nama_toko'] ?? 'Nama Toko Default';
            $alamat = $toko['alamat_pusat'] ?? 'Alamat Default';
            $telpon = $toko['wa_pusat'] ?? 'Nomor Telepon Default';
            $logoPath = str_starts_with($logo, 'fotodefault/')
                ? public_path($logo)
                : \App\Support\Uploads::resolve('logowebsite', $logo);
        @endphp
        @if (file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo">
        @endif
        <h1>{{ $nama_toko }}</h1>
        <h2>LAPORAN MUTASI PRODUK MINGGUAN</h2>
        <p>Alamat: {{ $alamat }}</p>
        <p>Telpon: {{ $telpon }}</p>
    </div>

    <div class="report-date">
        Laporan Stok Produk Dibuat: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
        @if ($start_date && $end_date)
            <br>Periode: {{ $start_date }} s/d {{ $end_date }}
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" width="1%">No</th>
                <th>Kode Stok</th>
                <th>Tanggal Masuk</th>
                <th>Nama Produk</th>
                <th>Foto</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Ukuran</th>
                <th>Total Harga</th>
                <th>Total Berat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse ($data_laporan_admin as $value)
                @php
                    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $tanggal = strtotime($value->tanggal_masuk_produk);
                    $fotoPath = !empty($value->foto_produk) ? \App\Support\Uploads::resolve('fotoproduk', $value->foto_produk) : '';
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $value->kode_stok }}</td>
                    <td>{{ date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal) }}</td>
                    <td>{{ $value->nama_produk }}</td>
                    <td>
                        @if ($fotoPath && file_exists($fotoPath))
                            <img class="photo" src="{{ $fotoPath }}">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $value->jenis_produk }}</td>
                    <td>{{ $value->jumlah_stok_produk }} {{ $value->satuan_produk }}</td>
                    <td>{{ $value->ukuran_produk }}</td>
                    <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $value->total_berat }} {{ $value->satuan_berat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd">
        Pekalongan, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
        Penanggung Jawab<br>
        <div class="space"></div>
        {{ auth()->user()->nama_lengkap ?? session('nama_lengkap', 'Nama User') }}
    </div>
</body>

</html>
