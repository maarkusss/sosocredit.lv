<?php

namespace App\Listeners\Applications;

use App\Events\ApplicationEvent;
use Carbon\Carbon;
use Goodday\Common\Services\UserAgent;
use Goodday\Common\Helpers\CommonHelpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpsertSubscribedApplicationInHoiFm
{
    public function __construct(protected UserAgent $user_agent)
    {
    }

    public function handle(ApplicationEvent $event): void
    {
        $application = $event->application;

        //        if (empty($application->subscribed)) {
        //            return;
        //        }

        $user_agent = $this->user_agent->getUserAgentData($application->utm['user_agent'] ?? null);

        $dob = '';
        if (!empty($application->age)) {
            try {
                $carbon = Carbon::createFromFormat('Y-m-d', $application->dob);
                if ($carbon && $carbon->isPast()) {
                    $dob = $carbon->format('Y-m-d');
                }
            } catch (Throwable $e) {
            }
        }

        try {
            $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'usertoken' => config('services.hoi_fm.user_token'),
                    'projectprefix' => CommonHelpers::s1(),
                ])
                ->connectTimeout(3)
                ->timeout(10)
                ->post('https://hoi.fm/api/universal/upsert/per-prefix', [
                    'email' => trim($application->email),
                    'phone' => trim($application->phone),
                    'fingerprint' => $application->fingerprint ?? '',
                    'name' => $application->first_name ?? '',
                    'first_name' => $application->first_name ?? '',
                    'last_name' => $application->last_name ?? '',
                    'personal_code' => $dob,
                    'token' => '',
                    'device' => $user_agent['device'] ?? '',
                    'os' => $user_agent['os'] ?? '',
                    'browser' => $user_agent['browser'] ?? '',
                    'lang' => config('app.locale'),
                ]);

            if (!$response->successful()) {
                Log::error('[UpsertInHoi]: Request was not successful', [
                    'application_id' => $application->id,
                    'body' => $response->body(),
                    'code' => $response->status(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('[UpsertInHoi]: An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
