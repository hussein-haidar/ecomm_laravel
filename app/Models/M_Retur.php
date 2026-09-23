<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    // Kirim notifikasi in-app + email (auto-refund) saat retur selesai
    public static function notifikasiSelesai($retur)
    {
        $kode = $retur->kode_retur;
        $refundInfo = '';
        if ($retur->tipe_retur === 'pengembalian_dana' && $retur->jumlah_refund > 0) {
            $refundInfo = ' Pengembalian dana Rp ' . number_format($retur->jumlah_refund, 0, ',', '.')
                . ' (' . ($retur->status_refund ?: 'Belum Diproses') . ').';
        }

        // Notifikasi in-app untuk pelanggan
        try {
            M_Notifikasi::kirim([
                'id_pelanggan' => $retur->id_pelanggan,
                'nama_pelanggan' => $retur->nama_pelanggan,
                'judul' => 'Retur Selesai',
                'pesan' => "Retur {$kode} telah selesai diproses." . $refundInfo,
                'tipe' => 'sukses',
                'link' => route('pelanggan_data.detailRetur', $retur->id_retur),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi retur selesai: ' . $e->getMessage());
        }

        // Email auto-refund ke pelanggan
        $emailPelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $retur->id_pelanggan)
            ->value('email');

        if ($emailPelanggan) {
            try {
                Mail::raw(
                    "Halo {$retur->nama_pelanggan},\n\n"
                    . "Retur {$kode} untuk produk \"{$retur->nama_produk}\" telah selesai diproses." . $refundInfo . "\n\n"
                    . "Terima kasih telah berbelanja di toko kami.",
                    function ($message) use ($emailPelanggan, $kode) {
                        $message->to($emailPelanggan)
                            ->subject("Status Retur Selesai - {$kode}");
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim email auto-refund retur: ' . $e->getMessage());
            }
        }
    }
}