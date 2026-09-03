@extends('layouts.app')
@section('content')
<main class="product-section">
<div class="container my-5">
<h3 class="product-title mb-4"><i class="fas fa-bolt text-warning"></i> Flash Sale</h3>
@if(empty($flashSales))
<div class="text-center py-5"><i class="fas fa-clock fa-3x text-muted mb-3 d-block"></i><p class="text-muted">Tidak ada flash sale aktif.</p></div>
@else
@foreach($flashSales as $id => $fs)
<div class="mb-5">
<div class="d-flex justify-content-between align-items-center mb-3"><h5 class="text-danger"><i class="fas fa-fire"></i> {{ $fs['info']->nama_flash_sale }}</h5><span class="badge bg-danger fs-countdown" data-end="{{ $fs['info']->waktu_selesai }}"></span></div>
<div class="row">@foreach($fs['items'] as $item)<div class="col-md-3 mb-3"><div class="card h-100 border-danger"><img src="{{ asset('fotoproduk/' . ($item->foto_produk ?? 'default.png')) }}" class="card-img-top" style="height:180px;object-fit:cover;"><div class="card-body text-center"><h6>{{ $item->nama_produk }}</h6><p class="text-decoration-line-through text-muted mb-1">Rp.{{ number_format($item->harga_normal,0,',','.') }}</p><p class="text-danger fw-bold h5">Rp.{{ number_format($item->harga_flash_sale,0,',','.') }}</p><p class="small text-muted">Stok: {{ ($item->kuota ?? 0) - ($item->terjual ?? 0) }}</p><a href="{{ url('home_toko/detail_produk/' . $item->nama_produk) }}" class="btn btn-buy btn-sm w-100">Beli</a></div></div></div>@endforeach</div>
</div>
@endforeach
@endif
</div>
</main>
<script>document.querySelectorAll('.fs-countdown').forEach(el=>{const end=new Date(el.dataset.end).getTime();setInterval(()=>{const diff=end-Date.now();if(diff<=0){el.textContent='Selesai';return;}const h=Math.floor(diff/3600000);const m=Math.floor((diff%3600000)/60000);const s=Math.floor((diff%60000)/1000);el.textContent=h+'j '+m+'m '+s+'s';},1000);});</script>
@endsection
