@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity input validation
        document.querySelectorAll('input[name="jumlah_produk"]').forEach(input => {
            input.addEventListener('change', function() {
                const min = parseInt(this.min) || 1;
                const max = parseInt(this.max) || 99;
                let val = parseInt(this.value);
                if (isNaN(val) || val < min) this.value = min;
                else if (val > max) this.value = max;
                this.form.submit();
            });
        });

        // Size select change
        document.querySelectorAll('select[name="ukuran_produk"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Checkout selected
        window.checkoutSelected = function() {
            const selectedProducts = [];
            document.querySelectorAll(".checkout-checkbox:checked").forEach(checkbox => {
                selectedProducts.push(checkbox.value);
            });

            if (selectedProducts.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Silakan pilih produk yang ingin di-checkout.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan melanjutkan proses checkout untuk produk yang dipilih.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement("form");
                    form.method = "GET";
                    form.action = "{{ route('pelanggan_data.beli') }}";

                    const csrfToken = document.createElement("input");
                    csrfToken.type = "hidden";
                    csrfToken.name = "_token";
                    csrfToken.value = "{{ csrf_token() }}";
                    form.appendChild(csrfToken);

                    selectedProducts.forEach(id => {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "selected_products[]";
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        // Toggle select all
        const selectAllBtn = document.getElementById('selectAllBtn');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.checkout-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => {
                    cb.checked = !allChecked;
                    const btn = document.querySelector(`.checkout-btn[data-id="${cb.value}"]`);
                    if (btn) {
                        btn.classList.toggle('btn-primary', !allChecked);
                        btn.classList.toggle('btn-outline-primary', allChecked);
                    }
                });
                this.innerHTML = allChecked ? '<i class="fas fa-check-square me-1"></i>Pilih Semua' : '<i class="fas fa-square me-1"></i>Batal Pilih';
            });
        }

        // Real-time stock check
        setInterval(function() {
            fetch('{{ route('home_toko.ambilStok') }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(produk => {
                        const row = document.querySelector(`tr[data-id-stok="${produk.id_stok}"]`);
                        if (row) {
                            const stokBadge = row.querySelector('.stok-badge');
                            const qtyInput = row.querySelector('input[name="jumlah_produk"]');
                            const maxStok = parseInt(produk.jumlah_stok_produk);
                            
                            if (stokBadge) {
                                stokBadge.textContent = maxStok <= 0 ? 'Habis' : maxStok;
                                stokBadge.className = 'badge stok-badge ' + (maxStok <= 0 ? 'bg-danger' : (maxStok <= 5 ? 'bg-warning text-dark' : 'bg-success'));
                            }
                            
                            if (qtyInput && parseInt(qtyInput.max) !== maxStok) {
                                qtyInput.max = maxStok;
                                if (parseInt(qtyInput.value) > maxStok) {
                                    qtyInput.value = maxStok;
                                    qtyInput.form.submit();
                                }
                            }

                            if (maxStok <= 0) {
                                row.classList.add('table-danger');
                                row.querySelectorAll('input, select, button').forEach(el => el.disabled = true);
                            }
                        }
                    });
                })
                .catch(err => console.error('Stock check error:', err));
        }, 30000);
    });
</script>
@endpush

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4" data-aos="fade-down">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Keranjang Belanja</h2>
            <p class="text-muted mb-0">{{ $keranjang->count() ?? 0 }} produk di keranjang</p>
        </div>
        @if($keranjang->count() > 0)
            <button type="button" id="selectAllBtn" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-square me-1"></i>Pilih Semua
            </button>
        @endif
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
        <div class="card-body py-3">
            <form method="GET" class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama produk, toko, atau ukuran..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Cari</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pelanggan_data.cart') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($keranjang->isEmpty())
        {{-- Empty Cart --}}
        <div class="card border-0 shadow-sm" data-aos="fade-up">
            <div class="card-body text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-shopping-basket fa-3x text-muted"></i>
                </div>
                <h4 class="fw-bold mb-2">Keranjang Anda Kosong</h4>
                <p class="text-muted mb-4">Belum ada produk di keranjang. Yuk mulai belanja!</p>
                <a href="{{ route('home_toko.katalog') }}" class="btn btn-primary rounded-pill px-5">
                    <i class="fas fa-store me-2"></i>Mulai Belanja
                </a>
            </div>
        </div>
    @else
        {{-- Cart Items --}}
        <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;"><input type="checkbox" id="selectAllHeader" class="form-check-input"></th>
                                <th style="min-width: 280px;">Produk</th>
                                <th class="text-end" style="width: 120px;">Harga</th>
                                <th style="width: 130px;">Ukuran</th>
                                <th style="width: 140px;">Jumlah</th>
                                <th class="text-end" style="width: 140px;">Total</th>
                                <th style="width: 150px;">Waktu</th>
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            @endphp
                            @foreach($keranjang as $value)
                                @php
                                    $tanggal = strtotime($value->waktu_ditambahkan);
                                    $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
                                    $formattedWaktu = date('H:i', $tanggal);
                                    $stok = $value->jumlah_stok_produk ?? 0;
                                @endphp
                                <tr data-id-stok="{{ $value->id_stok }}">
                                    <td>
                                        <input type="checkbox" class="checkout-checkbox form-check-input" value="{{ $value->id_keranjang }}" id="checkbox-{{ $value->id_keranjang }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ url('home_toko/detail_produk/' . urlencode($value->nama_produk)) }}" class="d-block">
                                                <img src="{{ asset('fotoproduk/' . ($value->foto_produk ?? 'default.jpg')) }}" class="rounded border" width="70" height="70" style="object-fit: cover;" alt="{{ $value->nama_produk }}">
                                            </a>
                                            <div class="flex-grow-1">
                                                <a href="{{ url('home_toko/detail_produk/' . urlencode($value->nama_produk)) }}" class="text-dark text-decoration-none fw-semibold d-block text-truncate" style="max-width: 250px;">{{ $value->nama_produk }}</a>
                                                <div class="text-muted small"><i class="fas fa-store me-1"></i>{{ $value->nama_toko }}</div>
                                                <span class="badge stok-badge {{ $stok <= 0 ? 'bg-danger' : ($stok <= 5 ? 'bg-warning text-dark' : 'bg-success') }}">
                                                    <i class="fas fa-boxes me-1"></i>Stok: {{ $stok }} {{ $value->satuan_produk }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-nowrap fw-medium">Rp{{ number_format($value->harga_produk, 0, ',', '.') }}</td>
                                    <td>
                                        <form id="update-cart-size-{{ $value->id_keranjang }}" action="{{ route('pelanggan_data.update_cart', ['id_keranjang' => $value->id_keranjang]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_keranjang" value="{{ $value->id_keranjang }}">
                                            <select name="ukuran_produk" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="">-- Pilih --</option>
                                                @foreach($value->ukuran_list as $ukuran_produk)
                                                    <option value="{{ trim($ukuran_produk) }}" {{ $value->ukuran_produk == trim($ukuran_produk) ? 'selected' : '' }}>
                                                        {{ trim($ukuran_produk) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form id="update-cart-qty-{{ $value->id_keranjang }}" action="{{ route('pelanggan_data.update_cart', ['id_keranjang' => $value->id_keranjang]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_keranjang" value="{{ $value->id_keranjang }}">
                                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                                <input type="number" name="jumlah_produk" class="form-control text-center" min="1" max="{{ $stok }}" value="{{ $value->jumlah_produk }}" onchange="this.form.submit()">
                                                <span class="input-group-text">{{ $value->satuan_produk }}</span>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-end text-nowrap fw-semibold text-success">Rp{{ number_format($value->total_harga, 0, ',', '.') }}</td>
                                    <td class="text-nowrap small">
                                        <i class="fas fa-calendar-alt text-muted me-1"></i>{{ $formattedTanggal }}<br>
                                        <span class="text-muted"><i class="fas fa-clock me-1"></i>{{ $formattedWaktu }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column gap-1">
                                            <button type="button" class="btn btn-sm {{ $value->id_keranjang ? 'btn-primary' : 'btn-outline-primary' }} checkout-btn" data-id="{{ $value->id_keranjang }}">
                                                <i class="fa {{ $value->id_keranjang ? 'fa-check-circle' : 'fa-square' }} me-1"></i>{{ $value->id_keranjang ? 'Dipilih' : 'Pilih' }}
                                            </button>
                                            <a href="javascript:void(0)" onclick="window.bukaChat && bukaChat('{{ $value->nama_toko }}', '{{ $value->id_stok }}')" class="btn btn-sm btn-outline-info" title="Chat Penjual {{ $value->nama_toko }}">
                                                <i class="fas fa-comments me-1"></i>Chat
                                            </a>
                                            <form action="{{ route('pelanggan_data.deleteCart', $value->id_keranjang) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100 btn-delete" data-id="{{ $value->id_keranjang }}" data-nama="{{ $value->nama_produk }}">
                                                    <i class="fa fa-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-body pt-3">
                {{ $keranjang->appends(request()->query())->links() }}
            </div>
        </div>

        {{-- Cart Summary & Checkout --}}
        <div class="row g-4" data-aos="fade-up">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                            <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Pilih produk (tombol <strong>Pilih</strong>), lalu klik <strong>Checkout</strong>.</span>
                            @if($keranjang->count() > 0)
                                <span class="badge bg-primary rounded-pill px-3 py-2">Total: {{ $keranjang->count() }} item</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Ringkasan</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal ({{ $jumlah_item }} item)</span>
                            <span class="fw-semibold">Rp{{ number_format($total_semua, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ongkir</span>
                            <span class="fw-semibold text-success">Dihitung di Checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-5">Total Estimasi</span>
                            <span class="fw-bold fs-5 text-primary">Rp{{ number_format($total_semua, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('home_toko.katalog') }}" class="btn btn-outline-primary rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i>Lanjut Belanja
                            </a>
                            <button type="button" class="btn btn-primary rounded-pill py-2 fw-semibold" onclick="checkoutSelected()">
                                <i class="fas fa-cart-arrow-down me-2"></i>Checkout ({{\{ selectedCount || 0 \}\}})
                            </button>
                        </div>
                        <p class="text-muted small text-center mt-3 mb-0">Total final termasuk ongkir akan ditampilkan di halaman checkout.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Initialize selected count display
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.checkout-checkbox');
        const checkoutBtn = document.querySelector('button[onclick="checkoutSelected()"]');
        
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.checkout-checkbox:checked').length;
            if (checkoutBtn) {
                checkoutBtn.innerHTML = `<i class="fas fa-cart-arrow-down me-2"></i>Checkout (${checked})`;
            }
        }

        // Toggle individual checkbox
        document.querySelectorAll('.checkout-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const checkbox = document.getElementById('checkbox-' + id);
                checkbox.checked = !checkbox.checked;
                
                if (checkbox.checked) {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    this.innerHTML = '<i class="fa fa-check-circle me-1"></i>Dipilih';
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                    this.innerHTML = '<i class="fa fa-square me-1"></i>Pilih';
                }
                updateSelectedCount();
            });
        });

        // Select all header
        const selectAllHeader = document.getElementById('selectAllHeader');
        if (selectAllHeader) {
            selectAllHeader.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    const btn = document.querySelector(`.checkout-btn[data-id="${cb.value}"]`);
                    if (btn) {
                        btn.classList.toggle('btn-primary', this.checked);
                        btn.classList.toggle('btn-outline-primary', !this.checked);
                        btn.innerHTML = this.checked ? '<i class="fa fa-check-circle me-1"></i>Dipilih' : '<i class="fa fa-square me-1"></i>Pilih';
                    }
                });
                updateSelectedCount();
            });
        }

        // Select all button
        const selectAllBtn = document.getElementById('selectAllBtn');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => {
                    cb.checked = !allChecked;
                    const btn = document.querySelector(`.checkout-btn[data-id="${cb.value}"]`);
                    if (btn) {
                        btn.classList.toggle('btn-primary', !allChecked);
                        btn.classList.toggle('btn-outline-primary', allChecked);
                        btn.innerHTML = !allChecked ? '<i class="fa fa-check-circle me-1"></i>Dipilih' : '<i class="fa fa-square me-1"></i>Pilih';
                    }
                });
                this.innerHTML = allChecked ? '<i class="fas fa-square me-1"></i>Batal Pilih' : '<i class="fas fa-check-square me-1"></i>Pilih Semua';
                updateSelectedCount();
            });
        }

        updateSelectedCount();
    });
</script>
@endsection