<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Nota {{ $pembelian->kode_beli }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 13px;
        }

        .nota-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
        }

        .nota-header {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            color: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .nota-header .row {
            width: 100%;
        }

        .nota-logo {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #fff;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }

        .nota-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nota-toko-nama {
            font-size: 18px;
            font-weight: bold;
        }

        .nota-judul {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .nota-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            margin-top: 4px;
        }

        .nota-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .nota-box h6 {
            color: #1d4ed8;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table td.label {
            color: #64748b;
            width: 45%;
        }

        .info-table td.value {
            font-weight: 600;
        }

        .section-title {
            margin: 14px 0 8px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .nota-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .nota-table th {
            background: #1d4ed8;
            color: #fff;
            padding: 7px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .nota-table td {
            padding: 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .foto-produk {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .nota-summary {
            width: 260px;
            margin-left: auto;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 12px;
            margin-top: 12px;
        }

        .row-sum {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #475569;
        }

        .row-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px dashed #cbd5e1;
            margin-top: 6px;
            padding-top: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #1d4ed8;
        }

        .nota-footer {
            margin-top: 14px;
            padding: 10px;
            background: #eff6ff;
            border-radius: 8px;
            text-align: center;
            color: #1e40af;
            font-weight: 600;
            font-size: 12px;
        }

        .mb-1 {
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    @php
        $rupiah = function ($angka) {
            return 'Rp ' . number_format((float) $angka, 0, ',', '.');
        };

        $ongkir = (int) preg_replace('/[^0-9]/', '', $pembelian->ongkir);
        $subtotal = $pembelian->harga_produk * $pembelian->jumlah_produk;
        $totalBayar = $subtotal + $ongkir;
        $totalBerat = ($pembelian->berat_produk * $pembelian->jumlah_produk) ?? 0;

        $tanggalBeli = $pembelian->waktu_pembelian
            ? \Illuminate\Support\Carbon::parse($pembelian->waktu_pembelian)->format('d M Y H:i')
            : '-';
        $estimasiTiba = $pembelian->estimasi_waktu_tiba
            ? \Illuminate\Support\Carbon::parse($pembelian->estimasi_waktu_tiba)->format('d M Y H:i')
            : '-';

        $logoUrl = \App\Support\Uploads::resolve('logowebsite', $pembelian->logo_website ?? '');
        $fotoProdukUrl = \App\Support\Uploads::resolve('fotoproduk', $pembelian->foto_produk ?? '');
    @endphp

    <div class="nota-card">
        <div class="nota-header">
            <table style="width:100%;">
                <tr>
                    <td style="width:60px;">
                        @if ($logoUrl && file_exists($logoUrl))
                            <div class="nota-logo"><img src="{{ $logoUrl }}"></div>
                        @endif
                    </td>
                    <td>
                        <div class="nota-toko-nama">{{ $pembelian->nama_toko }}</div>
                        <div style="font-size:11px;opacity:.85;">{{ $pembelian->alamat_pusat }}</div>
                        <div style="font-size:11px;opacity:.85;">{{ $pembelian->wa_pusat ?: '-' }}</div>
                    </td>
                    <td style="text-align:right;">
                        <div class="nota-judul">NOTA PEMBELIAN</div>
                        <div class="nota-badge">{{ $pembelian->status_beli }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="nota-box">
            <h6>Informasi Transaksi</h6>
            <table class="info-table">
                <tr>
                    <td class="label">Kode Transaksi</td>
                    <td class="value">{{ $pembelian->kode_beli }}</td>
                </tr>
                <tr>
                    <td class="label">Waktu Pembelian</td>
                    <td class="value">{{ $tanggalBeli }}</td>
                </tr>
                <tr>
                    <td class="label">Status Pembelian</td>
                    <td class="value">{{ $pembelian->status_beli }}</td>
                </tr>
                <tr>
                    <td class="label">Kurir</td>
                    <td class="value">{{ $pembelian->jenis_kurir ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Ongkos Kirim</td>
                    <td class="value">{{ $rupiah($ongkir) }}</td>
                </tr>
                <tr>
                    <td class="label">Estimasi Tiba</td>
                    <td class="value">{{ $estimasiTiba }}</td>
                </tr>
                <tr>
                    <td class="label">Total Berat</td>
                    <td class="value">{{ number_format($totalBerat, 0, ',', '.') }} {{ $pembelian->satuan_berat }}</td>
                </tr>
            </table>
        </div>

        <div class="nota-box">
            <h6>Penerima</h6>
            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="value">{{ $pembelian->nama_pelanggan }}</td>
                </tr>
                <tr>
                    <td class="label">No. Telepon</td>
                    <td class="value">{{ $pembelian->no_telpon }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $pembelian->email }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="value">{{ $pembelian->alamat }}</td>
                </tr>
            </table>
        </div>

        <div class="section-title">Detail Produk</div>
        <table class="nota-table">
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="9%">Foto</th>
                    <th>Nama Produk</th>
                    <th>Ukuran</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">
                        @if ($fotoProdukUrl && file_exists($fotoProdukUrl))
                            <img class="foto-produk" src="{{ $fotoProdukUrl }}">
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <strong>{{ $pembelian->nama_produk }}</strong><br>
                        <small>{{ $pembelian->berat_produk }} {{ $pembelian->satuan_berat }}</small>
                    </td>
                    <td>{{ $pembelian->ukuran_produk }}</td>
                    <td class="text-end">{{ $rupiah($pembelian->harga_produk) }}</td>
                    <td class="text-center">{{ $pembelian->jumlah_produk }} {{ $pembelian->satuan_produk }}</td>
                    <td class="text-end"><strong>{{ $rupiah($subtotal) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="nota-summary">
            <div class="row-sum">
                <span>Subtotal Produk</span>
                <span>{{ $rupiah($subtotal) }}</span>
            </div>
            <div class="row-sum">
                <span>Ongkos Kirim</span>
                <span>{{ $rupiah($ongkir) }}</span>
            </div>
            <div class="row-total">
                <span>Total Bayar</span>
                <span>{{ $rupiah($totalBayar) }}</span>
            </div>
        </div>

        <div class="nota-footer">
            Terima kasih sudah berbelanja di {{ $pembelian->nama_toko }}.
            Simpan atau unduh nota ini sebagai bukti pembelian Anda.
        </div>
    </div>
</body>

</html>
