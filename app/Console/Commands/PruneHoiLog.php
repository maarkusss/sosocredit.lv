<?php

namespace App\Console\Commands;

use App\Models\HoiLog;
use Illuminate\Console\Command;

class PruneHoiLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hoi-log:prune {--hours=48 : The number of hours to retain Hoi logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale entries from the Hoi logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info(HoiLog::prune(now()->subHours($this->option('hours'))) . ' entries pruned.');

        return Command::SUCCESS;
    }
}
