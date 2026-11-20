<?php

namespace App\Console\Commands;

use App\Models\Lender\LenderVisit;
use App\Models\Lender\LenderVisitSorting;
use Illuminate\Console\Command;

class UpdateVisitsBotStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visits:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update bot column in VisitsSorting models';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $non_bots_visits_sortings = LenderVisitSorting::query()
            ->whereNull('bot')
            ->get();

        /** @var LenderVisitSorting $visit_sorting */
        foreach ($non_bots_visits_sortings as $visit_sorting) {
            $visit_sorting->update([
                'bot' => LenderVisit::query()->where('id', $visit_sorting->visit_id)->first()->bot
            ]);
        }
    }
}
