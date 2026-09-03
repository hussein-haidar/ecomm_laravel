@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-3"></div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header"></div>

                <div class="box-body">
                    <!-- Pop up alert errors -->
                    @if (session('errors'))
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach (session('errors')->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('pemilik_data.update_produk', $produk->id_produk) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="sesi_user" value="{{ session('sesi_user') }}" class="form-control"
                            readonly>

                        <div class="form-group">
                            <label for="kode_produk">Kode Produk</label>
                            <input name="kode_produk" id="kode_produk"
                                value="{{ old('kode_produk', $produk->kode_produk) }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="jenis_produk">Jenis Produk</label>
                            <select id="jenis_produk" name="jenis_produk" class="form-control" required>
                                <option value="">--Pilih Jenis--</option>
                                @foreach ($data_jenis as $item)
                                    <option value="{{ $item->jenis_produk }}"
                                        {{ old('jenis_produk', $produk->jenis_produk) == $item->jenis_produk ? 'selected' : '' }}>
                                        {{ $item->jenis_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="kolom-varian">
                            <label for="varian_produk">Varian Produk</label>
                            <select id="varian_produk" name="varian_produk" class="form-control">
                                <option value="">--Pilih Varian--</option>
                                @foreach ($data_varian as $item)
                                    <option value="{{ $item->varian_produk }}"
                                        {{ old('varian_produk', $produk->varian_produk) == $item->varian_produk ? 'selected' : '' }}>
                                        {{ $item->varian_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nama_produk">Nama Produk</label>
                            <input name="nama_produk" id="nama_produk"
                                value="{{ old('nama_produk', $produk->nama_produk) }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="ukuran_produk">Ukuran Produk</label>
                            <input type="text" id="ukuran_produk" name="ukuran_produk" class="form-control"
                                value="{{ $produk->ukuran_produk }}">
                        </div>

                        <div class="form-group">
                            <label for="berat_produk">Berat Produk</label>
                            <input type="number" id="berat_produk" name="berat_produk" class="form-control"
                                value="{{ $produk->berat_produk }}">
                        </div>

                        <div class="form-group">
                            <label for="satuan_berat">Satuan Berat</label>
                            <select id="satuan_berat" name="satuan_berat" class="form-control">
                                <option value="">--Pilih Satuan--</option>
                                @foreach ($data_satuan_berat as $item)
                                    <option value="{{ $item->satuan_produk }}"
                                        {{ $produk->satuan_berat == $item->satuan_produk ? 'selected' : '' }}>
                                        {{ $item->satuan_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="harga_produk">Harga Produk</label>
                            <input type="text" id="harga_produk" name="harga_produk" class="form-control"
                                placeholder="Masukkan Harga Produk" value="{{ $produk->harga_produk }}" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi_produk">Deskripsi Produk</label>
                            <input type="text" name="deskripsi_produk" class="form-control"
                                placeholder="Masukkan Deskripsi Produk" value="{{ $produk->deskripsi_produk }}}" required>
                        </div>

                        <div class="form-group">
                            <label for="foto_produk">Upload Foto Produk (Utama)</label>
                            <input type="file" class="form-control" name="foto_produk" id="preview_gambar">
                            @if ($produk->foto_produk)
                                <img src="{{ asset('fotoproduk/' . $produk->foto_produk) }}" width="80" class="mt-2">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="gallery_images">Gallery Foto Produk (Multiple, opsional)</label>
                            <input type="file" class="form-control" name="gallery_images[]" id="gallery_images" multiple accept="image/*">
                            <small class="text-muted">Pilih multiple foto untuk gallery (depan, belakang, detail, dll)</small>
                            @if ($produk->gallery_images)
                                <div class="mt-2">
                                    @php $gallery = json_decode($produk->gallery_images, true) ?? []; @endphp
                                    @foreach ($gallery as $img)
                                        <img src="{{ asset('fotoproduk/' . $img) }}" width="60" class="m-1 border">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="size_guide_image">Foto Panduan Ukuran / Size Guide (opsional)</label>
                            <input type="file" class="form-control" name="size_guide_image" id="size_guide_image" accept="image/*">
                            <small class="text-muted">Gambar tabel ukuran (dada, pinggang, panjang, dll)</small>
                            @if ($produk->size_guide_image)
                                <img src="{{ asset('fotoproduk/' . $produk->size_guide_image) }}" width="80" class="mt-2">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="video_url">URL Video Produk (YouTube/Vimeo/MP4 link, opsional)</label>
                            <input type="url" class="form-control" name="video_url" id="video_url" placeholder="https://youtube.com/watch?v=... atau https://example.com/video.mp4" value="{{ $produk->video_url ?? '' }}">
                            <small class="text-muted">Link YouTube, Vimeo, atau direct MP4</small>
                        </div>

                        <div class="mt-3 d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ route('pemilik_data.produk') }}" class="btn btn-primary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const namaJenis = document.getElementById('jenis_produk');
            const namaVarian = document.getElementById('varian_produk');
            const kodeProdukInput = document.getElementById('kode_produk');
            const namaProdukInput = document.getElementById('nama_produk');

            const kodeProdukUrl = "{{ route('pemilik_data.generateKodeProduk') }}";
            const namaProdukUrl = "{{ route('pemilik_data.generateNamaProduk') }}";

            function fetchProdukDetails() {
                const jenis = namaJenis.value;
                const varian = namaVarian.value;

                if (jenis && varian) {
                    const params = new URLSearchParams({
                        jenis_produk: jenis,
                        varian_produk: varian
                    });

                    fetch(`${kodeProdukUrl}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.kode_produk) {
                                kodeProdukInput.value = data.kode_produk;
                            }
                        });

                    fetch(`${namaProdukUrl}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.nama_produk) {
                                namaProdukInput.value = data.nama_produk;
                            }
                        });
                }
            }

            namaJenis.addEventListener('change', fetchProdukDetails);
            namaVarian.addEventListener('change', fetchProdukDetails);
        });
    </script>

@endsection
