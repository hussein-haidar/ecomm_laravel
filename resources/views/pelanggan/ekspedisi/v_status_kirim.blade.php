@extends('layouts.app')
@section('content')
    <main class="product-section">
        <div class="container-product">
            <h3 class="text-title">Status Pengiriman</h3>

            <!-- Tabel Pengiriman Aktif (Belum Sampai Tujuan) -->
            <h4 class="mt-4">📦 Pengiriman Aktif</h4>
            <div class="table-responsive mt-2 mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Resi</th>
                            <th>Gambar</th>
                            <th>Produk</th>
                            <th>Ekspedisi</th>
                            <th>Status Pengiriman</th>
                            <th>Estimasi Pengiriman</th>
                            <th>Estimasi Waktu Tiba</th>
                            <th>*</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengiriman_aktif as $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->kode_resi ?? '-' }}</td>
                                <td>
                                    <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle"
                                        width="80px" height="80px">
                                </td>
                                <td>{{ $value->nama_produk ?? '-' }}</td>
                                    <td>
    <div style="font-size: 14px; line-height: 1.5;">
        <strong>Jenis Kurir:</strong> {{ $value->jenis_kurir }}<br>
        <strong>Ongkir:</strong> Rp. {{ number_format((int) preg_replace('/[^0-9]/', '', $value->ongkir), 0, ',', '.') }}
    </div>
</td>
                                <td>
                                    <span
                                        class="badge 
        @if ($value->status_kirim == 'Pesanan dibuat') bg-warning
        @elseif ($value->status_kirim == 'Dibayar') 
            bg-warning
        @elseif ($value->status_kirim == 'Dikemas') 
            bg-info
        @elseif (in_array($value->status_kirim, [
                'Dikirim dari toko',
                'Disortir',
                'Dikirim dari gudang',
                'Sampai gudang tujuan',
                'Diantar kurir',
            ])) 
            bg-primary
        @elseif ($value->status_kirim == 'Sampai tujuan') 
            bg-success
        @elseif ($value->status_kirim == 'Pesanan diterima') 
            bg-success
        @else 
            bg-secondary @endif">
                                        {{ $value->status_kirim ?? 'Belum Dikirim' }}
                                    </span>
                                </td>
                                <td>{{ $value->estimasi_waktu ?? '-' }}</td>
                                <td>{{ $value->estimasi_waktu_tiba ? \Carbon\Carbon::parse($value->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ url('pelanggan_data/tracking/' . $value->id_beli) }}" class="btn btn-info"
                                        style="display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; text-decoration: none; border-radius: 30px; font-weight: 500; box-shadow: 0 4px 12px rgba(37, 117, 252, 0.3); transition: all 0.3s ease; border: none;">
                                        Lacak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada pengiriman aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tabel Pengiriman Selesai (Sampai Tujuan) -->
            <h4 class="mt-4">✅ Pengiriman Selesai</h4>
            <div class="table-responsive mt-2 mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Resi</th>
                            <th>Gambar</th>
                            <th>Produk</th>
                            <th>Ekspedisi</th>
                            <th>Status Pengiriman</th>
                            <th>Estimasi Pengiriman</th>
                            <th>Estimasi Waktu Tiba</th>
                               <th>*</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengiriman_selesai as $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->kode_resi ?? '-' }}</td>
                                <td>
                                    <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}" class="img-circle"
                                        width="80px" height="80px">
                                </td>
                                <td>{{ $value->nama_produk ?? '-' }}</td>
                            <td>
    <div style="font-size: 14px; line-height: 1.5;">
        <strong>Jenis Kurir:</strong> {{ $value->jenis_kurir }}<br>
        <strong>Ongkir:</strong> Rp. {{ number_format((int) preg_replace('/[^0-9]/', '', $value->ongkir), 0, ',', '.') }}
    </div>
</td>
                                <td>
                                    <span
                                        class="badge 
        @if ($value->status_kirim == 'Sampai tujuan') bg-success
        @elseif ($value->status_kirim == 'Pesanan diterima') 
            bg-success
        @else 
            bg-secondary @endif">
                                        {{ $value->status_kirim ?? 'Selesai' }}
                                    </span>
                                </td>
                                <td>{{ $value->estimasi_waktu ?? '-' }}</td>
                                <td>{{ $value->estimasi_waktu_tiba ? \Carbon\Carbon::parse($value->estimasi_waktu_tiba)->format('d M Y H:i') : '-' }}
                                </td>
                                  <td>
                                    <a href="{{ url('pelanggan_data/tracking/' . $value->id_beli) }}" class="btn btn-info"
                                        style="display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; text-decoration: none; border-radius: 30px; font-weight: 500; box-shadow: 0 4px 12px rgba(37, 117, 252, 0.3); transition: all 0.3s ease; border: none;">
                                        Lacak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada pengiriman yang selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
