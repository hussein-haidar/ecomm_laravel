@extends('layouts.template')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $chatItems = collect($daftar_chat ?? $chat_list ?? []);
    @endphp

    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-comments"></i> Percakapan Chat Pembeli</h3>
        </div>

        <div class="box-body">
            @if (session('pesan'))
                <div class="alert alert-success" role="alert">
                    {{ session('pesan') }}
                </div>
            @endif

            @if ($chatItems->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fa fa-inbox"></i> Belum ada percakapan chat. Ketika pembeli mengirim pesan, percakapannya
                    akan muncul di sini.
                </div>
            @else
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Pesan Terakhir</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chatItems as $index => $chat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ data_get($chat, 'nama_pelanggan', '-') }}</td>
                                    <td>{{ data_get($chat, 'nama_produk', 'Produk umum') ?? 'Produk umum' }}</td>
                                    <td>
                                        @php
                                            $belumBaca = (int) data_get($chat, 'jumlah_belum_baca', 0);
                                            $lastPengirim = data_get($chat, 'pengirim');
                                            $lastPesan = data_get($chat, 'pesan', 'Belum ada pesan.');
                                            $namaPengirim = $lastPengirim === 'admin'
                                                ? 'Admin'
                                                : data_get($chat, 'nama_pengirim', 'Pelanggan');
                                            $badgeClass = $lastPengirim === 'admin' ? 'bg-success' : 'bg-primary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} me-1">{{ $namaPengirim }}</span>
                                        {{ $lastPesan }}
                                        @if ($belumBaca > 0)
                                            <span class="badge bg-danger ms-1">{{ $belumBaca }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $lastWaktu = data_get($chat, 'last_waktu');
                                        @endphp
                                        {{ $lastWaktu ? Carbon::parse($lastWaktu)->format('Y-m-d H:i:s') : '-' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin_data.chatDetail', data_get($chat, 'id_chat')) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-comments"></i> Buka Chat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
