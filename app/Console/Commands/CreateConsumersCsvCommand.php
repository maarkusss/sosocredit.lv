<?php

namespace App\Console\Commands;

use App\Models\Consumer;
use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateConsumersCsvCommand extends Command
{
    protected $signature = 'consumers:create-csv';

    protected $description = 'Create CSV with Consumers';

    public function handle(): void
    {
        $fp = fopen('storage/app/' . s1() . 'all_consumers.csv', 'w');

        $list = [
            ['Email', 'Phone', 'First Name', 'Last Name', 'Country', 'Zip'],
        ];
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        Consumer::query()
//            ->where('confirmed', 1)
            ->orderBy('id', 'DESC')
            ->chunk(250, function ($consumers) use ($fp) {
                foreach ($consumers as $consumer) {
                    if (Str::contains($consumer->email, '@goodday.group')) {
                        continue;
                    }

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
                        'lv',
                        '',
                    ]);
                }
            });

        fclose($fp);

        $this->info('Consumers CSV has been created');
    }
}
