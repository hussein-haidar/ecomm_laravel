<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str; // untuk truncate tagline

class WebsiteController extends Controller
{
    protected $dataWebsite;

    public function __construct()
    {
        // Ambil data website yang aktif
        $website = DB::table('website')
            ->where('status_website', 'Aktif')
            ->where('sesi_user', 'Superadmin')
            ->first();

        $this->dataWebsite = self::buatDataWebsite($website);

        // Share ke semua view
        View::share('dataWebsite', $this->dataWebsite);
    }

    /**
     * Bangun array data website. Bisa dipakai untuk toko mana pun
     * (platform Superadmin atau lapak penjual per nama_toko).
     */
    public static function buatDataWebsite($website)
    {
        if (is_array($website)) {
            $website = (object) $website;
        }

        $deskripsi = $website->footer_title ?? 'Toko online terpercaya dengan berbagai produk berkualitas.';

        $waPusat = '6281234567890';
        if (!empty($website->wa_pusat)) {
            $waDigits = preg_replace('/[^0-9]/', '', $website->wa_pusat);
            if (Str::startsWith($waDigits, '0')) {
                $waDigits = '62' . substr($waDigits, 1);
            } elseif (!Str::startsWith($waDigits, '62')) {
                $waDigits = '62' . $waDigits;
            }
            $waPusat = $waDigits;
        }

        return [
            'nama_toko' => $website->nama_toko ?? 'HAPPYSHOP',
            'logo_website' => $website->logo_website ?? null,
            'wa_pusat' => $website->wa_pusat ?? '',
            'wa_pusat_link' => $waPusat,
            'wa_cabang' => $website->wa_cabang ?? '',
            'alamat_pusat' => $website->alamat_pusat ?? 'Indonesia',
            'alamat_cabang' => $website->alamat_cabang ?? '',
            'latitude_pusat' => $website->latitude_pusat ?? -6.9175,
            'longitude_pusat' => $website->longitude_pusat ?? 107.6191,
            'kode_kota' => $website->kode_kota ?? '',
            'nama_kota' => $website->nama_kota ?? '',
            'bgd_web' => $website->bgd_web ?? null,
            'footer_title' => $deskripsi,
            'deskripsi_toko' => $deskripsi,
            'tagline' => Str::limit($deskripsi, 85, '…'),
            'email_toko' => ($website->email_toko
                ?: (($website->nama_toko ? strtolower(str_replace(' ', '', $website->nama_toko)) : 'info') . '@gmail.com')),
            'link_IG' => $website->link_IG ?? '#',
            'link_FB' => $website->link_FB ?? '#',
            'link_Tiktok' => $website->link_Tiktok ?? '#',
        ];
    }
}