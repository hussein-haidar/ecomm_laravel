<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $produks = DB::table('produk')->where('deleted_at', 0)->get();
        $tokoList = DB::table('website')->where('status_website', 'Aktif')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Home
        $xml .= '<url><loc>' . url('/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';
        
        // Katalog
        $xml .= '<url><loc>' . url('home_toko/katalog') . '</loc><changefreq>daily</changefreq><priority>0.9</priority></url>';
        
        // Flash Sale
        $xml .= '<url><loc>' . url('flash-sale') . '</loc><changefreq>daily</changefreq><priority>0.8</priority></url>';
        
        // Produk
        foreach ($produks as $p) {
            $xml .= '<url><loc>' . url('home_toko/detail_produk/' . $p->nama_produk) . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';
        }
        
        // Toko
        foreach ($tokoList as $t) {
            $xml .= '<url><loc>' . url('home_toko/view_toko/' . $t->nama_toko) . '</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>';
        }
        
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
