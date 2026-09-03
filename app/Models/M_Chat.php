<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_Chat extends Model
{
    protected $table = 'chat_transaksi';
    protected $primaryKey = 'id_chat';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'id_user',
        'sesi_user',
        'nama_toko',
        'id_stok',
        'nama_produk',
        'pengirim',
        'nama_pengirim',
        'pesan',
        'last_waktu',
        'created_at',
    ];

    protected static function kueriKonversi($idPelanggan, $idUser, $namaToko, $idStok)
    {
        $query = self::where('id_pelanggan', $idPelanggan)
            ->where('id_user', $idUser)
            ->where('nama_toko', $namaToko);

        if (empty($idStok)) {
            $query->whereNull('id_stok');
        } else {
            $query->where('id_stok', $idStok);
        }

        return $query;
    }

    public static function cariAtauBuat($idPelanggan, $namaPelanggan, $idUser, $sesiUser, $namaToko, $idStok = null, $namaProduk = null)
    {
        $chat = self::kueriKonversi($idPelanggan, $idUser, $namaToko, $idStok)
            ->orderBy('id_chat')
            ->first();

        if (!$chat) {
            $chat = self::create([
                'id_pelanggan' => $idPelanggan,
                'nama_pelanggan' => $namaPelanggan,
                'id_user' => $idUser,
                'sesi_user' => $sesiUser,
                'nama_toko' => $namaToko,
                'id_stok' => $idStok ?: null,
                'nama_produk' => $namaProduk ?: 'Produk umum',
                'pengirim' => 'pelanggan',
                'nama_pengirim' => $namaPelanggan,
                'pesan' => null,
                'last_waktu' => now(),
                'created_at' => now(),
            ]);
        } elseif (empty($chat->sesi_user) && !empty($sesiUser)) {
            $chat->update(['sesi_user' => $sesiUser]);
        }

        return $chat;
    }

    public static function kirimPesan($idChat, $pengirim, $pengirimId, $namaPengirim, $pesan)
    {
        $anchor = self::where('id_chat', $idChat)->first();

        if (!$anchor) {
            return null;
        }

        if (is_null($anchor->pesan)) {
            // Pesan pertama: isi baris awal (anchor) agar id_chat konversi tetap stabil.
            $anchor->update([
                'pengirim' => $pengirim,
                'nama_pengirim' => $namaPengirim,
                'pesan' => $pesan,
                'last_waktu' => now(),
            ]);
        } else {
            // Pesan berikutnya: simpan sebagai baris baru agar riwayat chat tersimpan.
            self::create([
                'id_pelanggan' => $anchor->id_pelanggan,
                'nama_pelanggan' => $anchor->nama_pelanggan,
                'id_user' => $anchor->id_user,
                'sesi_user' => $anchor->sesi_user,
                'nama_toko' => $anchor->nama_toko,
                'id_stok' => $anchor->id_stok,
                'nama_produk' => $anchor->nama_produk,
                'pengirim' => $pengirim,
                'nama_pengirim' => $namaPengirim,
                'pesan' => $pesan,
                'last_waktu' => now(),
                'created_at' => now(),
            ]);
        }

        return null;
    }

    public static function pesanChat($idChat)
    {
        $anchor = self::where('id_chat', $idChat)->first();

        if (!$anchor) {
            return collect();
        }

        return self::kueriKonversi($anchor->id_pelanggan, $anchor->id_user, $anchor->nama_toko, $anchor->id_stok)
            ->whereNotNull('pesan')
            ->orderBy('id_chat')
            ->get();
    }

    public static function daftarChatPelanggan($idPelanggan)
    {
        return self::kelompokkanKonversi(
            self::where('id_pelanggan', $idPelanggan)
                ->whereNotNull('pesan')
                ->orderBy('id_chat')
                ->get()
        );
    }

    public static function daftarChatAdmin($sesiUser)
    {
        return self::kelompokkanKonversi(
            self::where('sesi_user', $sesiUser)
                ->whereNotNull('pesan')
                ->orderBy('id_chat')
                ->get()
        );
    }

    protected static function kelompokkanKonversi($rows)
    {
        $hasil = [];

        $rows->groupBy(function ($baris) {
            return $baris->id_user . '|' . $baris->nama_toko . '|' . ($baris->id_stok ?: '');
        })->each(function ($grup) use (&$hasil) {
            $pertama = $grup->first();
            $terakhir = $grup->last();

            $hasil[] = (object) [
                'id_chat' => $pertama->id_chat,
                'id_pelanggan' => $pertama->id_pelanggan,
                'nama_pelanggan' => $pertama->nama_pelanggan,
                'id_user' => $pertama->id_user,
                'sesi_user' => $pertama->sesi_user,
                'nama_toko' => $pertama->nama_toko,
                'id_stok' => $pertama->id_stok,
                'nama_produk' => $pertama->nama_produk,
                'pengirim' => $terakhir->pengirim,
                'nama_pengirim' => $terakhir->nama_pengirim,
                'pesan' => $terakhir->pesan,
                'last_waktu' => $terakhir->last_waktu,
                'created_at' => $pertama->created_at,
            ];
        });

        return collect($hasil)->sortByDesc('last_waktu')->values();
    }

    public static function hitungBelumDibacaPelanggan($idPelanggan)
    {
        return self::daftarChatPelanggan($idPelanggan)
            ->where('pengirim', 'admin')
            ->count();
    }

    public static function hitungBelumDibacaAdmin($sesiUser)
    {
        return self::daftarChatAdmin($sesiUser)
            ->where('pengirim', 'pelanggan')
            ->count();
    }
}
