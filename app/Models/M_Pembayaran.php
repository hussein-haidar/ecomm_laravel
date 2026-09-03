<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Support\Uploads;

class M_Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bayar';
    public $timestamps = false;

    protected $fillable = [
        'sesi_user',
        'id_pelanggan',
        'nama_pelanggan',
        'bank_tujuan',
        'no_rek',
        'total_harga',
        'ongkir',
        'kode_kupon',
        'diskon',
        'total_bayar',
        'a_n',
        'foto_bayar',
        'waktu_pembayaran',
        'batas_waktu_bayar',
        'status_bayar',
        'alasan_batal',
        'midtrans_order_id',
        'midtrans_qr_string',
        'midtrans_qr_url',
        'midtrans_status',
        'midtrans_response',
    ];

    // Ambil semua pembayaran berdasarkan sesi user
    public static function get_bayar_by_sesi()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('pembayaran')
            ->select(
                'pembayaran.*',
                'produk.nama_produk',
                'produk.foto_produk',
                'produk.harga_produk',
                'pembelian.jumlah_produk',      // ✅ Ambil jumlah_produk dari pembelian
                'pembelian.satuan_produk'
            )
            ->leftJoin('pembelian', 'pembelian.nama_pelanggan', '=', 'pembayaran.nama_pelanggan')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk') // ✅ Join berdasarkan id_produk
            ->where('pembayaran.sesi_user', $sesi_user)
            ->orderByDesc('pembayaran.id_bayar')
            ->get()
            ->toArray();
    }

    // Ambil pembayaran dengan status Belum Bayar / Dibatalkan
    public static function get_bayar_by_status()
    {
        $id_pelanggan = Session::get('id_pelanggan');

        return DB::table('pembayaran')
            ->where('id_pelanggan', $id_pelanggan)
            ->whereIn('status_bayar', ['Belum Bayar', 'Dibatalkan'])
            ->orderByDesc('id_bayar')
            ->get()
            ->toArray();
    }

    // Tambahkan di dalam class Pembayaran
    public static function get_status_bayar_sesi($sesi_user, $nama_pelanggan)
    {
        return DB::table('pembayaran')
        ->where('sesi_user', $sesi_user)
            ->where('nama_pelanggan', $nama_pelanggan)
            ->where('status_bayar', 'Dibayar')
            ->orderBy('id_bayar', 'DESC')
            ->get(); // mengembalikan Collection
    }

    public static function detailBayar($id_bayar)
    {
        return DB::table('pembayaran')
            ->select(
                'pembayaran.*',
                'pembelian.*',
            )
            ->leftJoin('pembelian', 'pembelian.nama_pelanggan', '=', 'pembayaran.nama_pelanggan')
            ->where('pembayaran.id_bayar', $id_bayar)
            ->first();
    }

    public static function konfirmasi_pembayaran($id_bayar, $nama_pelanggan)
    {
        // 1. Update pembelian
        DB::table('pembelian')
            ->where('nama_pelanggan', $nama_pelanggan)
            ->update(['status_beli' => 'Berhasil']);

        // 2. Update pembayaran
        DB::table('pembayaran')
            ->where('id_bayar', $id_bayar)
            ->update([
                'waktu_pembayaran' => now(),
                'status_bayar' => 'Dibayar',
            ]);

        // 3. Ambil data ekspedisi berdasarkan id_bayar (lebih aman)
        $ekspedisi = DB::table('ekspedisi')
            ->where('id_bayar', $id_bayar)
            ->first();

        // Gunakan waktu pembayaran sebagai dasar estimasi
        $waktu_dasar = now();
        $estimasi_waktu_tiba = $waktu_dasar->copy();

        if ($ekspedisi && !empty(trim($ekspedisi->estimasi_waktu))) {
            $estimasi = trim($ekspedisi->estimasi_waktu);

            if (stripos($estimasi, 'jam') !== false) {
                preg_match_all('/\d+/', $estimasi, $matches);
                $jam = $matches[0] ? max(array_map('intval', $matches[0])) : 1;
                $estimasi_waktu_tiba->addHours($jam);
            } elseif (stripos($estimasi, 'hari') !== false) {
                preg_match_all('/\d+/', $estimasi, $matches);
                $hari = $matches[0] ? max(array_map('intval', $matches[0])) : 1;
                $estimasi_waktu_tiba->addDays($hari);
            }
        }

        // 4. Update ekspedisi
        DB::table('ekspedisi')
            ->where('id_bayar', $id_bayar)
            ->update([
                'estimasi_waktu_tiba' => $estimasi_waktu_tiba->format('Y-m-d H:i:s'),
                'status_kirim' => 'Dibayar',
            ]);

        // 5. Hapus keranjang
        DB::table('keranjang')
            ->where('nama_pelanggan', $nama_pelanggan)
            ->where('status_keranjang', 'Selesai')
            ->delete();
    }

    public static function batalkanTransaksiExpiredByUser($id_pelanggan)
    {
        $now = now();

        // Ambil data pembayaran yang expired untuk pelanggan tertentu
        $expired = DB::table('pembayaran')
            ->leftJoin('pembelian', 'pembelian.id_pelanggan', '=', 'pembayaran.id_pelanggan')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->where('pembayaran.id_pelanggan', $id_pelanggan)
            ->where('pembayaran.batas_waktu_bayar', '<', $now)
            ->where('pembayaran.status_bayar', 'Belum Bayar')
            ->select('pembayaran.*', 'pembelian.nama_produk')
            ->get();

        if ($expired->isEmpty()) {
            return; // Tidak ada transaksi expired, keluar lebih awal
        }

        $stokModel = new M_stok();

        foreach ($expired as $data) {
            $nama_produk = $data->nama_produk;

            // Ambil semua pembelian terkait pelanggan dan produk (status Belum Bayar)
            $pembelianList = DB::table('pembelian')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('nama_produk', $nama_produk)
                ->get();

            // Akumulasi jumlah produk per id_stok
            $stokPerId = [];
            foreach ($pembelianList as $pembelian) {
                if ($pembelian->status_beli == 'Ditunda') {
                    $id_stok = $pembelian->id_stok;
                    $jumlah  = $pembelian->jumlah_produk;

                    if (!isset($stokPerId[$id_stok])) {
                        $stokPerId[$id_stok] = 0;
                    }
                    $stokPerId[$id_stok] += $jumlah;
                }
            }

            // Kembalikan stok
            foreach ($stokPerId as $id_stok => $totalJumlah) {
                $stokModel->tambahStok($id_stok, $totalJumlah);
            }

            // Hapus dari keranjang
            DB::table('keranjang')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('nama_produk', $nama_produk)
                ->where('status_keranjang', 'Selesai')
                ->delete();

            // Hapus pembelian
            DB::table('pembelian')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('nama_produk', $nama_produk)
                ->where('status_beli', 'Ditunda')
                ->delete();

            // Hapus pembayaran
            DB::table('pembayaran')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('status_bayar', 'Belum Bayar')
                ->delete();

            // Ambil semua id_ekspedisi terkait pelanggan sebelum dihapus
            $idEkspedisiList = DB::table('ekspedisi')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('status_kirim', 'Pesanan dibuat')
                ->pluck('id_ekspedisi');

            // Hapus lacak_pesanan berdasarkan id_ekspedisi
            if ($idEkspedisiList->isNotEmpty()) {
                DB::table('lacak_pesanan')
                    ->whereIn('id_ekspedisi', $idEkspedisiList)
                    ->delete();
            }

            // Hapus ekspedisi
            DB::table('ekspedisi')
                ->where('id_pelanggan', $id_pelanggan)
                ->where('status_kirim', 'Pesanan dibuat')
                ->delete();

            // Hapus file QR Code
            Uploads::delete('qr', "qr_{$data->id_bayar}.png");
            Uploads::delete('qr', "qris_{$data->id_bayar}.png");
        }
    }
}
