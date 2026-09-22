<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\M_Pembayaran;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Batalkan otomatis semua transaksi yang melewati batas waktu bayar';

    public function handle()
    {
        $count = M_Pembayaran::batalkanSemuaTransaksiExpired();

        $this->info($count . ' transaksi kadaluarsa berhasil dibatalkan.');
    }
}