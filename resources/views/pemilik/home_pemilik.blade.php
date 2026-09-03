  @extends('layouts.template')  
@section('content') 

<!-- Pop up alert berhasil -->  
@if (session('pesan_welcome'))  
    <div class="alert alert-success" role="alert">  
        {{ session('pesan_welcome') }}  
    </div>  
@endif  

<!-- Pop up alert peringatan -->  
@if (session('message') && !session('pesan_wellcome'))  
    <div class="alert alert-warning" role="alert">  
        {{ session('message') }}  
    </div>  
@endif

<!-- Run text -->
<div class="card-body">
    <marquee style="font-family:arial; font-size:30px; color:#000000;">
        Selamat Datang Di {{ $title }}
    </marquee>
</div>
<br>

<div class="row">

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">

                <h3> <?= count($tot_jenis) ?></h3> <!-- Tampilkan jumlah permintaab produk -->
                <p>Data Jenis Produk</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3> <?= count($tot_varian) ?></h3> <!-- Tampilkan jumlah permintaab produk -->
                <p>Data Varian Produk</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->

    <!-- Small boxes (Stat box) -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3> <?= count($tot_produk) ?></h3> <!-- Tampilkan jumlah produk -->
                <p>Data Produk</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3> <?= count($tot_stok) ?></h3> <!-- Tampilkan jumlah stok produk -->
                <p>Data Stok Produk</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->

</div>
<!-- /.row -->

        <!-- Form Filter Bulan & Tahun -->
        <div class="box box-solid">
            <div class="box-header">
    <form method="get" class="mb-4">
        <div class="form-row row align-items-end">
            <div class="col-md-4">
                <label for="bulan">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">-- Semua Bulan --</option>
                    @for ($bln = 1; $bln <= 12; $bln++)
                        <option value="{{ $bln }}" {{ $bln == $bulan ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $bln, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label for="tahun">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-control">
                    @php
                        $tahun_sekarang = date('Y');
                    @endphp
                    @for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 100; $i--)
                        <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>{{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
            </div>
        </div>
    </form>

    <!-- Informasi Ringkas -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-left-primary shadow mb-3 p-3 bg-light">
                <h6 class="font-weight-bold text-primary mb-1">Produk Terlaris</h6>
                <p>{{ $produk_terlaris->nama_produk ?? '-' }} ({{ $produk_terlaris->total ?? 0 }} terjual)</p>
            </div>
            <div class="card border-left-warning shadow mb-3 p-3 bg-light">
                <h6 class="font-weight-bold text-warning mb-1">Produk Paling Sedikit Terjual</h6>
                <p>{{ $produk_tersedikit->nama_produk ?? '-' }} ({{ $produk_tersedikit->total ?? 0 }} terjual)</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-success shadow mb-3 p-3 bg-light">
                <h6 class="font-weight-bold text-success mb-1">Nilai Penjualan Tertinggi</h6>
                <p>{{ $produk_nilai_tertinggi->nama_produk ?? '-' }} (Rp.
                    {{ number_format($produk_nilai_tertinggi->total ?? 0, 0, ',', '.') }})</p>
            </div>
            <div class="card border-left-danger shadow mb-3 p-3 bg-light">
                <h6 class="font-weight-bold text-danger mb-1">Nilai Penjualan Terendah</h6>
                <p>{{ $produk_nilai_terendah->nama_produk ?? '-' }} (Rp.
                    {{ number_format($produk_nilai_terendah->total ?? 0, 0, ',', '.') }})</p>
            </div>
        </div>
    </div>

       <!-- Container untuk Chart Penjualan -->
                <div style="width: 90%; max-width: 800px; margin: auto;">
                </div>
                <h3 class="text-center">Grafik Penjualan Produk</h3>
                <canvas id="chartPenjualanProduk" width="800" height="400"></canvas>
            </div>

            <!-- Container untuk Chart Nilai Penjualan -->
            <div style="width: 90%; max-width: 800px; margin: auto;">
            </div>
            <h3 class="text-center">Grafik Nilai Penjualan</h3>
            <canvas id="chartNilaiPenjualan" width="800" height="400"></canvas>
        </div>

       

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>

    <script>
        const labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const penjualanData = @json($grafik_penjualan);
        const nilaiData = @json($grafik_nilai);

        const totalPenjualan = Array(12).fill(0);
        const totalNilai = Array(12).fill(0);

        penjualanData.forEach(item => {
            const index = item.bulan - 1;
            totalPenjualan[index] += parseInt(item.total);
        });

        nilaiData.forEach(item => {
            const index = item.bulan - 1;
            totalNilai[index] += parseInt(item.total_nilai);
        });

        // Chart Penjualan Produk
        new Chart(document.getElementById('chartPenjualanProduk'), {
            type: 'bar',
            data: {
                labels: labelBulan,
                datasets: [{
                    label: 'Total Produk Terjual',
                    data: totalPenjualan,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart Nilai Penjualan
        new Chart(document.getElementById('chartNilaiPenjualan'), {
            type: 'bar',
            data: {
                labels: labelBulan,
                datasets: [{
                    label: 'Nilai Penjualan (Rp.)',
                    data: totalNilai,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

         @endsection
