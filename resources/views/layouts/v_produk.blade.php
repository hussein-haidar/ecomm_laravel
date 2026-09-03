@foreach ($produk_data as $produk)
    <div class="product-item">
        <div class="product-list-image-container">
            <img src="{{ asset('fotoproduk/' . $produk['foto_produk']) }}" alt="{{ $produk['nama_produk'] }}">
        </div>
        <h4>{{ $produk['nama_produk'] }}</h4>
        <p class="produk product-rating mb-0">
            @for ($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= round($produk['rata_rating'] ?? 0) ? 'fas' : 'far' }} fa-star text-warning"></i>
            @endfor
            <span class="text-muted">({{ number_format($produk['rata_rating'] ?? 0, 1, ',', '.') }} - {{ $produk['jumlah_ulasan'] ?? 0 }} Ulasan)</span>
        </p>
        <p class="produk"><strong>Size:</strong> {{ $produk['ukuran_produk'] }}</p>
        <p class="produk">
            <strong>Stock:</strong>
            <span id="stok-{{ $produk['id_stok'] }}">
                {{ $produk['jumlah_stok_produk'] }}
            </span>
            {{ $produk['satuan_produk'] }}
        </p>
        <p class="produk">
            <strong>Price:</strong> Rp. {{ number_format($produk['harga_produk'], 0, ',', '.') }}
        </p>
        <p class="produk">
            <strong>Store:</strong>
            <a href="{{ url('home_toko/view_toko/' . $produk['nama_toko']) }}" target="_blank"
                style="color: black; text-decoration: none;">
                {{ $produk['nama_toko'] }}
            </a>
        </p>
        <a href="{{ url('home_toko/detail_produk/' . $produk['nama_produk']) }}" class="btn btn-detail">
            <i class="fa fa-fw fa-eye"></i>&nbsp;Detail
        </a>

        @if ($user_logged_in)
            <a href="javascript:void(0)" class="btn btn-chat"
                onclick="window.bukaChat && bukaChat('{{ $produk['nama_toko'] }}', '{{ $produk['id_stok'] }}')"
                title="Chat Penjual {{ $produk['nama_toko'] }}">
                <i class="fas fa-comments"></i>&nbsp;Chat Penjual
            </a>
        @else
            <a href="{{ url('auth/login_pelanggan') }}" class="btn btn-chat"
                title="Chat Penjual {{ $produk['nama_toko'] }}">
                <i class="fas fa-comments"></i>&nbsp;Chat Penjual
            </a>
        @endif

        @if ($user_logged_in)
            <form action="{{ url('pelanggan_data/add_to_cart') }}" method="POST" id="product-form-{{ $produk['id_stok'] }}">
                @csrf
                <div class="form-cart-wrapper">
                    <input type="hidden" name="id_stok" value="{{ $produk['id_stok'] }}">
                    <input type="hidden" name="nama_produk" value="{{ $produk['nama_produk'] }}">
                    <input type="hidden" name="satuan_produk" value="{{ $produk['satuan_produk'] }}">
                    <input type="hidden" name="harga_produk" value="{{ $produk['harga_produk'] }}">
                    <input type="hidden" name="berat_produk" value="{{ $produk['berat_produk'] }}">
                    <input type="hidden" name="satuan_berat" value="{{ $produk['satuan_berat'] }}">
                    <input type="hidden" name="sesi_user" value="{{ $produk['sesi_user'] }}">
                    <input type="hidden" name="nama_toko" value="{{ $produk['nama_toko'] }}">

                    <label for="ukuran_produk_{{ $produk['id_stok'] }}" class="form-label">Ukuran Produk:</label>
                    <select id="ukuran_produk_{{ $produk['id_stok'] }}" name="ukuran_produk" class="" required>
                        <option value="">Pilih Ukuran</option>
                        @foreach ($produk['ukuran_list'] as $ukuran)
                            <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                        @endforeach
                    </select>

                    <div class="form-cart-row">
                        <label for="jumlah_produk_{{ $produk['id_stok'] }}" class="form-label">Qty:</label>
                        <div class="qty-control">
                            <button type="button" class="btn-qty" onclick="updateQty('{{ $produk['id_stok'] }}', -1)">-</button>
                            <input type="number" id="jumlah_produk_{{ $produk['id_stok'] }}" name="jumlah_produk"
                                   min="1" value="1" max="{{ $produk['jumlah_stok_produk'] }}"
                                   class="form-input-qty" oninput="validateQtyUI('{{ $produk['id_stok'] }}')">
                            <button type="button" class="btn-qty" onclick="updateQty('{{ $produk['id_stok'] }}', 1)">+</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-cart" formaction="{{ url('pelanggan_data/add_to_cart') }}">
                        <i class="fas fa-shopping-cart"></i>&nbsp;Keranjang
                    </button>

                    <button type="button" class="btn btn-buy" onclick="beliLangsung({{ $produk['id_stok'] }})">
                        <i class="fa fa-fw fa-check"></i>&nbsp;Beli Sekarang
                    </button>
                </div>
            </form>
        @else
            <!-- Untuk tamu: tetap pakai form POST -->
            <form method="POST" action="{{ route('pelanggan_data.beliLangsung', ['id_stok' => $produk['id_stok']]) }}">
                @csrf
                <div class="form-cart-wrapper">
                    <input type="hidden" name="id_stok" value="{{ $produk['id_stok'] }}">
                    <input type="hidden" name="nama_produk" value="{{ $produk['nama_produk'] }}">
                    <input type="hidden" name="satuan_produk" value="{{ $produk['satuan_produk'] }}">
                    <input type="hidden" name="harga_produk" value="{{ $produk['harga_produk'] }}">
                    <input type="hidden" name="berat_produk" value="{{ $produk['berat_produk'] }}">
                    <input type="hidden" name="satuan_berat" value="{{ $produk['satuan_berat'] }}">
                    <input type="hidden" name="sesi_user" value="{{ $produk['sesi_user'] }}">
                    <input type="hidden" name="nama_toko" value="{{ $produk['nama_toko'] }}">

                    <label for="ukuran_produk_{{ $produk['id_stok'] }}" class="form-label">Ukuran Produk:</label>
                    <select name="ukuran_produk" required>
                        <option value="">Pilih Ukuran</option>
                        @foreach ($produk['ukuran_list'] as $ukuran)
                            <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                        @endforeach
                    </select>

                    <div class="form-cart-row">
                        <label class="form-label">Qty:</label>
                        <input type="number" name="jumlah_produk" min="1" value="1" max="{{ $produk['jumlah_stok_produk'] }}" class="form-input-qty">
                    </div>

                    <button type="submit" class="btn btn-cart" formaction="{{ url('pelanggan_data/add_to_cart') }}">
                        <i class="fas fa-shopping-cart"></i>&nbsp;Keranjang
                    </button>
                    <button type="submit" class="btn btn-buy">
                        <i class="fa fa-fw fa-check"></i>&nbsp;Beli Sekarang
                    </button>
                </div>
            </form>
        @endif
    </div>
@endforeach

<!-- ✅ JavaScript yang Diperbaiki -->
<script>
    function updateQty(idStok, delta) {
        const input = document.getElementById('jumlah_produk_' + idStok);
        if (!input) return;
        let val = parseInt(input.value) || 1;
        val = Math.max(1, Math.min(val + delta, parseInt(input.max)));
        input.value = val;
        // ❌ TIDAK ADA .submit() → hanya update UI
    }

    function validateQtyUI(idStok) {
        const input = document.getElementById('jumlah_produk_' + idStok);
        if (!input) return;
        let val = parseInt(input.value) || 1;
        const max = parseInt(input.max);
        if (val < 1) input.value = 1;
        else if (val > max) input.value = max;
        // ❌ TIDAK ADA .submit()
    }

    function beliLangsung(idStok) {
        const form = document.getElementById('product-form-' + idStok);
        if (!form) return alert('Form tidak ditemukan.');

        const ukuran = form.querySelector('select[name="ukuran_produk"]')?.value;
        const jumlah = form.querySelector('input[name="jumlah_produk"]')?.value || 1;
        const namaToko = form.querySelector('input[name="nama_toko"]')?.value || '';

        if (!ukuran) return alert('Silakan pilih ukuran produk terlebih dahulu.');

        const urlTemplate = "{{ route('pelanggan_data.beliLangsung', ['id_stok' => '__ID__']) }}";
        const url = urlTemplate.replace('__ID__', idStok);

        const params = new URLSearchParams({
            ukuran_produk: ukuran,
            jumlah_produk: jumlah,
            nama_toko: namaToko
        });

        window.location.href = url + '?' + params.toString();
    }
</script>

 <script>
    function muatStok() {
        fetch('{{ route('home_toko.ambilStok') }}') // atau '{{ url('home_toko/ambilStok') }}' jika tidak pakai named route
            .then(response => response.json())
            .then(data => {
                data.forEach(produk => {
                    const stokElement = document.getElementById('stok-' + produk.id_stok);
                    if (stokElement && stokElement.textContent != produk.jumlah_stok_produk) {
                        stokElement.textContent = produk.jumlah_stok_produk;
                    }
                });
            })
            .catch(err => {
                console.error("Gagal memuat stok:", err);
            });
    }

    // Jalankan saat halaman pertama kali dimuat
    muatStok();

    // Auto-refresh setiap 1 detik (1000 ms — bukan 10000)
    setInterval(muatStok, 1000);
</script>