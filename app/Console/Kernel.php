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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
            //$schedule->command('active:scheduled-product')->hourly();

       // $schedule->command('update:active-prod-qty')->everyMinute();
       // $schedule->command('update:hidden-prod-qty')->everyThirtyMinutes();
        $schedule->command('send:preorder-report-weekly')->sundays()->at('10:00');
        $schedule->command('send:preorder-report')->sundays()->at('11:00');
        $schedule->command('send:sms')->everyFiveMinutes();
        $schedule->command('update:prod-pre-order-qty')->daily()->at('09:00');
        $schedule->command('send:sms')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
