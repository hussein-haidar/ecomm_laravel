@extends('layouts.app')
@section('content')
<main class="product-section">
<div class="container my-5" style="max-width:700px;">
<h3 class="product-title mb-4">Notifikasi</h3>
@if($notifications->isEmpty())
<div class="text-center py-5"><p class="text-muted">Tidak ada notifikasi.</p></div>
@else
<div class="list-group">
@foreach($notifications as $n)
<a href="{{ $n->link ?? '#' }}" class="list-group-item list-group-item-action {{ $n->dibaca ? '' : 'list-group-item-primary' }}" onclick="fetch('{{ route('pelanggan_data.markReadNotifikasi', $n->id_notifikasi) }}')">
<div class="d-flex justify-content-between"><h6 class="mb-1">{{ $n->judul }}</h6><small class="text-muted">{{ $n->waktu }}</small></div>
<p class="mb-1 small">{{ $n->pesan }}</p>
<small class="badge bg-{{ $n->tipe=='sukses'?'success':($n->tipe=='peringatan'?'warning':'info') }}">{{ ucfirst($n->tipe) }}</small>
</a>
@endforeach
</div>
@endif
</div>
</main>
@endsection
