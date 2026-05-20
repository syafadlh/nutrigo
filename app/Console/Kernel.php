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
        // Generate menu harian jam 5 pagi
        $schedule->command('nutrigo:generate-menus')
            ->dailyAt('05:00');

        // Kirim reminder tiap menit
        $schedule->command('nutrigo:send-reminders')
            ->everyMinute();    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
