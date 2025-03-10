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
        \App\Console\Commands\FetchInvoices::class,
        \App\Console\Commands\FetchTransactions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update:subscriptions')->hourly()->appendOutputTo(storage_path('logs/update_subscriptions.log'));
        $schedule->command('fetch:invoices')->everyMinute()->appendOutputTo(storage_path('logs/fetch_invoices.log'));
        $schedule->command('update:invoices')->hourly()->appendOutputTo(storage_path('logs/update_invoices.log'));
        $schedule->command('fetch:transactions')->everyMinute()->appendOutputTo(storage_path('logs/fetch_transactions.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
