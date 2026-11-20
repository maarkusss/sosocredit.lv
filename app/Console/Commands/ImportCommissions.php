<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Commission;
use App\Models\Lender;
use App\Models\LenderCampaign;
use App\Services\GoodAff\GoodAffService;
use Illuminate\Console\Command;

class ImportCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports commissions from Goodaff';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(GoodAffService $goodaff_service)
    {
        $date_from = date("Y-m-d", strtotime("-7 days"));
        $date_to = date("Y-m-d");

        $commissions = $goodaff_service->getCommissions($date_from, $date_to);
        if (!$commissions) {
            $this->error('No data returned from GoodAff');
            return;
        }

        $this->warn('Retrieved data from GoodAff, importing commissions...');
        $goodaff_lenders = LenderCampaign::query()
            ->orderBy('id', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        foreach ($commissions as $data) {
            $commission = Commission::query()->where('uid', $data->id)->first();

            if ($data->campaign_id && array_key_exists($data->campaign_id, $goodaff_lenders)) {
                $explode_name = explode(" - ", $goodaff_lenders[$data->campaign_id]);
                $this->info('Campaign ID: ' . $data->campaign_id . ' | Name: ' . $explode_name[0]);
                if ($commission) {
                    if ($commission->status != $data->status) {
                        $commission->update([
                            'status' => $data->status,
                            'commission_date' => date('Y-m-d H:i:s'),
                        ]);

                        if (empty($commission->s4) && !empty($data->s4)) {
                            $commission->update([
                                's4' => $data->s4,
                            ]);
                        }

                        if (empty($commission->s7) && !empty($data->s7)) {
                            $commission->update([
                                's7' => $data->s7,
                            ]);
                        }
                    }
                } else {
                    if (array_key_exists($data->campaign_id, $goodaff_lenders) && $data->commission > 0) {
                        $lender = Lender::query()->where('name', 'like', $explode_name[0] . '%')->first();
                        $application = Application::query()->where('goodaff_click_id', $data->click_id)->first();

                        if(empty($application)){
                            if(!empty($data->s9)){
                                $extension = phone_extension();
                                $phone_number = $data->s9;
                                
                                if (str_starts_with($phone_number, $extension)) {
                                    $phone_number = substr($phone_number, strlen($extension));
                                }

                                $application = Application::query()->where('phone', $phone_number)->first();
                            }
                        }

                        /** @var Lender $lender */
                        Commission::create([
                            'visitor_id' => $data->visitor_id,
                            'uid' => $data->id,
                            'transaction_id' => $data->transaction_id,
                            'commission' => $data->commission,
                            'lender_id' => !empty($lender->id) ? $lender->id : 0,
                            'referrer' => $data->click_referer,
                            'ip' => $data->click_ip,
                            'status' => $data->status,
                            'utm_campaign' => $data->s2,
                            'utm_source' => $data->s5,
                            'gclid' => $data->s3,
                            'commission_date' => $data->created_date,
                            's4' => $data->s4 ?? null,
                            's7' => $data->s7 ?? null,
                            'click_id' => $data->click_id ?? null,
                            'consumer_id' => !empty($application->consumer_id) ? $application->consumer_id : 0,
                        ]);
                    }
                }
            }
        }
        $this->info('Import completed.');
        $this->newLine();
    }
}
