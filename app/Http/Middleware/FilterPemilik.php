<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterPemilik
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!session()->has('level')) {
            session()->flash('pesan_akses', 'Anda Tidak Memiliki Akses Website, Silahkan Login Terlebih Dahulu !!!');
            return redirect()->route('auth.login_user');
        }

        // Hanya pemilik yang boleh mengakses
        if (session()->get('level') != 'pemilik') {
            session()->flash('message', 'Anda Tidak Memiliki Akses Halaman Yang Dituju !!!');
            return redirect()->route($this->homeByLevel());
        }

        // Cek status verifikasi lapak (hanya untuk pemilik)
        $sesiUser = session()->get('sesi_user');
        if ($sesiUser) {
            $website = \DB::table('website')
                ->where('sesi_user', $sesiUser)
                ->where('level', 'pemilik')
                ->first();
            if ($website && $website->status_verifikasi !== 'Disetujui') {
                $msg = match($website->status_verifikasi) {
                    'Menunggu' => 'Lapak Anda sedang ditinjau admin. Silakan tunggu persetujuan.',
                    'Ditolak' => 'Lapak ditolak: ' . ($website->alasan_ditolak ?? 'Tidak ada alasan.'),
                    default => 'Lapak belum disetujui.',
                };
                return redirect()->route('auth.login_user')
                    ->with('pesan_warning', $msg);
            }
        }

        return $next($request);
    }

    protected function homeByLevel(): string
    {
        switch (session()->get('level')) {
            case 'superadmin':
                return 'home_superadmin';
            case 'admin':
                return 'home_admin';
            case 'pelanggan':
                return 'home_toko.index';
            default:
                return 'auth.login_user';
        }
    }
}
