@extends('layouts.app')
@section('content')

    <!-- Product Section -->
    <main class="product-section">
        <div class="container-product">
            <h3 class="text-title">Upload Bukti Bayar</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Pembayaran -->
            <form action="{{ route('pelanggan_data.saveBayar', $pembayaran->id_bayar) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('POST')

                <input type="hidden" name="id_bayar" value="{{ session('id_bayar') }}" class="form-control" readonly>

                <div class="mb-3">
                    <label for="nama_pelanggan" class="form-label">Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                        value="{{ $pembayaran->nama_pelanggan }}" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="bank_tujuan" class="form-label">Bank Penerima</label>
                    <input type="text" id="bank_tujuan" name="bank_tujuan" value="{{ $pembayaran->bank_tujuan }}"
                        class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="no_rek" class="form-label">No Rekening</label>
                    <input type="text" id="no_rek" name="no_rek" value="{{ $pembayaran->no_rek }}"
                        class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="total_bayar" class="form-label">Total Bayar</label>
                    <input type="text" class="form-control" id="total_bayar" name="total_bayar"
                        value="Rp. {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Upload Bukti Bayar</label>
                     <small class="text-muted">Contoh nama file: <code>bukti_transfer_bri.jpg</code></small>
                    <input type="file" class="form-control" name="foto_bayar" id="preview_gambar" required>
                </div>

                <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
            </form>

        </div>
    </main>
@endsection
