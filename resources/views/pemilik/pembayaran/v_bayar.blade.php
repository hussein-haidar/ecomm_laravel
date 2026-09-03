@extends('layouts.template')
@section('content')

    <div class="box">
        <div class="box-header">
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <!-- Alert Success -->
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu Pembayaran</th>
                                <th>Pelanggan</th>
                                <th>Bank Tujuan</th>
                                <th>No Rekening</th>
                                <th>Total Bayar</th>
                                <th>Bukti Bayar</th>
                                <th>Status Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
        foreach ($pembayaran as $key => $value) {
        ?>
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

                                        $timestamp = strtotime($value->waktu_pembayaran);

                                        $tanggal = date('d', $timestamp);
                                        $nama_bulan = $bulan[date('n', $timestamp)]; // n = 1-12
                                        $tahun = date('Y', $timestamp);
                                        $jam = date('H:i:s', $timestamp); // Jam:Menit:Detik

                                        echo "$tanggal $nama_bulan $tahun, $jam";
                                    @endphp
                                </td>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->bank_tujuan }}</td>
                                <td>{{ $value->no_rek }}</td>
                                <td>Rp. {{ number_format($value->total_bayar, 0, ',', '.') }}</td>
                                <td>
                                    <img src="{{ asset('fotobayar/' . $value->foto_bayar) }}" class="img-circle"
                                        width="80px" height="80px">
                                    <a href="{{ route('admin_data.viewFoto', $value->id_bayar) }}">Lihat Foto</a>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin_data.konfirmStatusBayar') }}"
                                        class="d-inline">
                                        @csrf
                                        @method('POST') <!-- atau PATCH tergantung controller -->

                                        <input type="hidden" name="id_bayar" value="{{ $value->id_bayar }}">
                                        <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}"
                                            class="form-control" readonly>

                                        <select name="status_bayar" class="form-control d-inline w-auto"
                                            onchange="toggleAlasan(this, {{ $value->id_bayar }})"
                                            style="display: inline-block; width: auto;
                @if ($value->status_bayar == 'Dibayar') background-color: #5cb85c; color: white;
                @elseif($value->status_bayar == 'Dibatalkan') background-color: #d9534f; color: white;
                @else background-color: #f0ad4e; color: white; @endif">

                                            <option value="Belum Bayar"
                                                {{ $value->status_bayar == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar
                                            </option>
                                            <option value="Dibatalkan"
                                                {{ $value->status_bayar == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                                            </option>
                                            <option value="Dibayar"
                                                {{ $value->status_bayar == 'Dibayar' ? 'selected' : '' }}>
                                                Dibayar</option>
                                        </select>

                                        <div id="alasan-div-{{ $value->id_bayar }}"
                                            style="margin-top: 5px; display: {{ $value->status_bayar == 'Dibatalkan' ? 'block' : 'none' }};">
                                            <input type="text" name="alasan_batal" class="form-control mt-2"
                                                placeholder="Masukkan alasan pembatalan"
                                                value="{{ old('alasan_batal', $value->alasan_batal) }}">
                                            <button type="submit" class="btn btn-danger btn-sm mt-1">Kirim</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection

    <!-- Script Toggle Alasan -->
    <script>
        function toggleAlasan(select, id) {
            const div = document.getElementById('alasan-div-' + id);
            if (select.value === 'Dibatalkan') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
                select.form.submit(); // Submit otomatis jika bukan dibatalkan
            }
        }
    </script>
