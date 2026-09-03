@extends('layouts.template')
@section('content')

@if (session('pesan_welcome'))
    <div class="alert alert-success" role="alert">
        {{ session('pesan_welcome') }}
    </div>
@endif

@if (session('message'))
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
        <div class="small-box bg-aqua">
            <div class="inner"><h3>{{ count($tot_bank) }}</h3><p>Data Bank</p></div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner"><h3>{{ count($tot_kurir) }}</h3><p>Data Kurir</p></div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner"><h3>{{ count($tot_toko) }}</h3><p>Data Toko</p></div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner"><h3>{{ count($tot_komisi) }}</h3><p>Total Komisi</p></div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
        </div>
    </div>
</div>

<!-- Filter Bulan & Tahun -->
 <div class="box box-solid">
            <div class="box-header">
<form method="GET" class="mb-4">
    <div class="row align-items-end">
        <div class="col-md-4">
            <label for="bulan">Pilih Bulan</label>
            <select name="bulan" class="form-control">
                <option value="">-- Semua Bulan --</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ ($bulan ?? '') == $i ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <label for="tahun">Pilih Tahun</label>
            <select name="tahun" class="form-control">
                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>
                        {{ $y }}
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

<!-- Grafik Komisi per Toko -->

<div class="row mt-4">
    <div class="col-12">
        <h3 class="text-center">
            Top Toko Berdasarkan Komisi
            @if($bulan_nama)
                ({{ $bulan_nama }} {{ $tahun }})
            @else
                (Tahun {{ $tahun }})
            @endif
        </h3>
        <div style="height: 500px; max-width: 900px; margin: 0 auto;">
            <canvas id="chartKomisiToko"></canvas>
        </div>
    </div>
</div>

<!-- Info Ringkas -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-left-success shadow p-3 bg-light">
            <h6 class="font-weight-bold text-success">Toko Terbaik</h6>
            <p>
                {{ $komisi_per_toko->isNotEmpty() ? $komisi_per_toko->first()->nama_toko : '-' }}<br>
                <strong>Rp {{ number_format($komisi_per_toko->isNotEmpty() ? $komisi_per_toko->first()->total_komisi : 0, 0, ',', '.') }}</strong>
            </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-left-info shadow p-3 bg-light">
            <h6 class="font-weight-bold text-info">Toko Aktif</h6>
            <p><strong>{{ $jumlah_toko_aktif }}</strong> dari {{ $jumlah_toko_total }} toko</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const tokoData = @json($komisi_per_toko);

    if (!tokoData || tokoData.length === 0) {
        document.getElementById('chartKomisiToko').parentElement.innerHTML = 
            '<div class="alert alert-info text-center" style="margin-top: 20px;">Tidak ada data komisi untuk periode ini.</div>';
    } else {
        const labels = tokoData.map(item => item.nama_toko);
        const komisi = tokoData.map(item => parseFloat(item.total_komisi) || 0);

        const colors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(255, 205, 86, 0.7)',
            'rgba(100, 181, 246, 0.7)',
            'rgba(255, 87, 34, 0.7)',
            'rgba(128, 128, 128, 0.7)',
            'rgba(60, 180, 75, 0.7)'
        ];

        new Chart(document.getElementById('chartKomisiToko'), {
            type: 'bar',
            data: {  // ✅ INI YANG KURANG: "data:"
                labels: labels,
                datasets: [{
                    label: 'Total Komisi (Rp)',
                    data: komisi,  // ✅ dan ini harus "data:"
                    backgroundColor: colors.slice(0, komisi.length),
                    borderColor: colors.slice(0, komisi.length).map(c => c.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.x;
                                const transaksi = tokoData[context.dataIndex]?.jumlah_transaksi || 0;
                                return [
                                    `Komisi: Rp ${new Intl.NumberFormat('id-ID').format(value)}`,
                                    `Transaksi: ${transaksi} kali`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }
</script>

@endsection