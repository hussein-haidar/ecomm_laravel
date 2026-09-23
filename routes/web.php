<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Home_superadmin;
use App\Http\Controllers\Home_pemilik;
use App\Http\Controllers\Home_admin;
use App\Http\Controllers\Home_toko;
use App\Http\Controllers\Superadmin_data;
use App\Http\Controllers\Pemilik_data;
use App\Http\Controllers\Admin_data;
use App\Http\Controllers\Admin_laporan_mingguan;
use App\Http\Controllers\Admin_laporan_bulanan;
use App\Http\Controllers\Pelanggan_data;
use App\Http\Middleware\FilterSuperadmin;
use App\Http\Middleware\FilterPemilik;
use App\Http\Middleware\FilterAdmin;
use App\Http\Middleware\FilterPelanggan;
use App\Http\Controllers\SitemapController;


use App\Http\Controllers\MidtransCallbackController;


// Ini adalah route root '/'
Route::get('/', function () {
    return redirect('/home_toko/index');
});

// Atau langsung tampilkan konten dari controller
Route::get('/home_toko/index', [App\Http\Controllers\Home_toko::class, 'index'])->name('home_toko.index');

// Rute untuk register (filter berdasarkan level)  
Route::get('auth/login_user', [Auth::class, 'login_user'])->middleware('guest')->name('auth.login_user');
Route::post('auth/cek_login', [Auth::class, 'cek_login'])->name('auth.cek_login');
Route::post('auth/logout_user', [Auth::class, 'logout_user'])->name('auth.logout_user');
Route::get('auth/register_user', [Auth::class, 'register_user'])->name('auth.register_user');
Route::post('auth/save_user', [Auth::class, 'save_user'])->name('auth.save_user');

// Rute untuk register (filter berdasarkan level)  
Route::get('auth/login_superadmin', [Auth::class, 'login_superadmin'])->middleware('guest')->name('auth.login_superadmin');
Route::post('auth/cek_login_superadmin', [Auth::class, 'cek_login_superadmin'])->name('auth.cek_login_superadmin');
Route::post('auth/logout_superadmin', [Auth::class, 'logout_superadmin'])->name('auth.logout_superadmin');
Route::get('auth/register_superadmin', [Auth::class, 'register_superadmin'])->name('auth.register_superadmin');
Route::post('auth/save_superadmin', [Auth::class, 'save_superadmin'])->name('auth.save_superadmin');

// File: routes/web.php
Route::prefix('log')->group(function () {
    Route::get('superadmin_data/log_aktivis', [Superadmin_data::class, 'log_aktivis'])->name('superadmin_data.log_aktivis');
    Route::post('superadmin_data/delete-by-date', [Superadmin_data::class, 'deleteByDateRange'])->name('superadmin_data.deleteByDate');
});

// Rute untuk superadmin  
Route::middleware([FilterSuperAdmin::class])->group(function () {
    Route::get('home_superadmin', [Home_superadmin::class, 'index'])->name('home_superadmin');
    Route::get('superadmin_data/profil', [superadmin_data::class, 'profil'])->name('superadmin_data.profil');
    Route::get('superadmin_data/edit/{id_user}', [superadmin_data::class, 'edit'])->name('superadmin_data.edit');
    Route::post('superadmin_data/update_profile/{id_user}', [superadmin_data::class, 'update_profile'])->name('superadmin_data.update_profile');

    Route::get('superadmin_data/view_totbenefit', [superadmin_data::class, 'view_totbenefit'])->name('superadmin_data.view_totbenefit');

    Route::get('superadmin_data/bank', [superadmin_data::class, 'bank'])->name('superadmin_data.bank');
    Route::get('superadmin_data/add_bank', [superadmin_data::class, 'add_bank'])->name('superadmin_data.add_bank');
    Route::post('superadmin_data/save_bank', [superadmin_data::class, 'save_bank'])->name('superadmin_data.save_bank');
    Route::get('superadmin_data/edit_bank/{id_bank}', [superadmin_data::class, 'edit_bank'])->name('superadmin_data.edit_bank');
    Route::post('superadmin_data/update_bank/{id_bank}', [superadmin_data::class, 'update_bank'])->name('superadmin_data.update_bank');
    Route::delete('superadmin_data/delete_bank/{id_bank}', [superadmin_data::class, 'delete_bank'])->name('superadmin_data.delete_bank');

    Route::get('superadmin_data/kurir', [superadmin_data::class, 'kurir'])->name('superadmin_data.kurir');
    Route::get('superadmin_data/add_kurir', [superadmin_data::class, 'add_kurir'])->name('superadmin_data.add_kurir');
    Route::post('superadmin_data/save_kurir', [superadmin_data::class, 'save_kurir'])->name('superadmin_data.save_kurir');
    Route::get('superadmin_data/edit_kurir/{id_kurir}', [superadmin_data::class, 'edit_kurir'])->name('superadmin_data.edit_kurir');
    Route::post('superadmin_data/update_kurir/{id_kurir}', [superadmin_data::class, 'update_kurir'])->name('superadmin_data.update_kurir');
    Route::delete('superadmin_data/delete_kurir/{id_kurir}', [superadmin_data::class, 'delete_kurir'])->name('superadmin_data.delete_kurir');

    Route::get('superadmin_data/komisi', [superadmin_data::class, 'komisi'])->name('superadmin_data.komisi');
    Route::get('superadmin_data/add_komisi', [superadmin_data::class, 'add_komisi'])->name('superadmin_data.add_komisi');
    Route::post('superadmin_data/save_komisi', [superadmin_data::class, 'save_komisi'])->name('superadmin_data.save_komisi');
    Route::get('superadmin_data/edit_komisi/{id_biaya}', [superadmin_data::class, 'edit_komisi'])
        ->name('superadmin_data.edit_komisi');
    Route::post('superadmin_data/update_komisi/{id_biaya}', [superadmin_data::class, 'update_komisi'])->name('superadmin_data.update_komisi');
    Route::delete('superadmin_data/delete_komisi/{id_biaya}', [superadmin_data::class, 'delete_komisi'])->name('superadmin_data.delete_komisi');

    Route::get('superadmin_data/website', [superadmin_data::class, 'website'])->name('superadmin_data.website');
    Route::get('superadmin_data/add_website', [superadmin_data::class, 'add_website'])->name('superadmin_data.add_website');
    Route::post('superadmin_data/save_website', [superadmin_data::class, 'save_website'])->name('superadmin_data.save_website');
    Route::get('superadmin_data/edit_website/{id_website}', [superadmin_data::class, 'edit_website'])->name('superadmin_data.edit_website');
    Route::post('superadmin_data/update_website/{id_website}', [superadmin_data::class, 'update_website'])->name('superadmin_data.update_website');
    Route::delete('superadmin_data/delete_website/{id_website}', [superadmin_data::class, 'delete_website'])->name('superadmin_data.delete_website');

    Route::get('superadmin_data/view_website', [superadmin_data::class, 'view_website'])->name('superadmin_data.view_website');
    Route::post('superadmin_data/konfirmStatusWebAll', [superadmin_data::class, 'konfirmStatusWebAll'])->name('superadmin_data.konfirmStatusWebAll');
    Route::post('superadmin_data/konfirmStatusWeb_By_Sesi', [superadmin_data::class, 'konfirmStatusWeb_By_Sesi'])->name('superadmin_data.konfirmStatusWeb_By_Sesi');

    Route::get('superadmin_data/event', [superadmin_data::class, 'event'])->name('superadmin_data.event');
    Route::post('superadmin_data/konfirmStatusEvent_By_Sesi', [superadmin_data::class, 'konfirmStatusEvent_By_Sesi'])->name('superadmin_data.konfirmStatusEvent_By_Sesi');
    Route::get('superadmin_data/add_event', [superadmin_data::class, 'add_event'])->name('superadmin_data.add_event');
    Route::post('superadmin_data/save_event', [superadmin_data::class, 'save_event'])->name('superadmin_data.save_event');
    Route::get('superadmin_data/edit_event/{id_promo}', [superadmin_data::class, 'edit_event'])
        ->name('superadmin_data.edit_event');
    Route::post('superadmin_data/update_event/{id_promo}', [superadmin_data::class, 'update_event'])->name('superadmin_data.update_event');
    Route::delete('superadmin_data/delete_event/{id_promo}', [superadmin_data::class, 'delete_event'])->name('superadmin_data.delete_event');

    Route::get('superadmin_data/backup_db', [superadmin_data::class, 'backup_db'])->name('superadmin_data.backup_db');
    Route::post('superadmin_data/proses_db', [superadmin_data::class, 'proses_db'])->name('superadmin_data.proses_db');
});

// Rute untuk pemilik (filter berdasarkan level)  
Route::middleware([FilterPemilik::class])->group(function () {
    Route::get('home_pemilik', [Home_pemilik::class, 'index'])->name('home_pemilik');
    Route::get('pemilik_data/profil', [Pemilik_data::class, 'profil'])->name('pemilik_data.profil');

    Route::get('pemilik_data/satuan', [Pemilik_data::class, 'satuan'])->name('pemilik_data.satuan');
    Route::get('pemilik_data/add_satuan', [Pemilik_data::class, 'add_satuan'])->name('pemilik_data.add_satuan');
    Route::post('pemilik_data/save_satuan', [Pemilik_data::class, 'save_satuan'])->name('pemilik_data.save_satuan');
    Route::get('pemilik_data/edit_satuan/{id_satuan}', [Pemilik_data::class, 'edit_satuan'])->name('pemilik_data.edit_satuan');
    Route::post('pemilik_data/update_satuan/{id_satuan}', [Pemilik_data::class, 'update_satuan'])->name('pemilik_data.update_satuan');
    Route::delete('pemilik_data/delete_satuan/{id_satuan}', [Pemilik_data::class, 'delete_satuan'])->name('pemilik_data.delete_satuan');

    Route::get('pemilik_data/varian', [Pemilik_data::class, 'varian'])->name('pemilik_data.varian');
    Route::get('pemilik_data/add_varian', [Pemilik_data::class, 'add_varian'])->name('pemilik_data.add_varian');
    Route::post('pemilik_data/save_varian', [Pemilik_data::class, 'save_varian'])->name('pemilik_data.save_varian');
    Route::get('pemilik_data/edit_varian/{id_varian}', [Pemilik_data::class, 'edit_varian'])->name('pemilik_data.edit_varian');
    Route::post('pemilik_data/update_varian/{id_varian}', [Pemilik_data::class, 'update_varian'])->name('pemilik_data.update_varian');
    Route::delete('pemilik_data/delete_varian/{id_varian}', [Pemilik_data::class, 'delete_varian'])->name('pemilik_data.delete_varian');

    Route::get('pemilik_data/jenis', [Pemilik_data::class, 'jenis'])->name('pemilik_data.jenis');
    Route::get('pemilik_data/add_jenis', [Pemilik_data::class, 'add_jenis'])->name('pemilik_data.add_jenis');
    Route::post('pemilik_data/save_jenis', [Pemilik_data::class, 'save_jenis'])->name('pemilik_data.save_jenis');
    Route::get('pemilik_data/edit_jenis/{id_jenis}', [Pemilik_data::class, 'edit_jenis'])->name('pemilik_data.edit_jenis');
    Route::post('pemilik_data/update_jenis/{id_jenis}', [Pemilik_data::class, 'update_jenis'])->name('pemilik_data.update_jenis');
    Route::delete('pemilik_data/delete_jenis/{id_jenis}', [Pemilik_data::class, 'delete_jenis'])->name('pemilik_data.delete_jenis');

    Route::get('pemilik_data/produk', [Pemilik_data::class, 'produk'])->name('pemilik_data.produk');
    Route::get('/generateKodeProduk', [Pemilik_data::class, 'generateKodeProduk'])->name('pemilik_data.generateKodeProduk');
    Route::get('/generateNamaProduk', [Pemilik_data::class, 'generateNamaProduk'])->name('pemilik_data.generateNamaProduk');
    Route::get('pemilik_data/add_produk', [Pemilik_data::class, 'add_produk'])->name('pemilik_data.add_produk');
    Route::post('pemilik_data/save_produk', [Pemilik_data::class, 'save_produk'])->name('pemilik_data.save_produk');
    Route::get('pemilik_data/edit_produk/{id_produk}', [Pemilik_data::class, 'edit_produk'])->name('pemilik_data.edit_produk');
    Route::post('pemilik_data/update_produk/{id_produk}', [Pemilik_data::class, 'update_produk'])->name('pemilik_data.update_produk');
    Route::post('pemilik_data/update_carousel/{id_produk}', [Pemilik_data::class, 'update_carousel'])->name('pemilik_data.update_carousel');
    Route::delete('pemilik_data/delete_produk/{id_produk}', [Pemilik_data::class, 'delete_produk'])->name('pemilik_data.delete_produk');
    Route::get('pemilik_data/data_dihapus_produk/', [Pemilik_data::class, 'data_dihapus_produk'])->name('pemilik_data.data_dihapus_produk');
    Route::get('pemilik_data/restore/{id_produk}', [Pemilik_data::class, 'restore'])->name('pemilik_data.restore');
    Route::delete('pemilik_data/delete_hard_produk/{id_produk}', [Pemilik_data::class, 'delete_hard_produk'])->name('pemilik_data.delete_hard_produk');

    Route::get('pemilik_data/view_jual', [Pemilik_data::class, 'view_jual'])->name('pemilik_data.view_jual');
    Route::get('pemilik_data/view_totjual', [Pemilik_data::class, 'view_totjual'])->name('pemilik_data.view_totjual');
    Route::get('pemilik_data/view_benefit', [Pemilik_data::class, 'view_benefit'])->name('pemilik_data.view_benefit');
    Route::get('pemilik_data/view_totbenefit', [Pemilik_data::class, 'view_totbenefit'])->name('pemilik_data.view_totbenefit');

    Route::get('pemilik_data/user', [Pemilik_data::class, 'user'])->name('pemilik_data.user');
    Route::get('pemilik_data/add_user', [Pemilik_data::class, 'add_user'])->name('pemilik_data.add_user');
    Route::post('pemilik_data/save_user', [Pemilik_data::class, 'save_user'])->name('pemilik_data.save_user');
    Route::get('pemilik_data/edit_user/{id_user}', [Pemilik_data::class, 'edit_user'])->name('pemilik_data.edit_user');
    Route::post('pemilik_data/update_user/{id_user}', [Pemilik_data::class, 'update_user'])->name('pemilik_data.update_user');
    Route::delete('pemilik_data/delete_user/{id_user}', [Pemilik_data::class, 'delete_user'])->name('pemilik_data.delete_user');

    Route::post('pemilik_data/konfirmStatusWeb_By_Sesi', [Pemilik_data::class, 'konfirmStatusWeb_By_Sesi'])->name('pemilik_data.konfirmStatusWeb_By_Sesi');
    Route::get('pemilik_data/website', [Pemilik_data::class, 'website'])->name('pemilik_data.website');
    Route::get('pemilik_data/add_website', [Pemilik_data::class, 'add_website'])->name('pemilik_data.add_website');
    Route::post('pemilik_data/save_website', [Pemilik_data::class, 'save_website'])->name('pemilik_data.save_website');
    Route::get('pemilik_data/edit_website/{id_website}', [Pemilik_data::class, 'edit_website'])->name('pemilik_data.edit_website');
    Route::post('pemilik_data/update_website/{id_website}', [Pemilik_data::class, 'update_website'])->name('pemilik_data.update_website');
    Route::delete('pemilik_data/delete_website/{id_website}', [Pemilik_data::class, 'delete_website'])->name('pemilik_data.delete_website');

    // Laporan Penjualan
    Route::get('pemilik_data/laporan_penjualan', [Pemilik_data::class, 'laporan_penjualan'])->name('pemilik_data.laporan_penjualan');
    Route::get('pemilik_data/grafik_penjualan', [Pemilik_data::class, 'grafikPenjualan'])->name('pemilik_data.grafikPenjualan');

    // Kupon
    Route::get('pemilik_data/kupon', [Pemilik_data::class, 'kupon'])->name('pemilik_data.kupon');
    Route::get('pemilik_data/add_kupon', [Pemilik_data::class, 'add_kupon'])->name('pemilik_data.add_kupon');
    Route::post('pemilik_data/save_kupon', [Pemilik_data::class, 'save_kupon'])->name('pemilik_data.save_kupon');
    Route::get('pemilik_data/edit_kupon/{id}', [Pemilik_data::class, 'edit_kupon'])->name('pemilik_data.edit_kupon');
    Route::post('pemilik_data/update_kupon/{id}', [Pemilik_data::class, 'update_kupon'])->name('pemilik_data.update_kupon');
    Route::delete('pemilik_data/delete_kupon/{id}', [Pemilik_data::class, 'delete_kupon'])->name('pemilik_data.delete_kupon');

    // Flash Sale
    Route::get('pemilik_data/flash_sale', [Pemilik_data::class, 'flash_sale'])->name('pemilik_data.flash_sale');
    Route::get('pemilik_data/add_flash_sale', [Pemilik_data::class, 'add_flash_sale'])->name('pemilik_data.add_flash_sale');
    Route::post('pemilik_data/save_flash_sale', [Pemilik_data::class, 'save_flash_sale'])->name('pemilik_data.save_flash_sale');
    Route::get('pemilik_data/edit_flash_sale/{id_flash_sale}', [Pemilik_data::class, 'edit_flash_sale'])->name('pemilik_data.edit_flash_sale');
    Route::post('pemilik_data/update_flash_sale/{id_flash_sale}', [Pemilik_data::class, 'update_flash_sale'])->name('pemilik_data.update_flash_sale');
    Route::delete('pemilik_data/delete_flash_sale/{id_flash_sale}', [Pemilik_data::class, 'delete_flash_sale'])->name('pemilik_data.delete_flash_sale');


});

// Rute untuk admin  
Route::middleware([FilterAdmin::class])->group(function () {
    Route::get('home_admin', [Home_admin::class, 'index'])->name('home_admin');
    Route::get('admin_data/profil', [Admin_data::class, 'profil'])->name('admin_data.profil');
    Route::get('admin_data/edit/{id_user}', [Admin_data::class, 'edit'])->name('admin_data.edit');
    Route::post('admin_data/update_profile/{id_user}', [Admin_data::class, 'update_profile'])->name('admin_data.update_profile');

    Route::get('admin_data/view_produk', [Admin_data::class, 'view_produk'])->name('admin_data.view_produk');
    Route::get('/generateKodeStok', [Admin_data::class, 'generateKodeStok'])->name('admin_data.generateKodeStok');
    Route::get('admin_data/stok', [Admin_data::class, 'stok'])->name('admin_data.stok');
    Route::get('admin_data/tot_stok', [Admin_data::class, 'tot_stok'])->name('admin_data.tot_stok');
    Route::get('admin_data/add_stok', [Admin_data::class, 'add_stok'])->name('admin_data.add_stok');

    Route::post('admin_data/save_stok', [Admin_data::class, 'save_stok'])->name('admin_data.save_stok');
    Route::get('admin_data/edit_stok/{id_stok}', [Admin_data::class, 'edit_stok'])->name('admin_data.edit_stok');
    Route::post('admin_data/update_stok/{id_stok}', [Admin_data::class, 'update_stok'])->name('admin_data.update_stok');
    Route::delete('admin_data/delete_stok/{id_stok}', [Admin_data::class, 'delete_stok'])->name('admin_data.delete_stok');

    Route::get('admin_data/view_jual', [Admin_data::class, 'view_jual'])->name('admin_data.view_jual');
    Route::get('admin_data/view_totjual', [Admin_data::class, 'view_totjual'])->name('admin_data.view_totjual');
    Route::get('admin_data/view_bayar', [Admin_data::class, 'view_bayar'])->name('admin_data.view_bayar');
    Route::get('admin_data/viewFoto/{id_bayar}', [Admin_data::class, 'viewFoto'])->name('admin_data.viewFoto');
    Route::post('admin_data/konfirmStatusBayar', [Admin_data::class, 'konfirmStatusBayar'])->name('admin_data.konfirmStatusBayar');

    Route::get('admin_data/view_benefit', [Admin_data::class, 'view_benefit'])->name('admin_data.view_benefit');
    Route::get('admin_data/view_totbenefit', [Admin_data::class, 'view_totbenefit'])->name('admin_data.view_totbenefit');

    Route::get('admin_data/chat', [Admin_data::class, 'chat'])->name('admin_data.chat');
    Route::get('admin_data/chat/{id_chat}', [Admin_data::class, 'chatDetail'])->name('admin_data.chatDetail');
    Route::post('admin_data/chat/kirim', [Admin_data::class, 'chatKirim'])->name('admin_data.chatKirim');
    Route::get('admin_data/chat/pesan/{id_chat}', [Admin_data::class, 'chatPesan'])->name('admin_data.chatPesan');

    Route::get('admin_laporan_mingguan/laporanStok', [Admin_laporan_mingguan::class, 'laporanStok'])->name('admin_laporan_mingguan.laporanStok');
    Route::get('admin_laporan_mingguan/filterStokByDate', [Admin_laporan_mingguan::class, 'filterStokByDate'])->name('admin_laporan_mingguan.filterStokByDate');
    Route::get('admin_laporan_mingguan/cetakLaporanStok', [Admin_laporan_mingguan::class, 'cetakLaporanStok'])->name('admin_laporan_mingguan.cetakLaporanStok');
    Route::get('admin_laporan_mingguan/exportExcel', [Admin_laporan_mingguan::class, 'exportExcel'])->name('admin_laporan_mingguan.exportExcel');
    Route::get('admin_laporan_mingguan/resetFilterStok', [Admin_laporan_mingguan::class, 'resetFilterStok'])->name('admin_laporan_mingguan.resetFilterStok');

    Route::get('admin_laporan_bulanan/laporanStok', [Admin_laporan_bulanan::class, 'laporanStok'])->name('admin_laporan_bulanan.laporanStok');
    // Route untuk GET dan POST
    Route::match(['get', 'post'], 'admin_laporan_bulanan/filterStokByMonth', [Admin_laporan_bulanan::class, 'filterStokByMonth'])->name('admin_laporan_bulanan.filterStokByMonth');
    Route::get('admin_laporan_bulanan/cetakLaporanStok', [Admin_laporan_bulanan::class, 'cetakLaporanStok'])->name('admin_laporan_bulanan.cetakLaporanStok');
    Route::get('admin_laporan_bulanan/exportExcel', [Admin_laporan_bulanan::class, 'exportExcel'])->name('admin_laporan_bulanan.exportExcel');
    Route::get('admin_laporan_bulanan/resetFilterStok', [Admin_laporan_bulanan::class, 'resetFilterStok'])->name('admin_laporan_bulanan.resetFilterStok');

    // Laporan Penjualan
    Route::get('admin_data/laporan_penjualan', [Admin_data::class, 'laporan_penjualan'])->name('admin_data.laporan_penjualan');
    Route::get('admin_data/grafik_penjualan', [Admin_data::class, 'grafikPenjualan'])->name('admin_data.grafikPenjualan');

    // Moderasi Ulasan
    Route::get('admin_data/moderasi_ulasan', [Admin_data::class, 'moderasi_ulasan'])->name('admin_data.moderasi_ulasan');
    Route::post('admin_data/verifikasi_ulasan/{id}', [Admin_data::class, 'verifikasi_ulasan'])->name('admin_data.verifikasi_ulasan');
    Route::post('admin_data/tolak_ulasan/{id}', [Admin_data::class, 'tolak_ulasan'])->name('admin_data.tolak_ulasan');

    // Retur / Pengembalian
    Route::get('admin_data/retur', [Admin_data::class, 'view_retur'])->name('admin_data.view_retur');
    Route::get('admin_data/retur/detail/{id_retur}', [Admin_data::class, 'detail_retur'])->name('admin_data.detail_retur');
    Route::post('admin_data/retur/verifikasi/{id_retur}', [Admin_data::class, 'verifikasi_retur'])->name('admin_data.verifikasi_retur');
    Route::post('admin_data/retur/terima/{id_retur}', [Admin_data::class, 'terima_retur'])->name('admin_data.terima_retur');
    Route::post('admin_data/retur/selesai/{id_retur}', [Admin_data::class, 'selesaikan_retur'])->name('admin_data.selesaikan_retur');

});

// Rute untuk register (filter berdasarkan level)  
Route::get('auth/login_pelanggan', [Auth::class, 'login_pelanggan'])->name('auth.login_pelanggan');
Route::post('auth/logout_pelanggan', [Auth::class, 'logout_pelanggan'])->name('auth.logout_pelanggan');
Route::post('auth/cek_login_pelanggan', [Auth::class, 'cek_login_pelanggan'])->name('auth.cek_login_pelanggan');
Route::get('auth/google/redirect', [Auth::class, 'redirectGoogle'])->name('auth.google.redirect');
Route::get('auth/google/callback', [Auth::class, 'callbackGoogle'])->name('auth.google.callback');
Route::get('auth/register_pelanggan', [Auth::class, 'register_pelanggan'])->name('auth.register_pelanggan');
Route::post('auth/save_pelanggan', [Auth::class, 'save_pelanggan'])->name('auth.save_pelanggan');
// Halaman lupa password - GET
Route::get('auth/lupa_password', [Auth::class, 'lupa_password'])->name('auth.lupa_password');
// Proses cek email + kirim link reset (token) - POST
Route::post('auth/cek_proses', [Auth::class, 'cek_proses'])->name('auth.cek_proses');
// Reset Password View - GET dengan token
Route::get('auth/reset_password/{token}', [Auth::class, 'reset_password'])->name('auth.reset_password');
// Proses ganti password - POST dengan token
Route::post('auth/ganti_password/{token}', [Auth::class, 'ganti_password'])->name('auth.ganti_password');

// Lupa password STAFF (superadmin / pemilik / admin)
Route::get('auth/lupa_password_user', [Auth::class, 'lupa_password_user'])->name('auth.lupa_password_user');
Route::post('auth/cek_proses_user', [Auth::class, 'cek_proses_user'])->name('auth.cek_proses_user');
Route::get('auth/reset_password_user/{token}', [Auth::class, 'reset_password_user'])->name('auth.reset_password_user');
Route::post('auth/ganti_password_user/{token}', [Auth::class, 'ganti_password_user'])->name('auth.ganti_password_user');

// Rute untuk toko (filter berdasarkan level)  
Route::get('home_toko/view_toko/{nama_toko}', [Home_toko::class, 'view_toko'])->name('view.toko');
Route::get('home_toko/katalog', [Home_toko::class, 'katalog'])->name('home_toko.katalog');
Route::get('home_toko/jenisProduk/{jenis_produk?}', [App\Http\Controllers\Home_toko::class, 'jenisProduk'])->name('home_toko.jenisProduk');
Route::get('home_toko/detail_produk/{nama_produk}', [Home_toko::class, 'detail_produk'])->name('home_toko.detail_produk');
Route::get('home_toko/ambilStok', [Home_toko::class, 'ambilStok'])->name('home_toko.ambilStok');
Route::get('home_toko/syaket', [Home_toko::class, 'syaket'])->name('home_toko.syaket');
Route::get('home_toko/bantuan', [Home_toko::class, 'bantuan'])->name('home_toko.bantuan');

// Flash sale public
Route::get('flash-sale', [App\Http\Controllers\Pelanggan_data::class, 'flashSale'])->name('pelanggan_data.flashSale');

// Sitemap
Route::get('sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Rute untuk pemilik (filter berdasarkan level)  
Route::middleware([FilterPelanggan::class])->group(
    function () {
        Route::get('pelanggan_data/profil', [Pelanggan_data::class, 'profil'])->name('pelanggan_data.profil');
        Route::get('pelanggan_data/profil/edit/{id}', [Pelanggan_data::class, 'edit'])->name('pelanggan_data.edit');
        Route::post('pelanggan_data/update_profile/{id}', [Pelanggan_data::class, 'update_profile'])->name('pelanggan_data.update_profile');

        Route::get('pelanggan_data/cart', [Pelanggan_data::class, 'cart'])->name('pelanggan_data.cart');
        Route::get('pelanggan_data/get_cart', [Pelanggan_data::class, 'getCart'])->name('pelanggan_data.get_cart');
        Route::post('pelanggan_data/add_to_cart', [Pelanggan_data::class, 'add_to_cart'])->name('pelanggan_data.add_to_cart');
        Route::post('pelanggan_data/update_cart/{id_keranjang}', [Pelanggan_data::class, 'update_cart'])->name('pelanggan_data.update_cart');
        Route::delete('pelanggan_data/remove_from_cart/{id_keranjang}', [Pelanggan_data::class, 'remove_from_cart'])->name('pelanggan_data.remove_from_cart');
        Route::delete('pelanggan_data/deleteCart/{id_keranjang}', [Pelanggan_data::class, 'deleteCart'])->name('pelanggan_data.deleteCart');
        Route::get('pelanggan_data/checkout', [Pelanggan_data::class, 'checkout'])->name('pelanggan_data.checkout');

        Route::get('pelanggan_data/beli', [Pelanggan_data::class, 'beli'])->name('pelanggan_data.beli');
        Route::get('pelanggan_data/search_destination', [Pelanggan_data::class, 'search_destination'])->name('pelanggan_data.search_destination');
        Route::get('pelanggan_data/refine_alamat', [Pelanggan_data::class, 'refine_alamat'])->name('pelanggan_data.refine_alamat');
        Route::post('pelanggan_data/hitung_ongkir', [Pelanggan_data::class, 'hitung_ongkir'])->name('pelanggan_data.hitung_ongkir');
        Route::post('pelanggan_data/hitung_ongkir_lokal', [Pelanggan_data::class, 'hitung_ongkir_lokal'])->name('pelanggan_data.hitung_ongkir_lokal');
        Route::get('/generateKodeTransaksi', [Pelanggan_data::class, 'generateKodeTransaksi'])->name('pelanggan_data.generateKodeTransaksi');
        Route::post('pelanggan_data/saveBeli', [Pelanggan_data::class, 'saveBeli'])->name('pelanggan_data.saveBeli');

        // Correct route definition
        Route::get('pelanggan_data/beliLangsung/{id_stok}', [Pelanggan_data::class, 'beliLangsung'])
            ->name('pelanggan_data.beliLangsung');

        Route::post('pelanggan_data/saveBeliLangsung', [Pelanggan_data::class, 'saveBeliLangsung'])
            ->name('pelanggan_data.saveBeliLangsung');

        Route::get('pelanggan_data/notaPembelian/{id_beli}', [Pelanggan_data::class, 'notaPembelian'])->name('pelanggan_data.notaPembelian');
        Route::get('pelanggan_data/unduhNotaPdf/{id_beli}', [Pelanggan_data::class, 'unduhNotaPdf'])->name('pelanggan_data.unduhNotaPdf');

        Route::get('pelanggan_data/statusBayar', [Pelanggan_data::class, 'statusBayar'])->name('pelanggan_data.statusBayar');
        Route::get('pelanggan_data/statusKirim', [Pelanggan_data::class, 'statusKirim'])->name('pelanggan_data.statusKirim');;

        Route::get('pelanggan_data/tracking/{id_beli}', [pelanggan_data::class, 'tracking'])->name('pelanggan_data.tracking');
        Route::get('pelanggan_data/getTrackingStatus/{id_beli}', [pelanggan_data::class, 'getTrackingStatus'])->name('pelanggan_data.getTrackingStatus');

        Route::get('pelanggan_data/riwayatBeli', [Pelanggan_data::class, 'riwayatBeli'])->name('pelanggan_data.riwayatBeli');
        Route::post('pelanggan_data/simpanUlasan', [Pelanggan_data::class, 'simpanUlasan'])->name('pelanggan_data.simpanUlasan');
        Route::redirect('pelanggan_data/chat', '/home_toko/index');
        Route::post('pelanggan_data/chat/kirim', [Pelanggan_data::class, 'chatKirim'])->name('pelanggan_data.chatKirim');
        Route::get('pelanggan_data/chat/pesan/{id_chat}', [Pelanggan_data::class, 'chatPesan'])->name('pelanggan_data.chatPesan');
        Route::get('pelanggan_data/chat/daftar', [Pelanggan_data::class, 'chatDaftar'])->name('pelanggan_data.chatDaftar');
        Route::post('pelanggan_data/chat/buka', [Pelanggan_data::class, 'chatBuka'])->name('pelanggan_data.chatBuka');
        Route::get('pelanggan_data/lihatBayar/{id_bayar}', [Pelanggan_data::class, 'lihatBayar'])->name('pelanggan_data.lihatBayar');
        Route::get('pelanggan_data/addBayar/{id_bayar}', [Pelanggan_data::class, 'addBayar'])->name('pelanggan_data.addBayar');
        Route::post('pelanggan_data/saveBayar/{id_bayar}', [Pelanggan_data::class, 'saveBayar'])->name('pelanggan_data.saveBayar');

        Route::post('pelanggan_data/updateStatusOtomatis', [Pelanggan_data::class, 'updateStatusOtomatis'])->name('pelanggan_data.updateStatusOtomatis');

        // Kupon/Voucher
        Route::post('pelanggan_data/applyKupon', [Pelanggan_data::class, 'applyKupon'])->name('pelanggan_data.applyKupon');

        // Notifikasi
        Route::get('pelanggan_data/notifikasi', [Pelanggan_data::class, 'notifikasi'])->name('pelanggan_data.notifikasi');
        Route::get('pelanggan_data/notifikasi/read/{id}', [Pelanggan_data::class, 'markReadNotifikasi'])->name('pelanggan_data.markReadNotifikasi');

        // Wishlist
        Route::get('pelanggan_data/wishlist', [Pelanggan_data::class, 'wishlist'])->name('pelanggan_data.wishlist');
        Route::post('pelanggan_data/wishlist/add', [Pelanggan_data::class, 'addWishlist'])->name('pelanggan_data.wishlist.add');
        Route::delete('pelanggan_data/wishlist/delete/{id_wishlist}', [Pelanggan_data::class, 'deleteWishlist'])->name('pelanggan_data.wishlist.delete');

        // Retur / Pengembalian
        Route::get('pelanggan_data/retur', [Pelanggan_data::class, 'retur'])->name('pelanggan_data.retur');
        Route::get('pelanggan_data/retur/{id_retur}', [Pelanggan_data::class, 'detailRetur'])->name('pelanggan_data.detailRetur');
        Route::post('pelanggan_data/retur/ajukan', [Pelanggan_data::class, 'ajukanRetur'])->name('pelanggan_data.ajukanRetur');
        Route::post('pelanggan_data/retur/{id_retur}/kirim', [Pelanggan_data::class, 'kirimRetur'])->name('pelanggan_data.kirimRetur');
        Route::post('pelanggan_data/retur/{id_retur}/batal', [Pelanggan_data::class, 'batalkanRetur'])->name('pelanggan_data.batalkanRetur');
    }
);

// Midtrans Callback Routes (tanpa auth middleware)
Route::post('midtrans/callback', [MidtransCallbackController::class, 'callback'])->name('midtrans.callback');
Route::get('midtrans/finish', [MidtransCallbackController::class, 'finish'])->name('midtrans.finish');
Route::get('midtrans/unfinish', [MidtransCallbackController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('midtrans/error', [MidtransCallbackController::class, 'error'])->name('midtrans.error');

// Midtrans Status Check (dengan auth)
Route::middleware([FilterPelanggan::class])->group(function () {
    Route::get('midtrans/check-status/{id_bayar}', [MidtransCallbackController::class, 'checkStatus'])->name('midtrans.checkStatus');
});
