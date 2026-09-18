@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wishlist remove
        document.querySelectorAll('.btn-wishlist-remove').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const idWishlist = this.dataset.idWishlist;
                const row = this.closest('tr');
                
                Swal.fire({
                    title: 'Hapus dari Wishlist?',
                    text: 'Produk akan dihapus dari daftar wishlist Anda.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('pelanggan_data.wishlist.delete', ['id_wishlist' => '__ID__']) }}'.replace('__ID__', idWishlist), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showToast(data.message || 'Produk dihapus dari wishlist', 'success');
                                row.style.opacity = '0';
                                row.style.transition = 'opacity 0.3s';
                                setTimeout(() => row.remove(), 300);
                                
                                // Check if table is empty
                                const tbody = document.querySelector('#wishlistTable tbody');
                                if (tbody && tbody.children.length === 0) {
                                    location.reload();
                                }
                            } else {
                                showToast(data.message || 'Gagal menghapus', 'error');
                            }
                        })
                        .catch(() => showToast('Error', 'error'));
                    }
                });
            });
        });
    });
</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="fw-bold mb-0"><i class="fas fa-heart me-2 text-danger"></i>Wishlist Saya</h4>
                    <a href="{{ route('home_toko.katalog') }}" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-plus me-1"></i>Tambah Produk
                    </a>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if($wishlist->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Wishlist Kosong</h5>
                            <p class="text-muted">Simpan produk favorit Anda di sini untuk dibeli nanti.</p>
                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                                <i class="fas fa-store me-1"></i>Mulai Belanja
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="wishlistTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Gambar</th>
                                        <th style="min-width: 250px;">Produk</th>
                                        <th>Harga</th>
                                        <th style="width: 150px;">Stok</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wishlist as $item)
                                        @php
                                            $stok = $item->jumlah_stok_produk ?? 0;
                                            $stokClass = $stok <= 0 ? 'text-danger' : ($stok <= 5 ? 'text-warning' : 'text-success');
                                            $stokText = $stok <= 0 ? 'Habis' : ($stok <= 5 ? "Sisa $stok" : 'Tersedia');
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" class="d-block">
                                                    <img src="{{ asset('fotoproduk/' . ($item->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="60" height="60" style="object-fit: cover;" alt="{{ $item->nama_produk }}">
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->nama_produk }}</div>
                                                <div class="text-muted small"><i class="fas fa-store me-1"></i>{{ $item->nama_toko }}</div>
                                                @if($item->harga_flash ?? false)
                                                    <span class="badge bg-danger mt-1"><i class="fas fa-bolt me-1"></i>FLASH SALE</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->harga_flash ?? false)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="text-danger fw-bold">Rp{{ number_format($item->harga_produk, 0, ',', '.') }}</span>
                                                        <span class="text-muted text-decoration-line-through small">Rp{{ number_format($item->harga_normal, 0, ',', '.') }}</span>
                                                        <span class="badge bg-danger">{{ $item->diskon_persen ?? 0 }}%</span>
                                                    </div>
                                                @else
                                                    <span class="fw-bold text-success">Rp{{ number_format($item->harga_produk, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $stokClass }}">{{ $stokText }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($stok > 0)
                                                        <a href="{{ url('home_toko/detail_produk/' . urlencode($item->nama_produk)) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(session('user_logged_in'))
                                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 btn-add-cart" 
                                                                data-id-stok="{{ $item->id_stok }}"
                                                                data-nama-produk="{{ $item->nama_produk }}"
                                                                data-harga="{{ $item->harga_produk }}"
                                                                data-nama-toko="{{ $item->nama_toko }}"
                                                                title="Tambah ke Keranjang">
                                                                <i class="fas fa-cart-plus"></i>
                                                            </button>
                                                        @endif
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled title="Stok Habis">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-wishlist-remove" data-id-wishlist="{{ $item->id_wishlist }}" title="Hapus dari Wishlist">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-5">
                                <i class="fas fa-shopping-bag me-2"></i>Lanjut Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection