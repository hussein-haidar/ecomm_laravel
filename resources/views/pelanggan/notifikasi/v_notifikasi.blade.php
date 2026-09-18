@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h4 class="fw-bold mb-0"><i class="fas fa-bell me-2 text-primary"></i>Notifikasi</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Notifikasi</h5>
                            <p class="text-muted">Saat ini tidak ada notifikasi baru untuk Anda.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $n)
                                <a href="{{ $n->link ?? '#' }}" 
                                   class="list-group-item list-group-item-action d-flex align-items-start gap-3 py-3 {{ $n->dibaca ? '' : 'bg-primary bg-opacity-10 border-start border-3 border-primary' }}"
                                   onclick="fetch('{{ route('pelanggan_data.markReadNotifikasi', $n->id_notifikasi) }}')">
                                    <div class="flex-shrink-0">
                                        <span class="badge rounded-pill px-3 py-2
                                            @if($n->tipe == 'sukses') bg-success
                                            @elseif($n->tipe == 'peringatan') bg-warning text-dark
                                            @else bg-info @endif">
                                            {{ ucfirst($n->tipe) }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1 fw-{{ $n->dibaca ? 'normal' : 'bold' }}">{{ $n->judul }}</h6>
                                            <small class="text-muted">{{ $n->waktu }}</small>
                                        </div>
                                        <p class="mb-0 small text-muted">{{ $n->pesan }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection