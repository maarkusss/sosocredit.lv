<?php

namespace App\Console;

use App\Console\Commands\ExportCommissionsToGoogleSheets;
use App\Console\Commands\ExportConsumersToGoogleSheets;
use App\Console\Commands\ExportUnqualifiedConsumersToGoogleSheets;
use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\ImportCommissions;
use App\Console\Commands\ImportGoogleReviews;
use App\Console\Commands\PruneHoiLog;
use App\Console\Commands\RotateGocardlessToken;
use App\Console\Commands\UpdateEpc;
use App\Console\Commands\UpdateSortingEpc;
use Goodday\Common\Console\Commands\DownloadLenderCampaigns;
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
        DownloadLenderCampaigns::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(ImportCommissions::class)
            ->hourly();
        $schedule->command(UpdateEpc::class)
            ->everyTenMinutes();
        $schedule->command(UpdateSortingEpc::class)
            ->hourly();
        $schedule->command(GenerateSitemap::class)
            ->daily();
        $schedule->command(DownloadLenderCampaigns::class)
            ->dailyAt('10:20');
        $schedule->command(PruneHoiLog::class)
            ->dailyAt('01:30');
        $schedule->command(RotateGocardlessToken::class)
            ->everyThreeMinutes();
        $schedule->command(ExportConsumersToGoogleSheets::class)
            ->dailyAt('01:00');
        $schedule->command(ExportCommissionsToGoogleSheets::class)
            ->dailyAt('01:00');
        $schedule->command(ExportUnqualifiedConsumersToGoogleSheets::class)
            ->dailyAt('02:00');
        $schedule->command(ImportGoogleReviews::class)
            ->hourly();
      
        /**
         * Clear telescope logs
         */
        $schedule->command('telescope:prune --hours=168')
            ->hourly()
            ->runInBackground();
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
