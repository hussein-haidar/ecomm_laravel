<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update:status-pengiriman')->everyMinute();
        $schedule->command('orders:cancel-expired')->everyFiveMinutes()->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands'); // ← pastikan tidak ada spasi aneh
        require base_path('routes/console.php');
    }
}
