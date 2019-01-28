<?php

namespace App\Console;

use App\Console\Commands\goodCommand;
use App\Console\Commands\orderCommand;
use App\Console\Commands\useCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ticketCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        goodCommand::class,
        orderCommand::class,
        useCommand::class,
        ticketCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('good:date')->dailyAt('00:00');
        $schedule->command('order:date')->everyMinute();
        $schedule->command('use:date')->dailyAt('00:00');
        $schedule->command('ticket:date')->dailyAt('00:00');
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
