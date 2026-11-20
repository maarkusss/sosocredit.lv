<?php

namespace App\Services\Sms;

use App\Contracts\Consumerable;
use App\Contracts\ConsumerSmsServiceInterface;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\Sms\Messages\OfferLinkMessage;
use App\Services\Sms\Messages\VerificationCodeMessage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class SmsCenterConsumerService implements ConsumerSmsServiceInterface
{
    private ?string $api_url;
    private ?string $login;
    private ?string $password;

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();

        $this->api_url = config('services.sms_center.api_url', '');
        $this->login = config('services.sms_center.login', '');
        $this->password = config('services.sms_center.password', '');
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

        $apiUrl = $this->api_url;
        $login = $this->login;
        $psw = $this->password;
        $message = new VerificationCodeMessage($verification_code);
        $sender = $message->sender;
        $mes = $message->message;
        $country_code = phone_extension();
        $phones = $country_code . $consumer->phone;
        $fmt = 3;

        $url = "{$apiUrl}?login={$login}&psw={$psw}&sender={$sender}&mes={$mes}&phones={$phones}&fmt={$fmt}";

        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[SmsCenterConsumerService]: Not sending SMS due to local environment');
            return;
        }

        if (!app()->isProduction() || !$this->hasSmsCenterCredentials()) {
            Log::debug('[SmsCenterConsumerService]: Not sending SMS due to missing tokens');
            return;
        }

        $this->client->request(Request::METHOD_POST, $url);
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
        $apiUrl = $this->api_url;
        $login = $this->login;
        $psw = $this->password;
        $message = new OfferLinkMessage($consumer->hoi_token);
        $sender = $message->sender;
        $mes = $message->message;
        $country_code = phone_extension();
        $phones = $country_code . $consumer->phone;
        $fmt = 3;

        $url = "{$apiUrl}?login={$login}&psw={$psw}&sender={$sender}&mes={$mes}&phones={$phones}&fmt={$fmt}";

        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[SmsCenterConsumerService]: Not sending SMS due to local environment');
            return;
        }

        if (!app()->isProduction() || !$this->hasSmsCenterCredentials()) {
            Log::debug('[SmsCenterConsumerService]: Not sending SMS due to missing tokens');
            return;
        }

        $this->client->request(Request::METHOD_POST, $url);
    }

    protected function hasSmsCenterCredentials(): bool
    {
        return !empty($this->login) && !empty($this->password);
    }

    protected function random_code(): string
    {
        return rand(100000, 999999);
    }
}
