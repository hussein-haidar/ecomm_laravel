<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class M_Stok extends Model
{
    protected $table = 'stok_produk';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
      
        'sesi_user',
        'kode_stok',
        'jumlah_stok_produk',
        'satuan_produk',
        'tanggal_masuk_produk',
        'nama_produk',
        'varian_produk',
        'harga_produk',
        'persentase',
        'harga_produk_new',
        'berat_produk',
        'satuan_berat',
        'total_harga',
        'total_berat',
    ];

    // Jika tidak menggunakan timestamps, matikan pengelolaan timestamps otomatis  
    public $timestamps = false;

    // Ambil semua stok berdasarkan user
    public function getStok()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('stok_produk')
            ->select('stok_produk.*', 'produk.*')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->select(
            'stok_produk.*',
            'produk.*'
            )
            ->where('stok_produk.sesi_user', $sesi_user)
            ->orderByDesc('id_stok')
            ->get();
    }

    // Total stok dan total harga
    public function getTotStok()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('stok_produk as sp')
            ->leftJoin('produk as p', 'p.nama_produk', '=', 'sp.nama_produk')
            ->select(
                'sp.nama_produk',
                DB::raw('MAX(sp.kode_stok) as kode_stok'),
                DB::raw('SUM(sp.jumlah_stok_produk) as total_stok'),
                DB::raw('MAX(p.harga_produk) as harga_produk'),
                DB::raw('SUM(sp.jumlah_stok_produk * p.harga_produk) as total_harga'),
                DB::raw('MAX(sp.satuan_produk) as satuan_produk'),   // ✅ Fixed
                DB::raw('MAX(p.ukuran_produk) as ukuran_produk'),   // ✅ Fixed
                DB::raw('MAX(p.foto_produk) as foto_produk'),
                DB::raw('MAX(sp.id_stok) as id_stok')
            )
            ->where('sp.sesi_user', $sesi_user)
            ->groupBy('sp.nama_produk')
            ->orderByDesc('id_stok')
            ->get();
    }
    
    public function getStokByName($nama_produk)
    {
        return DB::table('stok_produk')
            ->select('stok_produk.*', 'produk.*')
            ->leftJoin('produk', 'produk.nama_produk', '=', 'stok_produk.nama_produk')
            ->where('stok_produk.nama_produk', $nama_produk)
            ->orderByDesc('id_stok')
            ->get();
    }

    public static function getUserById($sesi_user)
    {
        return DB::table('user')
            ->select('sesi_user')
            ->where('sesi_user', $sesi_user)
            ->first();
    }

    public function getSatuan()
    {
        return DB::table('satuan_produk')
            ->select('satuan_produk.*')
            ->orderBy('id_satuan', 'DESC')
            ->get();
    }

    public function getProduk()
    {
        $sesi_user = Session::get('sesi_user');
        return DB::table('produk')
            ->select(
                'produk.*'
            )
            ->where('produk.sesi_user', $sesi_user)
            ->orderBy('id_produk', 'DESC')
            ->get();
    }

    public function kurangiStokFIFO($nama_produk, $jumlah)
    {
        return DB::transaction(function () use ($nama_produk, $jumlah) {
            $stokList = DB::table('stok_produk')
                ->where('nama_produk', $nama_produk)
                ->where('jumlah_stok_produk', '>', 0)
                ->orderBy('tanggal_masuk_produk', 'ASC')
                ->lockForUpdate()
                ->get();

            if ($stokList->isEmpty()) {
                return ['success' => false, 'message' => 'Stok tidak tersedia untuk produk: ' . $nama_produk];
            }

            $total_harga = 0;
            $total_berat = 0;
            $stok_digunakan = [];
            $sisa = $jumlah;

            foreach ($stokList as $stok) {
                if ($sisa <= 0) break;

                $kurang = min($stok->jumlah_stok_produk, $sisa);

                $sisa_stok = $stok->jumlah_stok_produk - $kurang;
                $total_berat_baru = $sisa_stok * $stok->berat_produk;
                $total_harga_baru = $sisa_stok * $stok->harga_produk;

                DB::table('stok_produk')
                    ->where('id_stok', $stok->id_stok)
                    ->update([
                        'jumlah_stok_produk' => $sisa_stok,
                        'total_berat' => $total_berat_baru,
                        'total_harga' => $total_harga_baru
                    ]);

                $total_harga += $kurang * $stok->harga_produk;
                $total_berat += $kurang * $stok->berat_produk;

                $stok_digunakan[] = [
                    'id_stok' => $stok->id_stok,
                    'jumlah_diambil' => $kurang,
                    'harga' => $stok->harga_produk,
                    'berat' => $stok->berat_produk
                ];

                $sisa -= $kurang;
            }

            if ($sisa > 0) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi untuk produk: ' . $nama_produk];
            }

            return [
                'success' => true,
                'total_harga' => $total_harga,
                'total_berat' => $total_berat,
                'stok_digunakan' => $stok_digunakan
            ];
        });
    }

    public function tambahStok($id_stok, $jumlah)
    {
        $stok = $this->find($id_stok);

        if (!$stok) {
            return ['success' => false, 'message' => 'Stok tidak ditemukan.'];
        }

        if ($jumlah <= 0) {
            return ['success' => false, 'message' => 'Jumlah harus lebih dari 0.'];
        }

        $stok_baru = $stok->jumlah_stok_produk + $jumlah;
        $total_harga = $stok_baru * $stok->harga_produk;
        $total_berat = $stok_baru * $stok->berat_produk;

        $this->where('id_stok', $id_stok)->update([
            'jumlah_stok_produk' => $stok_baru,
            'total_harga' => $total_harga,
            'total_berat' => $total_berat,
        ]);

        return ['success' => true, 'message' => 'Stok berhasil ditambahkan.'];
    }

    public function add(array $data)
    {
        $produk = DB::table('produk')
            ->select('harga_produk', 'berat_produk')
            ->where('nama_produk', $data['nama_produk'])
            ->first();

        if (!$produk) {
            throw new \Exception("Produk tidak ditemukan: " . $data['nama_produk']);
        }

        $jumlah_stok = $data['jumlah_stok_produk'] ?? 0;
        $data['harga_produk'] = $produk->harga_produk;
        $data['berat_produk'] = $produk->berat_produk;
        $data['total_harga'] = $produk->harga_produk * $jumlah_stok;
        $data['total_berat'] = $produk->berat_produk * $jumlah_stok;

        DB::table('stok_produk')->insert($data);
    }

    // Fungsi baru untuk update data
    public function updateData($id_stok, $data)
    {
        // Ambil data produk berdasarkan nama_produk
        $produk = DB::table('produk')
            ->select('harga_produk', 'berat_produk')
            ->where('nama_produk', $data['nama_produk'])
            ->first();

        if (!$produk) {
            throw new \Exception("Produk tidak ditemukan: " . $data['nama_produk']);
        }

        // Ambil jumlah stok dari request
        $jumlah_stok = $data['jumlah_stok_produk'] ?? 0;

        // Set field yang diperlukan
        $data['harga_produk'] = $produk->harga_produk;
        $data['berat_produk'] = $produk->berat_produk;
        $data['total_harga'] = $produk->harga_produk * $jumlah_stok;
        $data['total_berat'] = $produk->berat_produk * $jumlah_stok;

        // Update database
        $updated = DB::table('stok_produk')->where('id_stok', $id_stok)->update($data);

        return $updated;
    }
}
