<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Midtrans payment gateway (QRIS).
    |
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL', '/pelanggan_data/statusBayar'),
    'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_REDIRECT_URL', '/pelanggan_data/statusBayar'),
    'error_redirect_url' => env('MIDTRANS_ERROR_REDIRECT_URL', '/pelanggan_data/statusBayar'),

    'callback_url' => env('MIDTRANS_CALLBACK_URL', '/midtrans/callback'),

];
