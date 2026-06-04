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

        // Kirim reminder masa berlaku reklame yang akan berakhir 10 hari lagi
        $schedule->command('permohonan:send-expiry-reminder --days=10')
            ->dailyAt('08:10')
            ->withoutOverlapping()
            ->description('Kirim reminder masa berlaku reklame H-10 ke pemohon');

        $schedule->command('permohonan:send-expiry-reminder --days=3')
            ->dailyAt('08:20')
            ->withoutOverlapping()
            ->description('Kirim reminder masa berlaku reklame H-3 ke pemohon');
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
