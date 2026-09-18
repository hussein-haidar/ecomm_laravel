@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-upload me-2 text-primary"></i>Upload Bukti Bayar</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('pelanggan_data.saveBayar', $pembayaran->id_bayar) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Pelanggan</label>
                                <input type="text" class="form-control" value="{{ $pembayaran->nama_pelanggan }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Bank Penerima</label>
                                <input type="text" class="form-control" value="{{ $pembayaran->bank_tujuan }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">No Rekening</label>
                                <input type="text" class="form-control" value="{{ $pembayaran->no_rek }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Total Bayar</label>
                                <input type="text" class="form-control" value="Rp{{ number_format($pembayaran->total_bayar, 0, ',', '.') }}" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Upload Bukti Bayar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('foto_bayar') is-invalid @enderror" name="foto_bayar" id="preview_gambar" accept="image/png,image/jpg,image/jpeg" required>
                                <div class="form-text">Format: PNG/JPG/JPEG. Contoh nama file: <code>bukti_transfer_bri.jpg</code></div>
                                @error('foto_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-2">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Konfirmasi Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection