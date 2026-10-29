<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterPelanggan
{
    public function handle(Request $request, Closure $next)
    {
        // Memeriksa session level
        if (session()->get('level') == '') {
            // Jika request AJAX/API, return JSON 401 agar frontend tidak kena redirect ke HTML login
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Harus login terlebih dahulu.'], 401);
            }
            session()->flash('pesan_akses', 'Anda Tidak Memiliki Akses Website, Silahkan Login Terlebih Dahulu !!!');
            return redirect()->route('auth.login_pelanggan'); // Redirect ke halaman login jika belum login
        }

        // Memeriksa apakah user memiliki level pelanggan
        if (session()->get('level') != 'pelanggan') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            session()->flash('message', 'Anda Tidak Memiliki Akses Halaman Yang Dituju !!!');
            return redirect()->route('home_toko.index'); // Redirect ke halaman utama toko jika level tidak sesuai
        }

        return $next($request);
    }
}
