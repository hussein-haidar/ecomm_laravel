@extends('layouts.app')
@section('content')

    <main class="product-section">
        <div class="container-product">

            <!-- Judul + Ringkasan -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h3 class="text-title mb-0">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>Keranjang Produk
                </h3>
                @if ($jumlah_item > 0)
                    <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                        {{ $jumlah_item }} produk di keranjang
                    </span>
                @endif
            </div>

            <!-- Search Box -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-3">
                    <form method="GET" class="row g-2">
                        <div class="col-md-5 col-lg-6">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama produk atau ukuran..." value="{{ request('search') }}">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route('pelanggan_data.cart') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            @if ($keranjang->isEmpty())
                <!-- Keranjang Kosong -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Keranjang Anda Kosong</h5>
                        <p class="text-muted mb-4">Belum ada produk di keranjang, ayo mulai belanja.</p>
                        <a href="{{ url('home_toko/katalog') }}" class="btn btn-primary">
                            <i class="fas fa-store me-1"></i>Mulai Belanja
                        </a>
                    </div>
                </div>
            @else
                <!-- Tabel Keranjang -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:280px;">Produk</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th>Ukuran</th>
                                        <th>Jumlah</th>
                                        <th class="text-end">Total</th>
                                        <th style="min-width:120px;">Waktu</th>
                                        <th class="text-center" style="min-width:130px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                    @endphp
                                    @foreach ($keranjang as $value)
                                        @php
                                            $tanggal = strtotime($value->waktu_ditambahkan);
                                            $formattedTanggal = date('d', $tanggal) . ' ' . $bulan[date('n', $tanggal)] . ' ' . date('Y', $tanggal);
                                            $formattedWaktu = date('H:i', $tanggal);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ asset('fotoproduk/' . $value->foto_produk) }}"
                                                        class="rounded border" width="70" height="70"
                                                        style="object-fit: cover;">
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $value->nama_produk }}</div>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-store me-1"></i>{{ $value->nama_toko }}
                                                        </div>
                                                        <span class="badge bg-success bg-opacity-10 text-success mt-1">
                                                            <i class="fas fa-boxes me-1"></i>Stok:
                                                            {{ $value->jumlah_stok_produk }} {{ $value->satuan_produk }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end text-nowrap">
                                                Rp. {{ number_format($value->harga_produk, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <select name="ukuran_produk" form="update-cart-{{ $value->id_keranjang }}"
                                                    class="form-select form-select-sm" style="min-width:110px;"
                                                    onchange="this.form.submit()">
                                                    <option value="">--Pilih Ukuran--</option>
                                                    @foreach ($value->ukuran_list as $ukuran_produk)
                                                        <option value="{{ trim($ukuran_produk) }}"
                                                            {{ $value->ukuran_produk == trim($ukuran_produk) ? 'selected' : '' }}>
                                                            {{ trim($ukuran_produk) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2" style="max-width:150px;">
                                                    <input type="number" name="jumlah_produk"
                                                        form="update-cart-{{ $value->id_keranjang }}"
                                                        class="form-control form-control-sm text-center" min="1"
                                                        max="{{ $value->jumlah_stok_produk }}"
                                                        value="{{ $value->jumlah_produk }}"
                                                        onchange="this.form.submit()">
                                                    <span class="text-muted small text-nowrap">{{ $value->satuan_produk }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end text-nowrap fw-semibold text-success">
                                                Rp. {{ number_format($value->total_harga, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <div class="text-nowrap small">
                                                    <i class="fas fa-calendar-alt text-muted me-1"></i>{{ $formattedTanggal }}<br>
                                                    <span class="text-muted"><i class="fas fa-clock me-1"></i>{{ $formattedWaktu }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column gap-1">
                                                    <input type="checkbox" class="checkout-checkbox d-none"
                                                        value="{{ $value->id_keranjang }}"
                                                        id="checkbox-{{ $value->id_keranjang }}">
                                                    <button type="button" class="btn btn-sm btn-outline-primary checkout-btn"
                                                        data-id="{{ $value->id_keranjang }}">
                                                        <i class="fa fa-check-circle me-1"></i>Pilih
                                                    </button>
                                                    <a href="javascript:void(0)"
                                                        onclick="window.bukaChat && bukaChat('{{ $value->nama_toko }}', '{{ $value->id_stok }}')"
                                                        class="btn btn-sm btn-info text-white"
                                                        title="Chat Penjual {{ $value->nama_toko }}">
                                                        <i class="fas fa-comments me-1"></i>Chat Penjual
                                                    </a>
                                                    <form id="delete-form-{{ $value->id_keranjang }}"
                                                        action="{{ route('pelanggan_data.deleteCart', $value->id_keranjang) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger w-100 btn-delete"
                                                            data-id="{{ $value->id_keranjang }}"
                                                            data-nama="{{ $value->nama_produk }}">
                                                            <i class="fa fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Form Update Keranjang (valid HTML5: input dihubungkan via atribut "form") -->
                                        <form id="update-cart-{{ $value->id_keranjang }}"
                                            action="{{ route('pelanggan_data.updateCart', ['id_keranjang' => $value->id_keranjang]) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            <input type="hidden" name="id_keranjang" value="{{ $value->id_keranjang }}">
                                        </form>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        {{ $keranjang->appends(request()->query())->links() }}
                    </div>
                </div>

                <!-- Ringkasan Total & Checkout -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <div class="text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Pilih produk yang ingin dibeli
                                    (tombol <strong>Pilih</strong>), lalu tekan <strong>Checkout</strong>.
                                </div>
                                <h4 class="mb-0">
                                    Total Harga ({{ $jumlah_item }} produk):
                                    <span class="text-success fw-bold">Rp. {{ number_format($total_semua, 0, ',', '.') }}</span>
                                </h4>
                            </div>
                            <div class="col-md-5">
                                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                    <a href="{{ url('home_toko/katalog') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-cart-plus me-1"></i>Kembali Belanja
                                    </a>
                                    <button class="btn btn-success" onclick="checkoutSelected()">
                                        <i class="fas fa-cart-arrow-down me-1"></i>Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

@endsection

<!-- JavaScript untuk button check -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle checkbox dan ganti warna tombol
            document.querySelectorAll('.checkout-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let checkbox = document.getElementById('checkbox-' + id);
                    checkbox.checked = !checkbox.checked;

                    if (checkbox.checked) {
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                    } else {
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-outline-primary');
                    }
                });
            });
        });

        function checkoutSelected() {
            let selectedProducts = [];
            document.querySelectorAll(".checkout-checkbox:checked").forEach((checkbox) => {
                selectedProducts.push(checkbox.value);
            });

            if (selectedProducts.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Silahkan pilih produk yang ingin di-checkout.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Tampilkan konfirmasi SweetAlert
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
                    // Buat form dinamis
                    let form = document.createElement("form");
                    form.method = "GET";
                    form.action = "{{ route('pelanggan_data.beli') }}";

                    // Tambahkan CSRF Token sebagai input hidden
                    let csrfToken = document.createElement("input");
                    csrfToken.type = "hidden";
                    csrfToken.name = "_token";
                    csrfToken.value = "{{ csrf_token() }}"; // Ini akan dirender oleh Blade
                    form.appendChild(csrfToken);

                    // Tambahkan produk yang dipilih
                    selectedProducts.forEach((id) => {
                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "selected_products[]";
                        input.value = id;
                        form.appendChild(input);
                    });

                    // Tambahkan form ke body lalu submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
