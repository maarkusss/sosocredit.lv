<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RcService
{
    protected string $base_url;
    protected ?string $project_token;

    protected ?string $user_token;

    public function __construct()
    {
        $this->base_url = config('services.rc.base_url');
        $this->project_token = config('services.rc.project_token');
        $this->user_token = config('services.rc.user_token');
    }

    public function subscribe(string $phone, ?string $first_name = null): void
    {
        $phone = strip_phone_number_formatting($phone);
        $phone = str_replace('_', '', $phone);

        if (empty($phone)) {
            return;
        }

        // $phone = phone_extension() . $phone;

        try {
            Http::acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->baseUrl($this->base_url)
                ->withHeaders([
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->project_token,
                ])->post('/api/v1/numbers', [
                    'phone' => $phone,
                    'first_name' => $first_name,
                    'autosubscribe' => 1,
                ]);
        } catch (Throwable $e) {
            Log::error('RcService error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unsubscribe(string $phone): void
    {
        $phone = strip_phone_number_formatting($phone);
        $phone = str_replace('_', '', $phone);

        if (empty($phone)) {
            return;
        }

        // $phone = phone_extension() . $phone;

        try {
            Http::acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->baseUrl($this->base_url)
                ->withHeaders([
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->project_token,
                ])->put('/api/v1/numbers/unsubscribe', [
                     'phone' => $phone,
                ]);
        } catch (Throwable $e) {
            Log::error('RcService error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
