@extends('layouts.template')

@section('content')
    <div class="box">
        <div class="box-header">
            <h4 class="box-title">Laporan Penjualan</h4>
        </div>

        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_periode">Filter Periode</label>
                        <select id="filter_periode" class="form-control">
                            <option value="7">7 Hari Terakhir</option>
                            <option value="30" selected>30 Hari Terakhir</option>
                            <option value="90">90 Hari Terakhir</option>
                            <option value="365">1 Tahun Terakhir</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" class="form-control"
                            value="{{ old('tanggal_mulai') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" class="form-control"
                            value="{{ old('tanggal_akhir') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary form-control" id="btn_filter">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <canvas id="grafikPenjualan" height="100"></canvas>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Transaksi</span>
                            <span class="info-box-number" id="total_transaksi">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pendapatan</span>
                            <span class="info-box-number" id="total_pendapatan">Rp. 0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Rata-rata Penjualan</span>
                            <span class="info-box-number" id="rata_rata">Rp. 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">No</th>
                            <th>Tanggal</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="tabel_laporan">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('grafikPenjualan').getContext('2d');
            var grafikPenjualan = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: [],
                        borderColor: '#00a65a',
                        backgroundColor: 'rgba(0, 166, 90, 0.1)',
                        fill: true,
                        tension: 0.3
                    }, {
                        label: 'Jumlah Transaksi',
                        data: [],
                        borderColor: '#00c0ef',
                        backgroundColor: 'rgba(0, 192, 239, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return 'Rp. ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

            function loadGrafik(periode) {
                var url = "{{ route('pemilik_data.grafikPenjualan') }}";
                var params = { periode: periode };

                var tanggalMulai = document.getElementById('tanggal_mulai').value;
                var tanggalAkhir = document.getElementById('tanggal_akhir').value;

                if (tanggalMulai && tanggalAkhir) {
                    params.tanggal_mulai = tanggalMulai;
                    params.tanggal_akhir = tanggalAkhir;
                }

                var queryString = new URLSearchParams(params).toString();

                fetch(`${url}?${queryString}`)
                    .then(response => response.json())
                    .then(data => {
                        var labels = data.map(item => item.tanggal);
                        var pendapatan = data.map(item => item.total_pendapatan);
                        var transaksi = data.map(item => item.jumlah_transaksi);

                        grafikPenjualan.data.labels = labels;
                        grafikPenjualan.data.datasets[0].data = pendapatan;
                        grafikPenjualan.data.datasets[1].data = transaksi;
                        grafikPenjualan.update();

                        var totalTransaksi = transaksi.reduce((a, b) => a + b, 0);
                        var totalPendapatan = pendapatan.reduce((a, b) => a + b, 0);
                        var rataRata = totalTransaksi > 0 ? Math.round(totalPendapatan / totalTransaksi) : 0;

                        document.getElementById('total_transaksi').textContent = totalTransaksi;
                        document.getElementById('total_pendapatan').textContent = 'Rp. ' + totalPendapatan.toLocaleString('id-ID');
                        document.getElementById('rata_rata').textContent = 'Rp. ' + rataRata.toLocaleString('id-ID');

                        var tabelBody = document.getElementById('tabel_laporan');
                        tabelBody.innerHTML = '';
                        var no = 1;
                        data.forEach(function(item) {
                            var row = '<tr>' +
                                '<td>' + no++ + '</td>' +
                                '<td>' + item.tanggal + '</td>' +
                                '<td>' + item.jumlah_transaksi + '</td>' +
                                '<td>Rp. ' + item.total_pendapatan.toLocaleString('id-ID') + '</td>' +
                                '</tr>';
                            tabelBody.innerHTML += row;
                        });

                        $('#example1').DataTable().destroy();
                        $('#example1').DataTable({
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
                    });
            }

            document.getElementById('btn_filter').addEventListener('click', function() {
                var periode = document.getElementById('filter_periode').value;
                loadGrafik(periode);
            });

            document.getElementById('filter_periode').addEventListener('change', function() {
                loadGrafik(this.value);
            });

            loadGrafik(30);
        });
    </script>
@endsection
