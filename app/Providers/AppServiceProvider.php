<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Share payment notification data to all views using the admin template layout
        view()->composer('layouts.template', function ($view) {
            $jumlahBayar = 0;
            $daftarNotifikasi = collect();

            $sesi_user = session('sesi_user');
            $nama_pelanggan = session('nama_pelanggan');

            if ($sesi_user && !empty($nama_pelanggan)) {
                try {
                    $model = new \App\Models\M_Pembayaran();
                    $hasil = $model->get_status_bayar_sesi($sesi_user, $nama_pelanggan);

                    if (is_array($hasil) || $hasil instanceof \Traversable) {
                        $daftarNotifikasi = collect($hasil);
                        $jumlahBayar = $daftarNotifikasi->count();
                    }
                } catch (\Exception $e) {
                    $daftarNotifikasi = collect();
                    $jumlahBayar = 0;
                }
            }

            $view->with(compact('jumlahBayar', 'daftarNotifikasi'));
        });
    }
}

