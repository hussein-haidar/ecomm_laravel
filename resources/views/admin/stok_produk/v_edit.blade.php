@extends('layouts.template')  

@section('content')  

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="box">
            <div class="box-header">
            </div>

            <div class="box-body">
                <!-- alert error -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin_data.update_stok', ['id_stok' => $data_stok->id_stok]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST') <!-- Untuk method PUT sesuai dengan RESTful -->

                    <input type="hidden" name="sesi_user" value="{{session('sesi_user') }}" class="form-control" readonly>

                    <div class="form-group">
                        <label for="kode_stok">Kode Stok</label>
                        <input type="text" class="form-control" id="kode_stok" name="kode_stok" value="{{ $data_stok->kode_stok }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Waktu Masuk Stok</label>
                        <input type="datetime-local" name="tanggal_masuk_produk" value="{{ $data_stok->tanggal_masuk_produk }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <select name="nama_produk" id="nama_produk" class="form-control" onchange="updateProdukDetails()">
                            <option value="">--Pilih Produk--</option>
                            @foreach ($data_produk as $value)
                                <option value="{{ $value->nama_produk }}"
                                    data-harga="{{ $value->harga_produk }}"
                                    data-berat="{{ $value->berat_produk }}"
                                    data-satuan-berat="{{ $value->satuan_berat }}"
                                    data-jenis="{{ $value->jenis_produk }}"
                                    data-ukuran="{{ $value->ukuran_produk }}"
                                    {{ ($data_stok->nama_produk == $value->nama_produk) ? 'selected' : '' }}>
                                    {{ $value->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jenis Produk</label>
                        <input type="text" id="jenis_produk" name="jenis_produk" value="{{ $data_stok->jenis_produk }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="harga_produk">Harga Produk</label>
                        <input type="text" id="harga_produk" name="harga_produk" value="{{ $data_stok->harga_produk }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="ukuran_produk">Ukuran Produk</label>
                        <input type="text" id="ukuran_produk" name="ukuran_produk" value="{{ $data_stok->ukuran_produk }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="berat_produk">Berat Produk</label>
                        <input type="text" id="berat_produk" name="berat_produk" value="{{ $data_stok->berat_produk }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="satuan_berat">Satuan Berat</label>
                        <input type="text" id="satuan_berat" name="satuan_berat" value="{{ $data_stok->satuan_berat }}" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_stok_produk">Jumlah Stok Produk</label>
                        <input type="number" name="jumlah_stok_produk" value="{{ $data_stok->jumlah_stok_produk }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Satuan Produk</label>
                        <select id="satuan_produk" name="satuan_produk" class="form-control">
                            <option value="">--Pilih Satuan--</option>
                            @foreach ($data_satuan_produk as $value)
                                <option value="{{ $value->satuan_produk }}" {{  $data_stok->satuan_produk == $value->satuan_produk ? 'selected' : '' }}>
                                    {{ $value->satuan_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Kebijakan Retur (S&K)</label><br>
                        <label class="checkbox-inline">
                            <input type="checkbox" name="boleh_retur" value="1" {{ $data_stok->boleh_retur ? 'checked' : '' }}>
                            Produk boleh diretur
                        </label>
                        <small class="help-block">Kosongkan (tidak dicentang) jika produk tidak boleh diretur
                            (custom/personalisasi, higiene, digital/voucher, promo/flash sale) kecuali rusak/cacat
                            atau salah/keliru.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('admin_data.stok') }}" class="btn btn-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const namaProduk = document.getElementById('nama_produk');
        const kodeStokInput = document.getElementById('kode_stok');

        // URL endpoint untuk fetch data
        const kodeStokUrl = "{{ route('admin_data.generateKodeStok') }}";

        function fetchKodeStok() {
            const produk = namaProduk.value;

            if (!produk) {
                console.warn("Nama produk kosong");
                return;
            }

            // Buat query params (bisa dikembangkan untuk tambahan parameter)
            const params = new URLSearchParams({
                nama_produk: produk
            });

            fetch(`${kodeStokUrl}?${params}`)
                .then(response => {
                    if (!response.ok) throw new Error("HTTP error " + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.kode_stok) {
                        kodeStokInput.value = data.kode_stok;
                    } else {
                        console.warn("Gagal mendapatkan kode stok:", data.message || "Tidak ditemukan");
                        kodeStokInput.value = "";
                    }
                })
                .catch(error => {
                    console.error("Error fetching kode stok:", error);
                    kodeStokInput.value = "";
                });
        }

        // Event listener untuk update kode stok saat pilih produk
        namaProduk.addEventListener('change', () => {
            fetchKodeStok();
            updateProdukDetails();
        });

        // Trigger awal jika produk sudah dipilih (misal saat edit)
        if (namaProduk.value) {
            fetchKodeStok();
        }
    });

    // Fungsi: Update detail produk (harga, berat, dll) dari dropdown
    function updateProdukDetails() {
        const select = document.getElementById("nama_produk");
        const selectedOption = select.options[select.selectedIndex];

        const harga = selectedOption.getAttribute("data-harga");
        const berat = selectedOption.getAttribute("data-berat");
        const satuanBerat = selectedOption.getAttribute("data-satuan-berat");
        const jenisProduk = selectedOption.getAttribute("data-jenis");
        const ukuran = selectedOption.getAttribute("data-ukuran");

        document.getElementById("harga_produk").value = harga ?? "";
        document.getElementById("berat_produk").value = berat ?? "";
        document.getElementById("satuan_berat").value = satuanBerat ?? "";
        document.getElementById("jenis_produk").value = jenisProduk ?? "";
        document.getElementById("ukuran_produk").value = ukuran ?? "";
    }
</script>
@endsection  