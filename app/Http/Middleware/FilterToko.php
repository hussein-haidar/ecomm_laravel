<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterToko
{
    public function handle(Request $request, Closure $next)
    {
        // Memeriksa session level  
        if (session()->get('level') == '') {
            session()->flash('pesan_akses', 'Anda Tidak Memiliki Akses Website, Silahkan Login Terlebih Dahulu !!!');
            return redirect()->route('auth.login_pelanggan'); // Redirect ke halaman login jika belum login  
        }

        // Memeriksa apakah user memiliki level pelanggan
        if (session()->get('level') != 'pelanggan') {
            session()->flash('message', 'Anda Tidak Memiliki Akses Halaman Yang Dituju !!!');
            return redirect()->route('home_toko.index'); // Redirect ke halaman utama toko jika level tidak sesuai
        }

        return $next($request);
    }
}
