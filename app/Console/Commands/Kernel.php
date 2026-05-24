<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // // ✅ Reset harian — setiap hari jam 00:00
        // $schedule->command('reset:harian')->dailyAt('00:00');

        // // ✅ Reset mingguan — setiap Senin jam 00:01
        // $schedule->command('reset:mingguan')->weeklyOn(1, '00:01');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
    
}