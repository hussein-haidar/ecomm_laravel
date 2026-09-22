<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api_key'])->group(function () {
    Route::get('toko', [ApiController::class, 'toko']);
    Route::get('toko/{nama_toko}', [ApiController::class, 'detail_toko']);
    Route::get('produk', [ApiController::class, 'produk']);
    Route::get('produk/{nama_produk}', [ApiController::class, 'detail_produk']);
});