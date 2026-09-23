<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Ekspedisi extends Model
{
    protected $table = 'ekspedisi';
    protected $primaryKey = 'id_ekspedisi';
    public $timestamps = false;

    protected $fillable = [
        'nama_toko',
        'id_pelanggan',
        'nama_pelanggan',
        'alamat',
        'ekspedisi',
        'provinsi',
        'kota',
        'paket',
        'ongkir',
        'estimasi',
        'longitude',
        'latitude',
        'jenis_kurir',
        'estimasi_waktu',
        'status_kirim',
        'estimasi_waktu_tiba',
        'waktu_mulai_tahap',
        'id_bayar',
        'id_beli',
        'kode_resi'
    ];

    public static function get_ekspedisi_by_pelanggan($keyword_resi = '')
    {
        $nama_pelanggan = Session::get('nama_pelanggan');

        $query = DB::table('ekspedisi')
            ->select(
                'ekspedisi.*',
                'produk.nama_produk',
                'produk.foto_produk',
                'produk.harga_produk',
                'pembelian.*',
                'pembayaran.total_bayar'
            )
            // 🔹 Pertama: JOIN ke pembelian (karena ekspedisi.id_beli → pembelian.id_beli)
            ->leftJoin('pembelian', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            // 🔹 Kedua: baru JOIN ke produk via pembelian.nama_produk
            ->leftJoin('produk', 'pembelian.nama_produk', '=', 'produk.nama_produk')
            // 🔹 Lalu JOIN ke pembayaran
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'ekspedisi.id_bayar')
            ->where('ekspedisi.nama_pelanggan', $nama_pelanggan);

        // ❗ Juga perbaiki kondisi WHERE — Anda pakai array, tapi harus pakai whereIn atau where
        // Jika maksud Anda: status_kirim = "Sampai tujuan" ATAU "Pesanan diterima"
        $query->whereIn('ekspedisi.status_kirim', ['Sampai tujuan', 'Pesanan diterima']);

        if (!empty($keyword_resi)) {
            $query->where(function ($q) use ($keyword_resi) {
                $q->where('ekspedisi.kode_resi', 'like', "%{$keyword_resi}%")
                    ->orWhere('produk.nama_produk', 'like', "%{$keyword_resi}%")
                    ->orWhere('ekspedisi.status_kirim', 'like', "%{$keyword_resi}%");
            });
        }

        return $query->orderByDesc('ekspedisi.id_ekspedisi')->get();
    }

    public static function get_lacak_by_status()
    {
        $id_pelanggan = Session::get('id_pelanggan');

        return DB::table('ekspedisi')
            ->where('id_pelanggan', $id_pelanggan)
            ->whereNotIn('ekspedisi.status_kirim', ['Pesanan dibuat','Sampai tujuan', 'Pesanan diterima'])
            ->orderByDesc('id_bayar')
            ->get()
            ->toArray();
    }

    public static function get_status_beli_by_status()
    {
        $id_pelanggan = session('id_pelanggan'); // pastikan session tersedia

        return DB::table('pembelian')
            ->select(
                'pembelian.*',
                'produk.*',
                'pembayaran.*',
                'ekspedisi.*',
                'lacak_pesanan.*',
                // tambahkan kolom lain jika perlu
            )
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->leftJoin('ekspedisi', 'ekspedisi.id_beli', '=', 'pembelian.id_beli')
            ->leftJoin('lacak_pesanan', 'lacak_pesanan.id_ekspedisi', '=', 'ekspedisi.id_ekspedisi') // ✅ betulkan di sini!
            ->where('pembelian.status_beli', 'Berhasil')
            ->where('pembelian.id_pelanggan', $id_pelanggan);
    }

    public static function updateStatusOtomatis($id_pelanggan = null, $silent = false)
    {
        $now = Carbon::now();
        $updated = false;

        $pesanan = DB::table('ekspedisi as e')
            ->leftJoin('kurir as k', 'e.jenis_kurir', '=', 'k.jenis_kurir')
            ->leftJoin('lacak_pesanan as lp', 'e.id_ekspedisi', '=', 'lp.id_ekspedisi')
            ->select(
                'e.id_ekspedisi',
                'e.id_pelanggan',
                'e.status_kirim',
                'e.jenis_kurir',
                'e.estimasi_waktu_tiba',
                'e.waktu_mulai_tahap', // 👈 tambahkan ini
                'e.updated_at as waktu_lacak',
                'k.tipe_kurir',
                'lp.waktu_dikemas',
                'lp.waktu_dikirim_toko',
                'lp.waktu_disortir',
                'lp.waktu_dikirim_gudang',
                'lp.waktu_sampai_gudang_tujuan',
                'lp.waktu_diantar_kurir',
                'lp.waktu_tiba_tujuan',
                'lp.waktu_pesanan_diterima'
            )
            ->when($id_pelanggan, fn($query) => $query->where('e.id_pelanggan', $id_pelanggan))
            ->get();

        foreach ($pesanan as $p) {
            // Pastikan lacak_pesanan tersedia
            DB::table('lacak_pesanan')->updateOrInsert(
                ['id_ekspedisi' => $p->id_ekspedisi],
                ['updated_at' => $now]
            );

            // Parse estimasi_waktu_tiba
            $estimasiWaktuTiba = null;
            if (!empty($p->estimasi_waktu_tiba)) {
                try {
                    $estimasiWaktuTiba = Carbon::parse($p->estimasi_waktu_tiba);
                } catch (\Exception $e) {
                    // abaikan
                }
            }

            $tipeKurir = strtolower($p->tipe_kurir ?? 'instan');
            $isReguler = $tipeKurir === 'reguler';
            $isInstant = !$isReguler;

            // === RECOVERY: Pastikan waktu_mulai_tahap valid ===
            $waktuMulai = null;
            if (!empty($p->waktu_mulai_tahap)) {
                try {
                    $waktuMulai = Carbon::parse($p->waktu_mulai_tahap);
                } catch (\Exception $e) {
                    $waktuMulai = null;
                }
            }

            if (!$waktuMulai) {
                // Gunakan waktu_lacak atau now sebagai fallback
                try {
                    $waktuMulai = !empty($p->waktu_lacak) ? Carbon::parse($p->waktu_lacak) : $now;
                } catch (\Exception $e) {
                    $waktuMulai = $now;
                }

                // Simpan kembali ke database
                DB::table('ekspedisi')
                    ->where('id_ekspedisi', $p->id_ekspedisi)
                    ->update(['waktu_mulai_tahap' => $waktuMulai, 'updated_at' => $now]);
            }

            $durasiBerlalu = $waktuMulai->diffInHours($now, false);

            // === RECOVERY WAKTU STATUS (versi diperluas) ===
            $recoveryUpdates = [];

            // Jika status sudah melewati "Dikemas", tapi waktu_dikemas belum diisi → isi dengan fallback
            $afterPackingStatuses = [
                'Dikemas', 'Dikirim dari toko', 'Disortir', 'Dikirim dari gudang',
                'Sampai gudang tujuan', 'Diantar kurir', 'Sampai tujuan', 'Pesanan diterima'
            ];

            if (in_array($p->status_kirim, $afterPackingStatuses) && empty($p->waktu_dikemas)) {
                // Prioritas: gunakan waktu_mulai_tahap jika valid, jika tidak, gunakan now()
                $fallbackTime = $waktuMulai ?? $now;
                $recoveryUpdates['waktu_dikemas'] = $fallbackTime;
            }

            // Recovery untuk status reguler lainnya (seperti sebelumnya, tapi pastikan kondisi benar)
            if ($isReguler) {
                if (in_array($p->status_kirim, ['Disortir', 'Dikirim dari gudang', 'Sampai gudang tujuan', 'Diantar kurir', 'Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_dikirim_toko)) {
                    $recoveryUpdates['waktu_dikirim_toko'] = $waktuMulai ?? $now;
                }
                if (in_array($p->status_kirim, ['Dikirim dari gudang', 'Sampai gudang tujuan', 'Diantar kurir', 'Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_disortir)) {
                    $recoveryUpdates['waktu_disortir'] = $waktuMulai ?? $now;
                }
                if (in_array($p->status_kirim, ['Sampai gudang tujuan', 'Diantar kurir', 'Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_dikirim_gudang)) {
                    $recoveryUpdates['waktu_dikirim_gudang'] = $waktuMulai ?? $now;
                }
                if (in_array($p->status_kirim, ['Diantar kurir', 'Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_sampai_gudang_tujuan)) {
                    $recoveryUpdates['waktu_sampai_gudang_tujuan'] = $waktuMulai ?? $now;
                }
            }

            // Recovery umum untuk kurir instan & reguler
            if (in_array($p->status_kirim, ['Diantar kurir', 'Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_diantar_kurir)) {
                $recoveryUpdates['waktu_diantar_kurir'] = $waktuMulai ?? $now;
            }
            if (in_array($p->status_kirim, ['Sampai tujuan', 'Pesanan diterima']) && empty($p->waktu_tiba_tujuan)) {
                $recoveryUpdates['waktu_tiba_tujuan'] = $waktuMulai ?? $now;
            }
            if ($p->status_kirim === 'Pesanan diterima' && empty($p->waktu_pesanan_diterima)) {
                $recoveryUpdates['waktu_pesanan_diterima'] = $waktuMulai ?? $now;
            }

            // Lakukan update jika ada field yang perlu diperbaiki
            if (!empty($recoveryUpdates)) {
                DB::table('lacak_pesanan')
                    ->where('id_ekspedisi', $p->id_ekspedisi)
                    ->update($recoveryUpdates);
                $updated = true;
            }

            // === LOGIKA FORCE UPDATE JIKA ESTIMASI SUDAH LEBAT ===
            if ($estimasiWaktuTiba && $now->gte($estimasiWaktuTiba)) {
                // Jika belum sampai tujuan, force ke "Sampai tujuan"
                if (in_array($p->status_kirim, [
                    'Dibayar',
                    'Dikemas',
                    'Dikirim dari toko',
                    'Disortir',
                    'Dikirim dari gudang',
                    'Sampai gudang tujuan',
                    'Diantar kurir'
                ])) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Sampai tujuan',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_tiba_tujuan' => $now,
                        'status_keterlambatan' => 'Terlambat',
                        'updated_at' => $now,
                    ]);

                    $updated = true;
                    continue; // skip logic di bawah
                }

                // Jika sudah "Sampai tujuan", lakukan auto-terima setelah durasi
                if ($p->status_kirim === 'Sampai tujuan') {
                    $waktuSampai = null;
                    if (!empty($p->waktu_tiba_tujuan)) {
                        try {
                            $waktuSampai = Carbon::parse($p->waktu_tiba_tujuan);
                        } catch (\Exception $e) {
                            // fallback ke estimasi atau now
                        }
                    }
                    if (!$waktuSampai && $estimasiWaktuTiba) {
                        $waktuSampai = $estimasiWaktuTiba;
                    }
                    if (!$waktuSampai) {
                        $waktuSampai = $now;
                    }

                    $durasiAutoTerima = $isInstant ? 1 : 2; // jam
                    if ($now->gte($waktuSampai->copy()->addHours($durasiAutoTerima))) {
                        DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                            'status_kirim' => 'Pesanan diterima',
                            'waktu_mulai_tahap' => $now,
                            'updated_at' => $now,
                        ]);
                        DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                            'waktu_pesanan_diterima' => $now,
                            'updated_at' => $now,
                        ]);
                        $updated = true;
                    }
                    continue;
                }
            }

            // === LOGIKA PROGRESIF (HANYA JALAN JIKA ESTIMASI BELUM LEBAT) ===

            // Dibayar → Dikemas
            if ($p->status_kirim === 'Dibayar') {
                if ($durasiBerlalu >= 0.1) { // 6 menit
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Dikemas',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_dikemas' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
            }

            // Kurir Instan
            if ($isInstant) {
                if ($p->status_kirim === 'Dikemas' && $durasiBerlalu >= 0.5) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Diantar kurir',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_diantar_kurir' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Diantar kurir' && $durasiBerlalu >= 0.75) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Sampai tujuan',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);

                    // Hitung waktu tiba & keterlambatan AKURAT
                    $waktuTiba = !empty($p->waktu_tiba_tujuan)
                        ? Carbon::parse($p->waktu_tiba_tujuan)
                        : $now;

                    $statusKeterlambatan = 'tepat_waktu';
                    if ($estimasiWaktuTiba && $waktuTiba->gt($estimasiWaktuTiba)) {
                        $statusKeterlambatan = 'terlambat';
                    } elseif ($estimasiWaktuTiba && $waktuTiba->lt($estimasiWaktuTiba)) {
                        $statusKeterlambatan = 'tepat_waktu';
                    }

                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_tiba_tujuan' => $waktuTiba,
                        'status_keterlambatan' => $statusKeterlambatan,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
            }

            // Kurir Reguler
            if ($isReguler) {
                if ($p->status_kirim === 'Dikemas' && $durasiBerlalu >= 2) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Dikirim dari toko',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_dikirim_toko' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Dikirim dari toko' && $durasiBerlalu >= 6) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Disortir',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_disortir' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Disortir' && $durasiBerlalu >= 4) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Dikirim dari gudang',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_dikirim_gudang' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Dikirim dari gudang' && $durasiBerlalu >= 12) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Sampai gudang tujuan',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_sampai_gudang_tujuan' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Sampai gudang tujuan' && $durasiBerlalu >= 24) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Diantar kurir',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);
                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_diantar_kurir' => $now,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
                if ($p->status_kirim === 'Diantar kurir' && $durasiBerlalu >= 2) {
                    DB::table('ekspedisi')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'status_kirim' => 'Sampai tujuan',
                        'waktu_mulai_tahap' => $now,
                        'updated_at' => $now,
                    ]);

                    $waktuTiba = !empty($p->waktu_tiba_tujuan)
                        ? Carbon::parse($p->waktu_tiba_tujuan)
                        : $now;

                    $statusKeterlambatan = 'tepat_waktu';
                    if ($estimasiWaktuTiba && $waktuTiba->gt($estimasiWaktuTiba)) {
                        $statusKeterlambatan = 'terlambat';
                    } elseif ($estimasiWaktuTiba && $waktuTiba->lt($estimasiWaktuTiba)) {
                        $statusKeterlambatan = 'lebih_cepat';
                    }

                    DB::table('lacak_pesanan')->where('id_ekspedisi', $p->id_ekspedisi)->update([
                        'waktu_tiba_tujuan' => $waktuTiba,
                        'status_keterlambatan' => $statusKeterlambatan,
                        'updated_at' => $now,
                    ]);
                    $updated = true;
                    continue;
                }
            }
        }

        if ($silent) {
            return $updated;
        }

        return response()->json([
            'success' => true,
            'message' => 'Status otomatis diperbarui.',
            'updated' => $updated
        ]);
    }

    public static function detailBeli($id_beli)
    {
        return DB::table('pembelian')
            ->select(
                'produk.*',
                'pembelian.*',
                'pembayaran.*',
                'ekspedisi.*',
                'lacak_pesanan.*',
                'pelanggan.*',
                'website.*',
            )
            ->leftJoin('produk', 'produk.nama_produk', '=', 'pembelian.nama_produk')
            ->leftJoin('pelanggan', 'pelanggan.nama_pelanggan', '=', 'pembelian.nama_pelanggan')
            ->leftJoin('pembayaran', 'pembayaran.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('ekspedisi', 'ekspedisi.id_bayar', '=', 'pembelian.id_bayar')
            ->leftJoin('lacak_pesanan', 'lacak_pesanan.id_ekspedisi', '=', 'ekspedisi.id_ekspedisi')
            ->leftJoin('website', 'website.nama_toko', '=', 'pembelian.nama_toko')
            ->where('pembelian.id_beli', $id_beli)
            ->first();
    }
}