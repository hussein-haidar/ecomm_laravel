<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Retur extends Model
{
    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    public $timestamps = true;

    protected $fillable = [
        'kode_retur',
        'id_beli',
        'id_bayar',
        'id_pelanggan',
        'id_stok',
        'nama_pelanggan',
        'nama_toko',
        'sesi_user',
        'nama_produk',
        'ukuran_produk',
        'jumlah_produk',
        'satuan_produk',
        'jenis_alasan',
        'alasan',
        'tipe_retur',
        'foto_bukti',
        'alamat_pengembalian',
        'no_telp',
        'nomor_resi',
        'status',
        'catatan_admin',
        'jumlah_refund',
        'status_refund',
        'waktu_pengajuan',
        'waktu_verifikasi',
        'waktu_selesai',
    ];

    public static function generateKodeRetur()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $prefix = "RTR/{$tahun}/{$bulan}/";

        $last = DB::table('retur')
            ->where('kode_retur', 'like', "{$prefix}%")
            ->orderByDesc('kode_retur')
            ->value('kode_retur');

        if ($last) {
            $nomor = (int) substr($last, -4) + 1;
        } else {
            $nomor = 1;
        }

        return $prefix . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    // Ambil daftar retur milik pelanggan
    public static function getReturPelanggan($idPelanggan, $keyword = '', $statusFilter = '')
    {
        $query = DB::table('retur')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'retur.nama_produk')
            ->select(
                'retur.*',
                'produk.foto_produk'
            )
            ->where('retur.id_pelanggan', $idPelanggan)
            ->when($keyword, function ($q) use ($keyword) {
                return $q->where(function ($sub) use ($keyword) {
                    $sub->where('retur.kode_retur', 'like', "%{$keyword}%")
                        ->orWhere('retur.nama_produk', 'like', "%{$keyword}%");
                });
            })
            ->when($statusFilter, fn ($q) => $q->where('retur.status', $statusFilter))
            ->orderByDesc('retur.id_retur');

        return $query->paginate(10);
    }

    // Ambil pembelian yang berhak diajukan retur (sudah sampai tujuan / diterima, belum pernah retur)
    public static function getPembelianEligibleRetur($idPelanggan)
    {
        $idSudahRetur = DB::table('retur')
            ->where('id_pelanggan', $idPelanggan)
            ->pluck('id_beli')
            ->toArray();

        return DB::table('pembelian')
            ->select(
                'pembelian.*',
                'produk.foto_produk',
                'produk.nama_produk',
                'ekspedisi.status_kirim',
                'ekspedisi.id_ekspedisi'
            )
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->leftJoin('ekspedisi', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            ->where('pembelian.id_pelanggan', $idPelanggan)
            ->where('pembelian.status_beli', 'Dibayar')
            ->whereIn('ekspedisi.status_kirim', ['Sampai tujuan', 'Pesanan diterima'])
            ->when($idSudahRetur, fn ($q) => $q->whereNotIn('pembelian.id_beli', $idSudahRetur))
            ->orderByDesc('pembelian.id_beli')
            ->get();
    }

    // Ambil detail retur beserta pembayaran & ekspedisi terkait
    public static function detailRetur($idRetur, $idPelanggan = null)
    {
        $query = DB::table('retur')
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'retur.id_bayar')
            ->leftJoin('ekspedisi', 'ekspedisi.id_beli', '=', 'retur.id_beli')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'retur.nama_produk')
            ->select(
                'retur.*',
                'pembayaran.total_bayar',
                'pembayaran.ongkir',
                'pembayaran.status_bayar',
                'ekspedisi.kode_resi',
                'produk.foto_produk'
            )
            ->where('retur.id_retur', $idRetur);

        if ($idPelanggan) {
            $query->where('retur.id_pelanggan', $idPelanggan);
        }

        return $query->first();
    }

    // Ambil daftar retur per toko/admin (berdasarkan sesi_user)
    public static function getReturAdmin($sesiUser, $keyword = '', $statusFilter = '')
    {
        $query = DB::table('retur')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'retur.nama_produk')
            ->select(
                'retur.*',
                'produk.foto_produk'
            )
            ->where('retur.sesi_user', $sesiUser)
            ->when($keyword, function ($q) use ($keyword) {
                return $q->where(function ($sub) use ($keyword) {
                    $sub->where('retur.kode_retur', 'like', "%{$keyword}%")
                        ->orWhere('retur.nama_produk', 'like', "%{$keyword}%")
                        ->orWhere('retur.nama_pelanggan', 'like', "%{$keyword}%");
                });
            })
            ->when($statusFilter, fn ($q) => $q->where('retur.status', $statusFilter))
            ->orderByDesc('retur.id_retur');

        return $query->paginate(10);
    }

    // Hitung retur aktif per toko
    public static function countReturAktif($sesiUser)
    {
        return DB::table('retur')
            ->where('sesi_user', $sesiUser)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->count();
    }
}