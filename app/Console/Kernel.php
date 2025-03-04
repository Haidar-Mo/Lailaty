<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\CriminalRecord::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('Criminal-Record')->dailyAt('00:00');
        $schedule->command('Criminal-Record-Reminder')->dailyAt('00:00');

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
