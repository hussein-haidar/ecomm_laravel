<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!session()->has('level')) {
            session()->flash('pesan_akses', 'Anda Tidak Memiliki Akses Website, Silahkan Login Terlebih Dahulu !!!');
            return redirect()->route('auth.login_superadmin');
        }

        // Hanya superadmin yang boleh mengakses
        if (session()->get('level') !== 'superadmin') {
            session()->flash('message', 'Anda Tidak Memiliki Akses Halaman Yang Dituju !!!');
            return redirect()->route($this->homeByLevel());
        }

        return $next($request);
    }

    protected function homeByLevel(): string
    {
        switch (session()->get('level')) {
            case 'pemilik':
                return 'home_pemilik';
            case 'admin':
                return 'home_admin';
            case 'pelanggan':
                return 'home_toko.index';
            default:
                return 'auth.login_superadmin';
        }
    }
}
