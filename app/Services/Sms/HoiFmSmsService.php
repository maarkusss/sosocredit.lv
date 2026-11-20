<?php

namespace App\Services\Sms;

use App\Contracts\Consumerable;
use App\Contracts\ConsumerSmsServiceInterface;
use App\Contracts\SendSmsInterface;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\Sms\Messages\OfferLinkMessage;
use App\Services\Sms\Messages\VerificationCodeMessage;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class HoiFmSmsService implements ConsumerSmsServiceInterface, SendSmsInterface
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'connect_timeout' => 10,
            'timeout' => 30,
            'verify' => false,
        ]);
    }

    public function sendVerificationCodeToConsumer(Consumerable $consumer, bool $sendWhatsApp = false)
    {
        $testUser = $consumer->phone == env('API_TEST_PHONE_NUMBER') && $consumer->email == env('API_TEST_EMAIL');

        if($testUser){
            Log::info('Test user verification - ', [
                'phone' => $consumer->phone,
                'email' => $consumer->email,
                'time'  => now(),
            ]);
            $rand_code = '123456';
        }else{
            // Prevent duplicate execution
            $recentSms = ConsumerSMSVerificationCode::where('phone_number', $consumer->phone)
            ->where('created_at', '>', now()->subSeconds(3))
            ->exists();

            if ($recentSms) {
                Log::info('SMS verification skipped - recent SMS exists', [
                    'phone' => $consumer->phone
                ]);
                return; // Exit early
            }

            $rand_code = rand(100000, 999999);
        }
       
        $verification_code = ConsumerSMSVerificationCode::create([
            'phone_number' => $consumer->phone,
            'code' => $rand_code,
            'expires_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+15 minutes')),
        ]);

        if(!$testUser){
            /** @var ConsumerSMSVerificationCode $verification_code */
            $message = new VerificationCodeMessage($verification_code);

            $alpha_name = $message->sender;
            $messageText = $message->message;
            $code = $verification_code->code;

            dispatch(function () use ($sendWhatsApp, $consumer, $code, $alpha_name, $messageText) {
                if ($sendWhatsApp) {
                    $this->sendWhatsAppTemplate($consumer, $code);
                }

                if (config('app.send_sms_via_rc')) {
                    $this->sendViaRC($consumer, $alpha_name, $messageText);
                } else {
                    $this->sendViaHoiFm($consumer, $alpha_name, $messageText);
                }
            })->afterResponse();
        }
    }

    public function sendOfferLinkToConsumer(Consumerable $consumer)
    {
        $message = new OfferLinkMessage($consumer->hoi_token);

        $alpha_name = $message->sender;
        $messageText = $message->message;

        dispatch(function () use ($consumer, $alpha_name, $messageText) {
            $this->sendViaHoiFm($consumer, $alpha_name, $messageText);
        })->afterResponse();
    }

    protected function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'usertoken' => config('services.hoi_fm.user_token'),
            'projecttoken' => config('services.hoi_fm.sms_project_token'),
        ];
    }

    protected function whatsAppHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.whatsapp.api_key'),
        ];
    }

    protected function rcHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'usertoken' => config('services.rc.user_token'),
        ];
    }

    protected function sendViaHoiFm(Consumerable $consumer, string $alpha_name, string $message): void
    {
        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[HoiFmSmsService]: Not sending SMS due to local environment', [
                'consumer_id' => $consumer->id,
                'message' => $message,
            ]);
            return;
        }

        $endpoint = config('services.hoi_fm.api_url') . '/send/sms';

        $recipient = phone_extension() . $consumer->phone;

        try {
            $this->client->post($endpoint, [
                'headers' => $this->headers(),
                'timeout' => 0,
                'form_params' => [
                    'alpha_name' => $alpha_name,
                    'country_code' => config('app.country_code'),
                    'message' => $message,
                    'recipient' => $recipient,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function sendViaRC(Consumerable $consumer, string $alpha_name, string $message): void
    {
        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[HoiFmSmsService]: Not sending SMS due to local environment', [
                'consumer_id' => $consumer->id,
                'message' => $message,
            ]);
            return;
        }

        $endpoint = config('services.rc.base_url') . '/api/v1/send/sms';
    
        $recipient = phone_extension() . $consumer->phone;

        $device= config('services.rc.sms_device') ?? null;
        $port= config('services.rc.sms_port') ?? null;

        try {
            /** @var ResponseInterface $response */
            $response = $this->client->post($endpoint, [
                'headers' => $this->rcHeaders(),
                'connect_timeout' => 4,
                'timeout' => 8,
                'http_errors' => false,
                'form_params' => [
                    'phone' => $recipient,
                    'message' => $message,
                    'device' => $device,
                    'port' => $port,
                ],
            ]);

            $status = $response->getStatusCode();

            if ($status !== 201) {
                Log::warning('[HoiFmSmsService]: RC did not return 201, falling back to HoiFm', [
                    'consumer_id' => $consumer->id,
                    'status' => $status,
                    'body' => (string) $response->getBody(),
                ]);

                $this->sendViaHoiFm($consumer, $alpha_name, $message);
                return;
            }

        } catch (Throwable $e) {
            report($e);

            Log::warning('[HoiFmSmsService]: RC request failed, falling back to HoiFm', [
                'consumer_id' => $consumer->id,
                'error' => $e->getMessage(),
            ]);

            $this->sendViaHoiFm($consumer, $alpha_name, $message);
        }
    }

    protected function sendWhatsAppTemplate(Consumerable $consumer, string $verificationCode): void
    {
        if (!app()->isProduction() && config('app.send_sms_from_local_env') !== true) {
            Log::debug('[HoiFmSmsService]: Not sending WhatsApp message due to local environment', [
                'consumer_id' => $consumer->id,
                'verification_code' => $verificationCode,
            ]);
            return;
        }

        $endpoint = config('services.whatsapp.api_url');
        $recipient = $this->formatPhoneNumber($consumer->phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $recipient,
            'type' => 'template',
            'template' => [
                'name' => 'sosocredit_lv_2fa_auth',
                'language' => [
                    'code' => app()->getLocale() ?? 'lv',
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $verificationCode
                            ]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $verificationCode
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->client->post($endpoint, [
                'headers' => $this->whatsAppHeaders(),
                'json' => $payload,
                'timeout' => 30,
            ]);
        } catch (Throwable $e) {
            Log::error('[HoiFmSmsService]: Failed to send WhatsApp message', [
                'consumer_id' => $consumer->id,
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);

            report($e);
        }

    }
    public function sendSms(string $country_code, string $phone, string $alpha_name, string $message): bool
    {
        if (!app()->isProduction()) {
            return 200;
        }

        $endpoint = config('services.hoi_fm.api_url') . '/send/sms';

        $recipient = phone_extension() . $phone;

        try {
            $response = $this->client->post($endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'usertoken' => config('services.hoi_fm.user_token'),
                    'projecttoken' => config('services.hoi_fm.sms_project_token'),
                ],
                'timeout' => 0,
                'form_params' => [
                    'alpha_name' => $alpha_name,
                    'country_code' => $country_code,
                    'message' => $message,
                    'recipient' => $recipient,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if not present
        if (!str_starts_with($phone, '371')) {
            $phone = '371' . $phone;
        }

        return $phone;
    }

    protected function checkWaMsgStatus(string $phone): bool
    {
        sleep(2); // wait for hoifm to obtain status

        $endpoint = 'https://www.hoi.fm/api/whatsapp/response/';
        $phone_id = config('services.whatsapp.phone_id');
        $recipient = $this->formatPhoneNumber($phone);

        $url = $endpoint . $recipient . '/' . $phone_id;

        try {
            $response = $this->client->get($url);
        } catch (Throwable $e) {
            Log::error('[HoiFmSmsService]: Failed to retrieve Wa delivery status from HoiFm', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);
        }

        $response_array = json_decode($response->getBody()->getContents(), true);

        Log::info('Whatsapp Status: ', [$response_array]);

        if (isset($response_array['data']) && !empty($response_array['data']))
        {
            $message_data = $response_array['data'][0];

            $last_status = $message_data['last_status'];
            $created_at = $message_data['created_at'];
            $last_category = $message_data['last_category'];

            $created_datetime = new DateTime($created_at);
            $current_datetime = new DateTime();

            $diff_seconds = $current_datetime->getTimestamp() - $created_datetime->getTimestamp();

            if ($diff_seconds > 5 || $last_category != 'authentication') {
                return false; // first check if it is fresh auth msg
            }

            if ($last_status != 'failed') {
                return true; // is fresh auth, not failed msg
            }
        }

        return false;
    }
}
