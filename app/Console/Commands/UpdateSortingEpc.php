<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Models\Commission;
use App\Models\Lender;
use App\Models\Lender\LenderSorting;
use App\Models\Lender\LenderSortingEpc;
use App\Models\Lender\LenderVisit;
use App\Models\Lender\LenderVisitSorting;
use App\Models\Setting;
use App\Traits\SupportsMsort;
use Illuminate\Console\Command;

class UpdateSortingEpc extends Command
{
    use SupportsMsort;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sorting:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates LendersSortingEpc statistics, deletes LendersSortingEpc with below 100 clicks, deletes Visits and VisitsSorting before 10 days';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sortings = LenderSorting::query()
            ->orderBy('updated_at', 'ASC')
            ->take(10)
            ->get();
        $lenders = Lender::all();
        /** @var LenderSorting $sorting */
        foreach ($sortings as $sorting) {
            $i = 1;
            $all_commissions_count = 0;
            $all_clicks_count = 0;
            $lenders_arr = [];

            $individual_days_epc = Setting::retrieve('individual_days_epc');

            //$clicks = Clicks::where('utm_campaign', $sorting->campaign_name)->distinct('visitor_id')->count();
            $this->info('Sorting - ' . $sorting->campaign_name);
            /** @var Lender $lender */
            foreach ($lenders as $lender) {
                $this->info('Lender - ' . $lender->name);

                $date_until = date("Y-m-d 23:59:59", strtotime($lender->daily_epc_before . ' days'));
                $date_from = date("Y-m-d 00:00:00", strtotime('-' . $individual_days_epc . ' days', strtotime($date_until)));

                $this->comment('Date until - ' . $date_until);
                $this->comment('Date from - ' . $date_from);

                $commissions_count = Commission::query()
                    ->approved()
                    ->where('lender_id', $lender->id)
                    ->where('commission_date', '>=', $date_from)
                    ->where('commission_date', '<=', $date_until)
                    ->where('utm_campaign', $sorting->campaign_name)
                    ->sum('commission');

                $all_commissions_count += $commissions_count;

                $click_count = Click::query()
                    ->where('lender_id', $lender->id)
                    ->where('created_at', '>=', $date_from)
                    ->where('created_at', '<=', $date_until)
                    ->where('utm_campaign', $sorting->campaign_name)
                    ->distinct('visitor_id')
                    ->count();

                $all_clicks_count += $click_count;

                if ($lender->guaranteed_epc) {
                    $epc = $lender->guaranteed_epc;
                } else {
                    $epc = ($click_count ? $commissions_count / $click_count : 0);
                }

                $lenders_arr[] = [
                    'id' => $lender->id,
                    'name' => $lender->name,
                    'commissions_count' => $commissions_count,
                    'clicks_count' => $click_count,
                    'epc' => $epc,
                    'lender_epc' => $lender->epc,
                ];
            }

            $new_lenders_arr = $this->msort(
                'epc',
                SORT_DESC,
                SORT_NUMERIC,
                'lender_epc',
                SORT_DESC,
                SORT_NUMERIC,
                'clicks_count',
                SORT_DESC,
                SORT_NUMERIC,
                $lenders_arr
            );

            foreach ($new_lenders_arr as $key => $value) {
                LenderSortingEpc::updateOrCreate([
                    'sorting_id' => $sorting->id,
                    'lender_id' => $value['id'],
                ], [
                    'position' => $i,
                    'epc' => $value['epc'],
                    'clicks' => $value['clicks_count'],
                    'earnings' => $value['commissions_count'],
                ]);

                $i++;
            }

            $sorting->update([
                'clicks' => $all_clicks_count,
                'average_epc' => $all_clicks_count ? $all_commissions_count / $all_clicks_count : 0,
            ]);
        }

        $sortings = LenderSorting::query()
            ->where('clicks', '<', 100)
            ->get();

        foreach ($sortings as $sorting) {
            LenderSortingEpc::query()
                ->where('sorting_id', $sorting->id)
                ->delete();

            $sorting->delete();
        }

        if ($this->isGoodTimeToCleanupVisitsAndVisitsSortings()) {
            LenderVisit::query()
                ->where('created_at', '<', date("Ymd", strtotime('-10 days')))
                ->delete();

            LenderVisitSorting::query()
                ->where('created_at', '<', date("Ymd", strtotime('-10 days')))
                ->delete();
        }
    }

    protected function isGoodTimeToCleanupVisitsAndVisitsSortings(): bool
    {
        return date("H") == 2 && date("i") < 10;
    }
}
