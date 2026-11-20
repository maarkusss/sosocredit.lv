<?php

namespace App\Services\Sms;

use App\Contracts\Consumerable;
use App\Contracts\ConsumerSmsServiceInterface;
use App\Contracts\SendSmsInterface;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\Sms\Messages\OfferLinkMessage;
use App\Services\Sms\Messages\VerificationCodeMessage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsClubConsumerService implements ConsumerSmsServiceInterface, SendSmsInterface
{
    private ?string $token;
    private ?string $api_url;

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();

        $this->token = config('services.sms_club.token', '');
        $this->api_url = config('services.sms_club.api_url', '');
    }

    /**
     * Send an SMS with a verification code.
     *
     * @param Consumerable $consumer
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendVerificationCodeToConsumer(Consumerable $consumer): void
    {
        $verification_code = ConsumerSMSVerificationCode::create([
            'phone_number' => $consumer->phone,
            'code' => $this->random_code(),
            'expires_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+15 minutes')),
        ]);
        /** @var ConsumerSMSVerificationCode $verification_code */

        $url = $this->api_url;
        $token = $this->token;
        $country_code = phone_extension();
        $message = new VerificationCodeMessage($verification_code);
        $alpha_name = $message->sender;
        $message = $message->message;

        $body = json_encode([
            'phone' => [$country_code . $consumer->phone],
            'message' => $message,
            'src_addr' => $alpha_name,
        ]);

        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[SmsClubConsumerService]: Not sending SMS due to local environment');
            return;
        }

        if (!app()->isProduction() || !$this->hasSmsClubToken()) {
            Log::debug('[SmsClubConsumerService]: Not sending SMS due to missing tokens');
            return;
        }

        $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
        ]);
    }

    /**
     * Send an SMS with an offer link.
     *
     * @param Consumerable $consumer
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendOfferLinkToConsumer(Consumerable $consumer): void
    {
        $url = $this->api_url;
        $token = $this->token;

        $message = new OfferLinkMessage($consumer->hoi_token);
        $alpha_name = $message->sender;
        $message = $message->message;
        $country_code = phone_extension();

        $body = json_encode([
            'phone' => [$country_code . $consumer->phone],
            'message' => $message,
            'src_addr' => $alpha_name,
        ]);

        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[SmsClubConsumerService]: Not sending SMS due to local environment');
            return;
        }

        if (!app()->isProduction() || !$this->hasSmsClubToken()) {
            Log::debug('[SmsClubConsumerService]: Not sending SMS due to missing tokens');
            return;
        }

        try {
            $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'body' => $body,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function hasSmsClubToken(): bool
    {
        return !empty($this->token);
    }

    protected function random_code(): string
    {
        return rand(100000, 999999);
    }

    public function sendSms(string $country_code, string $phone, string $alpha_name, string $message): bool
    {
        $country_code = phone_extension();

        $body = json_encode([
            'phone' => [$country_code . $phone],
            'message' => $message,
            'src_addr' => $alpha_name,
        ]);

        try {
            $this->client->request('POST', $this->api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ],
                'body' => $body,
            ]);
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        return true;
    }
}
