<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Home_toko extends Model
{
    protected $table = 'stok_produk';
    protected $primaryKey = 'id_stok';
    protected $fillable = [
        'kode_stok',
        'jumlah_stok_produk',
        'satuan_produk',
        'tanggal_masuk_produk',
        'id_stok',
        'nama_produk',
        'jenis_produk',
        'harga_produk',
        'berat_produk',
        'satuan_berat',
        'total_harga',
        'total_berat'
    ];

    public function getProduk($keyword = '', $hargaMin = null, $hargaMax = null, $sortBy = '', $jenisProduk = '')
    {
        $query = DB::table('stok_produk')
            ->select('stok_produk.*', 'produk.*','website.*')
            ->leftJoin('website', 'website.sesi_user', '=', 'stok_produk.sesi_user')
            ->leftjoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk');
        if (!empty($keyword)) {
            $query->where(function ($relation) use ($keyword) {
                $relation->where('stok_produk.nama_produk', 'like', "%{$keyword}%")
                    ->orWhere('stok_produk.ukuran_produk', 'like', "%{$keyword}%")
                    ->orWhere('stok_produk.jenis_produk', 'like', "%{$keyword}%");
            });
        }

        if (!empty($jenisProduk)) {
            $query->where('stok_produk.jenis_produk', $jenisProduk);
        }

        if (!empty($hargaMin)) {
            $query->where('stok_produk.harga_produk', '>=', $hargaMin);
        }

        if (!empty($hargaMax)) {
            $query->where('stok_produk.harga_produk', '<=', $hargaMax);
        }

        switch ($sortBy) {
            case 'asc':
                $query->orderBy('stok_produk.harga_produk', 'asc');
                break;
            case 'desc':
                $query->orderBy('stok_produk.harga_produk', 'desc');
                break;
            case 'a-z':
                $query->orderBy('stok_produk.nama_produk', 'asc');
                break;
            case 'z-a':
                $query->orderBy('stok_produk.nama_produk', 'desc');
                break;
            default:
                $query->orderBy('stok_produk.id_stok', 'asc');
                break;
        }

        $produkData = $query->get()->toArray();
        $produkData = array_map(function ($item) {
            return (array) $item;
        }, $produkData);

        $grouped = collect($produkData)->groupBy('nama_produk')->map(function ($items) {
            $sortedItems = $items->sortBy('id_stok')->values();
            $stokAktif = $sortedItems->firstWhere('jumlah_stok_produk', '>', 0);

            if ($stokAktif) {
                $stokAktif['ukuran_list'] = explode('-', $stokAktif['ukuran_produk']);
                return $stokAktif;
            }

            return null;
        })->filter()->values()->toArray();

        return $grouped;
    }

    public function getProduk_ByToko($namaToko = '', $keyword = '', $hargaMin = null, $hargaMax = null, $sortBy = '', $jenisProduk = '')
    {
        // Ambil data toko dari tabel website berdasarkan nama_toko
        $toko = DB::table('website')->where('nama_toko', $namaToko)->first();

        if (!$toko) {
            return []; // Jika toko tidak ditemukan, kembalikan array kosong
        }

        // Ambil sesi_user dari toko yang ditemukan
        $sesi_user = $toko->sesi_user;
        // Query stok produk berdasarkan sesi_user dari toko tersebut
        $query = DB::table('stok_produk')
            ->select('stok_produk.*', 'produk.*', 'website.nama_toko')
            ->leftJoin('website', 'website.sesi_user', '=', 'stok_produk.sesi_user')
            ->leftJoin('produk', 'stok_produk.nama_produk', '=', 'produk.nama_produk')
            ->where('stok_produk.sesi_user', $sesi_user); // Filter berdasarkan sesi_user dari toko

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('stok_produk.nama_produk', 'like', "%{$keyword}%")
                    ->orWhere('stok_produk.ukuran_produk', 'like', "%{$keyword}%")
                    ->orWhere('stok_produk.jenis_produk', 'like', "%{$keyword}%");
            });
        }

        if (!empty($jenisProduk)) {
            $query->where('stok_produk.jenis_produk', $jenisProduk);
        }

        if (!empty($hargaMin)) {
            $query->where('stok_produk.harga_produk', '>=', $hargaMin);
        }

        if (!empty($hargaMax)) {
            $query->where('stok_produk.harga_produk', '<=', $hargaMax);
        }

        switch ($sortBy) {
            case 'asc':
                $query->orderBy('stok_produk.harga_produk', 'asc');
                break;
            case 'desc':
                $query->orderBy('stok_produk.harga_produk', 'desc');
                break;
            case 'a-z':
                $query->orderBy('stok_produk.nama_produk', 'asc');
                break;
            case 'z-a':
                $query->orderBy('stok_produk.nama_produk', 'desc');
                break;
            default:
                $query->orderBy('stok_produk.id_stok', 'asc');
                break;
        }

        $produkData = $query->get()->toArray();
        $produkData = array_map(function ($item) {
            return (array) $item;
        }, $produkData);

        $grouped = collect($produkData)->groupBy('nama_produk')->map(function ($items) {
            $sortedItems = $items->sortBy('id_stok')->values();
            $stokAktif = $sortedItems->firstWhere('jumlah_stok_produk', '>', 0);

            if ($stokAktif) {
                $stokAktif['ukuran_list'] = explode('-', $stokAktif['ukuran_produk']);
                return $stokAktif;
            }

            return null;
        })->filter()->values()->toArray();

        return $grouped;
    }

    public function getJenisProdukDropdown()
    {
        return DB::table('stok_produk')
            ->select('jenis_produk')
            ->distinct()
            ->pluck('jenis_produk')
            ->map(fn($item) => ['jenis_produk' => $item])
            ->toArray();
    }

   public function getProdukByJenis($jenis_produk, $keyword)
{
    // Subquery: hanya cari id_stok terkecil per (nama_produk, ukuran_produk)
    $subQuery = DB::table('stok_produk')
        ->select([
            'nama_produk',
            'ukuran_produk',
            DB::raw('MIN(id_stok) as id_stok')
        ])
        ->where('jumlah_stok_produk', '>', 0)
        ->where('jenis_produk', $jenis_produk)
        ->groupBy('nama_produk', 'ukuran_produk');

    // Query utama
    $query = DB::table('stok_produk')
        ->joinSub($subQuery, 'latest_stok', function ($join) {
            $join->on('stok_produk.id_stok', '=', 'latest_stok.id_stok');
        })
        ->join('produk', 'stok_produk.nama_produk', '=', 'produk.nama_produk')
        ->leftJoin('website', 'stok_produk.sesi_user', '=', 'website.sesi_user') // join di sini
        ->where('stok_produk.jenis_produk', $jenis_produk);

    if (!empty($keyword)) {
        $query->where(function ($q) use ($keyword) {
            $q->where('stok_produk.nama_produk', 'like', "%$keyword%")
              ->orWhere('stok_produk.jenis_produk', 'like', "%$keyword%");
        });
    }

    $data = $query->select([
        'stok_produk.id_stok',
        'stok_produk.nama_produk',
        'stok_produk.jumlah_stok_produk',
        'stok_produk.ukuran_produk',
        'stok_produk.satuan_produk',
        'stok_produk.satuan_berat',
          'stok_produk.sesi_user',
        'produk.harga_produk',
        'produk.foto_produk',
        'produk.berat_produk',
        'website.nama_toko' // ✅ Aman karena join setelah subquery
    ])->get();

    return json_decode(json_encode($data), true);
}

    public function getWebsite($nama_toko)
    {
        return DB::table('website') // <-- benar: akses tabel website
            ->where('website.nama_toko', $nama_toko)
            ->orderBy('id_website', 'DESC')
            ->get();
    }

    public function getPromo()
    {
        return DB::table('event_promo')
            ->select('id_promo', 'nama_promo', 'gambar_event', 'gambar_event2', 'gambar_event3')
            ->where('status_event', 'aktif')
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_berakhir', '>=', now())
            ->orderBy('id_promo', 'DESC')
            ->get()
            ->map(function ($promo) {
                $images = [];
                if ($promo->gambar_event) {
                    $images[] = 'gambarevent/' . $promo->gambar_event;
                }
                if ($promo->gambar_event2) {
                    $images[] = 'gambarevent/' . $promo->gambar_event2;
                }
                if ($promo->gambar_event3) {
                    $images[] = 'gambarevent/' . $promo->gambar_event3;
                }
                $promo->all_images = $images;
                return $promo;
            });
    }

    public function detailStok($namaProduk, $idStok = null)
    {
        // Query utama untuk ambil detail produk dan stok
        $query = DB::table('stok_produk')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->leftJoin('website', 'website.sesi_user', '=', 'stok_produk.sesi_user')
            ->select(
                'stok_produk.*',
                'produk.*',
                'website.*', // <= Baris ini ditambahkan
                DB::raw('SUM(stok_produk.jumlah_stok_produk) OVER(PARTITION BY stok_produk.nama_produk) as total_stok')
            )
            ->where('stok_produk.nama_produk', $namaProduk);

        if (!empty($idStok)) {
            $query->where('stok_produk.id_stok', $idStok);
        }

        $result = $query->first();

        if ($result) {
            $result = (array) $result;

            // Ambil ukuran_produk dari hasil query
            $ukuran_produk = $result['ukuran_produk'] ?? '';

            // Proses ukuran_produk sesuai format
            if (!empty($ukuran_produk)) {
                if (strpos($ukuran_produk, '-') !== false) {
                    $ukuran_list = explode('-', $ukuran_produk);
                } elseif (strpos($ukuran_produk, ',') !== false) {
                    $ukuran_list = explode(',', $ukuran_produk);
                } else {
                    $ukuran_list = [$ukuran_produk];
                }

                // Bersihkan spasi dan kapitalisasi
                $ukuran_list = array_map('trim', $ukuran_list);
            } else {
                $ukuran_list = [];
            }

            $result['ukuran_list'] = $ukuran_list;
        } else {
            $result = ['ukuran_list' => [], 'total_stok' => 0];
        }

        return $result;
    }
}
