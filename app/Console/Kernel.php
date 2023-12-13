<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\GetArticleFromNewsAPi;
use App\Jobs\GetArticleFromNewsDataApi;
use App\Jobs\GetArticleFromTheGuardianApi;
use DateTimeZone;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new GetArticleFromNewsAPi)->dailyAt('6:22');
        $schedule->job(new GetArticleFromNewsDataApi)->dailyAt('6:22');
        $schedule->job(new GetArticleFromTheGuardianApi)->dailyAt('6:22');
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

    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'America/Chicago';
    }
}
