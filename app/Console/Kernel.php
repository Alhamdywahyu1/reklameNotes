<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Kirim reminder email setiap hari pada jam 08:00
        $schedule->command('permohonan:send-reminder')
            ->dailyAt('08:00')
            ->description('Kirim reminder untuk permohonan yang menunggu verifikasi');

        // Hanya menjalankan di production
        if (app()->environment('production')) {
            $schedule->command('permohonan:send-reminder')
                ->dailyAt('08:00')
                ->withoutOverlapping();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
