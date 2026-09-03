@extends('layouts.template')
@section('content')
    <div class="box">
        <div class="box-header">
        </div>

        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Grafik Penjualan</h3>
                        </div>
                        <div class="box-body">
                            <canvas id="chartPenjualan" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">No</th>
                            <th>Tanggal Penjualan</th>
                            <th>Kode Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Produk</th>
                            <th>Jumlah Terjual</th>
                            <th>Harga Satuan</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($data_penjualan as $value)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    @php
                                        $bulan = [
                                            1 => 'Januari',
                                            'Februari',
                                            'Maret',
                                            'April',
                                            'Mei',
                                            'Juni',
                                            'Juli',
                                            'Agustus',
                                            'September',
                                            'Oktober',
                                            'November',
                                            'Desember',
                                        ];

                                        $timestamp = strtotime($value->tanggal_penjualan);

                                        $tanggal = date('d', $timestamp);
                                        $nama_bulan = $bulan[date('n', $timestamp)];
                                        $tahun = date('Y', $timestamp);
                                        $jam = date('H:i:s', $timestamp);

                                        echo "$tanggal $nama_bulan $tahun, $jam";
                                    @endphp
                                </td>
                                <td>{{ $value->kode_pesanan }}</td>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->nama_produk }}</td>
                                <td>{{ $value->jumlah_terjual }}</td>
                                <td>Rp. {{ number_format($value->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($value->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('chartPenjualan').getContext('2d');
        var chartPenjualan = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($label_bulan ?? []) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($data_chart ?? []) !!},
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    borderColor: 'rgba(60,141,188,1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return 'Rp. ' + value.toLocaleString();
                            }
                        }
                    }]
                }
            }
        });
    </script>
@endsection
