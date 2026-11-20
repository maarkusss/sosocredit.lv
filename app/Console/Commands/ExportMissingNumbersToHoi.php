<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\HoiFm\HoiFmService;
use Goodday\Common\Services\UserAgent;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ExportMissingNumbersToHoi extends Command
{
    protected $signature = 'applications:export-missing';

    protected $description = 'Export missing numbers to hoi';

    protected Client $client;

    protected UserAgent $userAgent;

    protected string $sms_project_token;

    protected string $user_token;

    public function handle(HoiFmService $hoi_fm): void
    {
        $this->client = new Client([
            'verify' => false,
        ]);

        $this->userAgent = new UserAgent();

        $this->sms_project_token = config('services.hoi_fm.sms_project_token', '');

        $this->user_token = config('services.hoi_fm.user_token', '');

        Application::where('reexport_to_hoi', 1)
            ->where('reexported', 0)
            ->where('created_at', '>', '2024-05-10 00:00:00')
            ->orderBy('id', 'ASC')
            ->chunk(10, function ($applications) use ($hoi_fm) {
                /** @var Application $application */
                foreach ($applications as $application) {
                    $this->info('Exporting [' . $application->id . '] ' . $application->phone);

                    $user_agent = $this->userAgent->getUserAgentData($application->user_agent);

                    $response = $this->client->request('POST', 'https://hoi.fm/api/sms', [
                        'query' => [
                            'name' => $application->first_name,
                            'number' => phone_extension() . $application->phone,
                            'fingerprint' => $application->fingerprint,
                            'registration' => 1,
                            'token' => $application->hoi_token,
                            'device' => $user_agent['device'],
                            'os' => $user_agent['os'],
                            'browser' => $user_agent['browser'],
                            'lang' => $application->lang,
                            'date' => $application->created_at->format('Y-m-d H:i:s'),
                        ],
                        'headers' => [
                            'usertoken' => $this->user_token,
                            'projecttoken' => $this->sms_project_token,
                        ],
                    ]);

                    if ($response->getStatusCode() !== 201) {
                        $this->error('Status: ' . $response->getStatusCode());
                    }

                    $application->reexported = 1;
                    $application->reexported_at = now();
                    $application->timestamps = false;
                    $application->saveQuietly();
                }

                exit('Exported 10 applications' . PHP_EOL);
            });
    }
}
