<?php

namespace App\Console;

use App\Jobs\BadmintonCrawler;
use App\Jobs\CrawlerWeeks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new BadmintonCrawler(6, 1))->weeklyOn(2, '1:01');
        $schedule->job(new BadmintonCrawler(7, 1))->weeklyOn(2, '1:05');
        $schedule->job(new BadmintonCrawler(8, 1))->weeklyOn(2, '1:10');
        $schedule->job(new BadmintonCrawler(9, 1))->weeklyOn(2, '1:15');
        $schedule->job(new BadmintonCrawler(10, 1))->weeklyOn(2, '1:20');
        $schedule->job(new CrawlerWeeks())->weeklyOn(2, '1:25');
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
