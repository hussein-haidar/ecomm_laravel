<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!session()->has('level')) {
            session()->flash('pesan_akses', 'Anda Tidak Memiliki Akses Website, Silahkan Login Terlebih Dahulu !!!');
            return redirect()->route('auth.login_user');
        }

        // Hanya admin yang boleh mengakses
        if (session()->get('level') !== 'admin') {
            session()->flash('message', 'Anda Tidak Memiliki Akses Halaman Yang Dituju !!!');
            return redirect()->route($this->homeByLevel());
        }

        return $next($request);
    }

    protected function homeByLevel(): string
    {
        switch (session()->get('level')) {
            case 'superadmin':
                return 'home_superadmin';
            case 'pemilik':
                return 'home_pemilik';
            case 'pelanggan':
                return 'home_toko.index';
            default:
                return 'auth.login_user';
        }
    }
}
