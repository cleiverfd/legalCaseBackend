<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\EnviarCorreoDiario::class,
    ];
    
    protected function schedule(Schedule $schedule)
    {
    // $schedule->command('inspire')->hourly();
      $schedule->command('correo')->everyThreeMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
