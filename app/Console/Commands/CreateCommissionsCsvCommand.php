<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Consumer;
use App\Models\Application;
use App\Services\GoodAff\GoodAffStatus;
use Illuminate\Console\Command;

class CreateCommissionsCsvCommand extends Command
{
    protected $signature = 'commissions:create-csv';

    protected $description = 'Create CSV with Commissions bound to Consumer';

    public function handle(): void
    {
        $fp = fopen('storage/app/' . s1() . 'komisijas_consumers.csv', 'w');

        $list = [
            ['Email', 'Phone', 'First Name', 'Last Name', 'Country', 'Zip'],
        ];
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        $commissions = Commission::query()
            ->where('consumer_id', '>', 0)
            ->where('status', GoodAffStatus::APPROVED)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($commissions as $commission) {
            $consumer = Consumer::find($commission->consumer_id);
            /** @var Consumer $consumer */

            $email = $consumer->email;
            $first_name = '';
            $last_name = '';

            $last_application = Application::query()->where('consumer_id', $consumer->id)->orderBy('id', 'DESC')->first();
            if (!empty($last_application->id)) {
                $first_name = $last_application->first_name;
                $last_name = $last_application->last_name;

                if (empty($email)) {
                    $email = $last_application->email;
                }
            }

            fputcsv($fp, [
                $email,
                $consumer->phone,
                $first_name,
                $last_name,
                config('app.country_code'),
                '',
            ]);
        }

        fclose($fp);

        $this->info('Commissions CSV has been created');
    }
}
