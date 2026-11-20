<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Models\Commission;
use App\Models\Statistics;
use App\Models\Visitor;
use Illuminate\Console\Command;

class UpdateStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Statistics update';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $visitors = Visitor::query()
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        $clicks = Click::query()
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        $commissions = Commission::query()
            ->where('commission_date', date('Y-m-d'))
            ->count();

        Statistics::updateOrCreate([
            'date' => date('d-m-Y'),
        ], [
            'clicks' => $clicks,
            'visitors' => $visitors,
            'commissions' => $commissions,
        ]);
    }
}
