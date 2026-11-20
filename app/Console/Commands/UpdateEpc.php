<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Models\Commission;
use App\Models\Lender;
use App\Traits\SupportsMsort;
use Illuminate\Console\Command;

class UpdateEpc extends Command
{
    use SupportsMsort;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epc:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Lenders statistics';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $lenders = Lender::all();

        foreach ($lenders as $lender) {
            /** @var Lender $lender */
            $this->info('Lender - ' . $lender->name);
            $commissions_count = 0;
            if ($lender->epc_before || $lender->daily_epc) {
                $date_until = date("Y-m-d 23:59:59", strtotime($lender->daily_epc_before . ' days'));
                $date_from = date("Y-m-d 00:00:00", strtotime('-' . $lender->daily_epc . ' days', strtotime($date_until)));

                $commissions_count = Commission::query()
                    ->approved()
                    ->where('lender_id', $lender->id)
                    ->where('commission_date', '>=', $date_from)
                    ->where('commission_date', '<=', $date_until)
                    ->sum('commission');

                $click_count = Click::query()
                    ->where('lender_id', $lender->id)
                    ->where('created_at', '>=', $date_from)
                    ->where('created_at', '<=', $date_until)
                    ->distinct('visitor_id')
                    ->count();

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
                ];

                $new_lenders_arr = $this->msort(
                    'epc',
                    SORT_DESC,
                    SORT_NUMERIC,
                    'commissions_count',
                    SORT_DESC,
                    SORT_NUMERIC,
                    'clicks_count',
                    SORT_DESC,
                    SORT_NUMERIC,
                    $lenders_arr
                );

                $i = 1;
                foreach ($new_lenders_arr as $value) {
                    Lender::query()
                        ->where('id', $value['id'])
                        ->update([
                            'position' => $i,
                            'epc' => $value['epc'],
                            'earnings' => $value['commissions_count'],
                            'clicks' => $value['clicks_count']
                        ]);
                    $i++;
                }
            }

            $this->comment('Commission count = ' . $commissions_count);
            $this->comment('EPC = ' . $epc);
            $this->newLine();
        }
    }
}
