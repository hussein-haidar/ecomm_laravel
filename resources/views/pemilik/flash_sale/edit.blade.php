@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-3"></div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header"></div>

                <div class="box-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pemilik_data.update_flash_sale', $flash_sale->id_flash_sale) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nama_flash_sale">Nama Flash Sale</label>
                            <input type="text" id="nama_flash_sale" name="nama_flash_sale" class="form-control"
                                placeholder="Masukkan Nama Flash Sale"
                                value="{{ old('nama_flash_sale', $flash_sale->nama_flash_sale) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="id_stok">Produk (Stok)</label>
                            <select id="id_stok" name="id_stok" class="form-control" required>
                                <option value="">--Pilih Produk--</option>
                                @foreach ($data_stok as $stok)
                                    <option value="{{ $stok->id_stok }}"
                                        data-harga="{{ $stok->harga_produk }}"
                                        {{ old('id_stok', $item->id_stok ?? '') == $stok->id_stok ? 'selected' : '' }}>
                                        {{ $stok->nama_produk }} ({{ $stok->ukuran_produk }}) - Stok: {{ $stok->jumlah_stok_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Harga Normal</label>
                            <input type="text" id="harga_normal_display" class="form-control" readonly
                                value="{{ isset($item->harga_normal) ? 'Rp. ' . number_format($item->harga_normal, 0, ',', '.') : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="harga_flash_sale">Harga Flash Sale</label>
                            <input type="number" id="harga_flash_sale" name="harga_flash_sale" class="form-control"
                                placeholder="Masukkan Harga Flash Sale"
                                value="{{ old('harga_flash_sale', $item->harga_flash_sale ?? '') }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_stok">Kuota Flash Sale</label>
                            <input type="number" id="jumlah_stok" name="jumlah_stok" class="form-control"
                                placeholder="Masukkan Jumlah Kuota"
                                value="{{ old('jumlah_stok', $item->kuota ?? '') }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai</label>
                            <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="form-control"
                                value="{{ old('waktu_mulai', \Carbon\Carbon::parse($flash_sale->waktu_mulai)->format('Y-m-d\TH:i')) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_selesai">Waktu Selesai</label>
                            <input type="datetime-local" id="waktu_selesai" name="waktu_selesai" class="form-control"
                                value="{{ old('waktu_selesai', \Carbon\Carbon::parse($flash_sale->waktu_selesai)->format('Y-m-d\TH:i')) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="Aktif" {{ old('status', $flash_sale->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-aktif" {{ old('status', $flash_sale->status) == 'Non-aktif' ? 'selected' : '' }}>Non-aktif</option>
                            </select>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            <a href="{{ route('pemilik_data.flash_sale') }}" class="btn btn-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>

    <script>
        function updateHargaNormal() {
            const select = document.getElementById('id_stok');
            const opt = select.options[select.selectedIndex];
            const harga = opt ? opt.getAttribute('data-harga') : null;
            document.getElementById('harga_normal_display').value =
                harga ? 'Rp. ' + Number(harga).toLocaleString('id-ID') : '';
        }
        document.getElementById('id_stok').addEventListener('change', updateHargaNormal);
    </script>
@endsection
