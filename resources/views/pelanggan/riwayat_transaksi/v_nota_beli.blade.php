@extends('layouts.app')

@section('content')
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

        $badgeStatus = [
            'Berhasil' => 'bg-success',
            'Ditunda' => 'bg-warning text-dark',
            'Dibatalkan' => 'bg-danger',
        ];
        $kelasStatus = $badgeStatus[$pembelian->status_beli] ?? 'bg-secondary';
    @endphp

    <style>
        .nota-toolbar .btn {
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 600;
        }

        .nota-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 28px;
            max-width: 900px;
            margin: 0 auto;
        }

        .nota-header {
            background: linear-gradient(135deg, #1d4ed8, #7c3aed);
            color: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .nota-logo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .nota-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nota-logo i {
            font-size: 28px;
            color: #1d4ed8;
        }

        .nota-judul {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            text-align: right;
        }

        .nota-header .badge {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .nota-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 16px 18px;
            height: 100%;
        }

        .nota-box h6 {
            font-weight: 700;
            color: #1d4ed8;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .nota-box table {
            width: 100%;
            font-size: 14px;
        }

        .nota-box td {
            padding: 3px 0;
            vertical-align: top;
        }

        .nota-box td:first-child {
            color: #64748b;
            width: 45%;
        }

        .nota-box td:last-child {
            font-weight: 600;
            color: #0f172a;
        }

        .nota-table thead th {
            background: #1d4ed8;
            color: #fff;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: none !important;
            white-space: nowrap;
        }

        .nota-table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .nota-table .foto-produk {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .nota-summary {
            width: 280px;
            max-width: 100%;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 16px 18px;
        }

        .nota-summary .row-sum {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
            color: #475569;
        }

        .nota-summary .row-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px dashed #cbd5e1;
            margin-top: 8px;
            padding-top: 10px;
            font-weight: 800;
            font-size: 18px;
            color: #1d4ed8;
        }

        .nota-footer {
            margin-top: 24px;
            padding: 16px;
            background: #eff6ff;
            border-radius: 12px;
            text-align: center;
            color: #1e40af;
            font-weight: 600;
        }

        @media (max-width: 575px) {
            .nota-card {
                padding: 14px;
            }

            .nota-judul {
                font-size: 16px;
                text-align: left;
            }
        }

        @media print {
            @page {
                margin: 12mm;
            }

            body * {
                visibility: hidden;
            }

            #nota-area,
            #nota-area * {
                visibility: visible;
            }

            #nota-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                box-shadow: none;
                border: none;
            }

            .nota-toolbar,
            .no-print {
                display: none !important;
            }
        }
    </style>

    <main class="product-section">
        <div class="container my-4">
            <div class="d-flex flex-wrap gap-2 mb-4 nota-toolbar no-print">
                <a href="{{ route('pelanggan_data.unduhNotaPdf', $pembelian->id_beli) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Unduh Nota
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="cetakNota()">
                    <i class="fas fa-print me-1"></i> Cetak Nota
                </button>
                <a href="{{ route('pelanggan_data.riwayatBeli') }}" class="btn btn-outline-secondary ms-auto">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
                </a>
            </div>

            @if ($pembelian)
                <div id="nota-area" class="nota-card">
                    {{-- Header toko --}}
                    <div class="nota-header d-flex align-items-center">
                        <div class="nota-logo">
                            @if (!empty($pembelian->logo_website))
                                <img src="{{ asset('logowebsite/' . $pembelian->logo_website) }}" alt="Logo {{ $pembelian->nama_toko }}">
                            @else
                                <i class="fas fa-store"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $pembelian->nama_toko }}</h4>
                            <div class="small opacity-75">{{ $pembelian->alamat_pusat }}</div>
                            <div class="small opacity-75">
                                <i class="fab fa-whatsapp me-1"></i>{{ $pembelian->wa_pusat ?: '-' }}
                            </div>
                        </div>
                        <div class="text-md-end">
                            <div class="nota-judul">NOTA PEMBELIAN</div>
                            <span class="badge {{ $kelasStatus }} mt-1">
                                <i class="fas fa-check-circle me-1"></i>{{ $pembelian->status_beli }}
                            </span>
                        </div>
                    </div>

                    {{-- Info transaksi & pengiriman --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="nota-box">
                                <h6><i class="fas fa-file-invoice me-1"></i> Informasi Transaksi</h6>
                                <table>
                                    <tr>
                                        <td>Kode Transaksi</td>
                                        <td>{{ $pembelian->kode_beli }}</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Pembelian</td>
                                        <td>{{ $tanggalBeli }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status Pembelian</td>
                                        <td>{{ $pembelian->status_beli }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="nota-box">
                                <h6><i class="fas fa-truck me-1"></i> Informasi Pengiriman</h6>
                                <table>
                                    <tr>
                                        <td>Kurir</td>
                                        <td>{{ $pembelian->jenis_kurir ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ongkos Kirim</td>
                                        <td>{{ $rupiah($ongkir) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Estimasi Tiba</td>
                                        <td>{{ $estimasiTiba }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Berat</td>
                                        <td>{{ number_format($totalBerat, 0, ',', '.') }} {{ $pembelian->satuan_berat }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Penerima & pengirim --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="nota-box">
                                <h6><i class="fas fa-user me-1"></i> Penerima</h6>
                                <table>
                                    <tr>
                                        <td>Nama</td>
                                        <td>{{ $pembelian->nama_pelanggan }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Telepon</td>
                                        <td>{{ $pembelian->no_telpon }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $pembelian->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>{{ $pembelian->alamat }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="nota-box">
                                <h6><i class="fas fa-store me-1"></i> Pengirim</h6>
                                <table>
                                    <tr>
                                        <td>Nama Toko</td>
                                        <td>{{ $pembelian->nama_toko }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>{{ $pembelian->alamat_pusat }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kontak</td>
                                        <td>{{ $pembelian->wa_pusat ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Detail produk --}}
                    <h5 class="mt-4 mb-2 fw-bold">
                        <i class="fas fa-box-open me-1"></i> Detail Produk
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle nota-table mb-0">
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
                                    <td>
                                        @if (!empty($pembelian->foto_produk))
                                            <img src="{{ asset('fotoproduk/' . $pembelian->foto_produk) }}"
                                                class="foto-produk" alt="{{ $pembelian->nama_produk }}">
                                        @else
                                            <div class="foto-produk d-flex align-items-center justify-content-center bg-light text-muted">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pembelian->nama_produk }}</div>
                                        <small class="text-muted">{{ $pembelian->berat_produk }} {{ $pembelian->satuan_berat }}</small>
                                    </td>
                                    <td>{{ $pembelian->ukuran_produk }}</td>
                                    <td class="text-end">{{ $rupiah($pembelian->harga_produk) }}</td>
                                    <td class="text-center">{{ $pembelian->jumlah_produk }} {{ $pembelian->satuan_produk }}</td>
                                    <td class="text-end fw-semibold">{{ $rupiah($subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Ringkasan pembayaran --}}
                    <div class="d-flex justify-content-end mt-3">
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
                    </div>

                    {{-- Footer --}}
                    <div class="nota-footer">
                        <i class="fas fa-heart me-1"></i>
                        Terima kasih sudah berbelanja di {{ $pembelian->nama_toko }}.
                        Simpan atau unduh nota ini sebagai bukti pembelian Anda.
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center py-5">
                    <i class="fas fa-exclamation-triangle me-1"></i> Data nota tidak ditemukan.
                    <a href="{{ route('pelanggan_data.riwayatBeli') }}" class="alert-link">Kembali ke Riwayat Pembelian</a>
                </div>
            @endif
        </div>
    </main>

    <script>
        function cetakNota() {
            window.print();
        }
    </script>
@endsection
