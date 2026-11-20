<?php

namespace App\Services\HoiFm;

use App\Contracts\HoiFmServiceInterface;
use App\Entity\FirebaseDevice;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerDevice;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class HoiFmService implements HoiFmServiceInterface
{
    public const PROJECT_TYPE_SMS = 'sms';
    public const PROJECT_TYPE_EMAIL = 'email';
    public const PROJECT_TYPE_PUSH = 'push';
    public const NEW_TOKEN_LENGTH = 6;

    public const ALL = 'all';
    public const SUBSCRIBED_ONLY = 'subscribed';
    public const VERIFIED_ONLY = 'verified';

    private ?string $email_project_token;

    private ?string $push_project_token;
    private ?string $sms_project_token;
    private ?string $user_token;

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->email_project_token = config('services.hoi_fm.email_project_token', '');
        $this->push_project_token = config('services.hoi_fm.push_project_token', '');
        $this->sms_project_token = config('services.hoi_fm.sms_project_token', '');
        $this->user_token = config('services.hoi_fm.user_token', '');
    }

    /**
     * Generate new token for HoiFm.
     * Currently, always returns 6-chars-long-random-string
     */
    public function generateNewToken(): string
    {
        return Str::random(static::NEW_TOKEN_LENGTH);
    }

    public function addPhoneNumber($name, $first_name, $last_name, $personal_code, string $phone_number, string $hoi_token, array $user_agent, $fingerprint): void
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_SMS)) {
            return;
        }

        try {
            $payload = [
                'query' => [
                    'name' => $name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'personal_code' => $personal_code,
                    'number' => phone_extension() . $phone_number,
                    'fingerprint' => $fingerprint,
                    'registration' => 1,
                    'token' => $hoi_token,
                    'device' => $user_agent['device'],
                    'os' => $user_agent['os'],
                    'browser' => $user_agent['browser'],
                    'lang' => default_locale(),
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->sms_project_token,
                ],
            ];

            $this->client->request('POST', 'https://hoi.fm/api/sms', $payload);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function removePhoneNumber(string $phone_number): void
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_SMS)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/sms/' . phone_extension() . $phone_number;

        try {
            $this->client->request('DELETE', $endpoint, [
                'query' => [
                    'number' => phone_extension() . $phone_number,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->sms_project_token,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function updateHoiTokenForPhoneNumber(string $phone_number, string $new_hoi_token)
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_SMS)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/sms/' . phone_extension() . $phone_number;

        try {
            $this->client->request('PUT', $endpoint, [
                'query' => [
                    'token' => $new_hoi_token,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->sms_project_token,
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function unsubscribePhoneNumber(string $phone_number): void
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_SMS)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/sms/' . phone_extension() . $phone_number;

        try {
            $this->client->request('PUT', $endpoint, [
                'query' => [
                    'active' => 0,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->sms_project_token,
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function unsubscribePhoneNumberFromProjectPrefix(string $phone_number, string $project_prefix): void
    {
        $endpoint = 'https://hoi.fm/api/sms/per-prefix/' . phone_extension() . $phone_number . '/unsubscribe';

        try {
            $this->client->request('PUT', $endpoint, [
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projectprefix' => $project_prefix,
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function addEmail($name, string $email, string $hoi_token, array $user_agent, $fingerprint): void
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_EMAIL)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/mail';

        try {
            $this->client->request('POST', $endpoint, [
                'query' => [
                    'name' => $name,
                    'mail' => $email,
                    'fingerprint' => $fingerprint,
                    'registration' => 1,
                    'token' => $hoi_token,
                    'device' => $user_agent['device'],
                    'os' => $user_agent['os'],
                    'browser' => $user_agent['browser'],
                    'lang' => default_locale(),
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->email_project_token,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function removeEmail(string $email): void
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_EMAIL)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/mail/' . $email;

        try {
            $this->client->request('DELETE', $endpoint, [
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->email_project_token,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function unsubscribeEmail(string $email)
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_EMAIL)) {
            return;
        }

        $endpoint = 'https://hoi.fm/api/mail/' . $email;

        try {
            $this->client->request('PUT', $endpoint, [
                'query' => [
                    'active' => 0,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->email_project_token,
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function unsubscribeEmailFromProjectPrefix(string $email, string $project_prefix)
    {
        $endpoint = 'https://hoi.fm/api/mail/per-prefix/' . $email . '/unsubscribe';

        try {
            $this->client->request('PUT', $endpoint, [
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projectprefix' => $project_prefix,
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function reInsertConsumerEmail(Consumer $consumer)
    {
        $user_agent = get_consumer_user_agent_data();

        $this->removeEmail($consumer->email);
        $this->addEmail(
            name: $consumer->first_name,
            email: $consumer->email,
            hoi_token: $consumer->hoi_token,
            user_agent: $user_agent,
            fingerprint: $consumer->fingerprint
        );
    }

    public function reInsertConsumerPhone(Consumer $consumer)
    {
        $user_agent = get_consumer_user_agent_data();
    
        $this->removePhoneNumber($consumer->phone);
        $this->addPhoneNumber(
            name: $consumer->first_name,
            first_name: $consumer->first_name,
            last_name: $consumer->last_name,
            personal_code: $consumer->personal_code,
            phone_number: $consumer->phone,
            hoi_token: $consumer->hoi_token,
            user_agent: $user_agent,
            fingerprint: $consumer->fingerprint
        );
    }

    public function shouldExportEmailToHoi(Consumer $consumer): bool
    {
        if (!$consumer->email) {
            return false;
        }

        if (!in_array(config('hoi_fm.export_emails'), [static::ALL, static::VERIFIED_ONLY, static::SUBSCRIBED_ONLY], true)) {
            return false;
        }

        if (config('hoi_fm.export_emails') === static::ALL) {
            return true;
        }

        if (config('hoi_fm.export_emails') === static::VERIFIED_ONLY && $consumer->hasVerifiedEmail()) {
            return true;
        }

        if (config('hoi_fm.export_emails') === static::SUBSCRIBED_ONLY && $consumer->isSubscribedToEmailsAndSms()) {
            return true;
        }

        return false;
    }

    public function shouldExportPhoneToHoi(Consumer $consumer): bool
    {
        if (!$consumer->phone) {
            return false;
        }

        if (!in_array(config('hoi_fm.export_phone_numbers'), [static::ALL, static::VERIFIED_ONLY, static::SUBSCRIBED_ONLY], true)) {
            return false;
        }

        if (config('hoi_fm.export_phone_numbers') === static::ALL) {
            return true;
        }

        if (config('hoi_fm.export_phone_numbers') === static::VERIFIED_ONLY && $consumer->hasVerifiedPhone()) {
            return true;
        }

        if (config('hoi_fm.export_phone_numbers') === static::SUBSCRIBED_ONLY && $consumer->isSubscribedToEmailsAndSms()) {
            return true;
        }

        return false;
    }

    protected function hasHoiTokens(string $type): bool
    {
        if ($type === static::PROJECT_TYPE_EMAIL) {
            return $this->user_token && $this->email_project_token;
        }

        if ($type === static::PROJECT_TYPE_SMS) {
            return $this->user_token && $this->sms_project_token;
        }

        if ($type === static::PROJECT_TYPE_PUSH) {
            return $this->user_token && $this->push_project_token;
        }

        return false;
    }

    protected function shouldPerformRequest(string $type): bool
    {
        return $this->hasHoiTokens($type);
        // return $this->hasHoiTokens($type) && (app()->isProduction() || app()->environment('staging'));
    }

    public function unsubscribeConsumer(Consumer $consumer): void
    {
        if ($consumer->email) {
            $this->unsubscribeEmail($consumer->email);
        }

        if ($consumer->phone) {
            $this->unsubscribePhoneNumber($consumer->phone);
        }
    }

    /**
     * Push Project
     */
    public function addPushConsumer(Consumer $consumer): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        $endpoint = 'https://hoi.fm/api/push';

        return $this->exportConsumer($consumer, $endpoint, 'POST');
    }

    public function addPushDevice(ConsumerDevice $device): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        $endpoint = 'https://hoi.fm/api/push/devices';

        return $this->exportDevice($device, $endpoint, 'POST');
    }

    public function updatePushDevice(ConsumerDevice $device): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        $endpoint = 'https://hoi.fm/api/push/devices/device';

        return $this->exportDevice($device, $endpoint, 'PUT');
    }

    public function updatePushConsumer(Consumer $consumer): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        $endpoint = 'https://hoi.fm/api/push/consumer';

        return $this->exportConsumer($consumer, $endpoint, 'PUT');
    }

    public function upsertPushDevice(ConsumerDevice $device): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }
    
        $status = $this->addPushDevice($device);
        if ($status === 302) {
            $status = $this->updatePushDevice($device);
        }
    
        return $status;
    }

    public function deletePushDevice(ConsumerDevice $device): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        try {
            $response = $this->client->request('DELETE', 'https://hoi.fm/api/push/devices/' . $device->key, [
                'form_params' => [
                    'key' => $device->key,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->push_project_token,
                    'Accept' => 'application/json',
                ],
            ]);

            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
        }

        return 0;
    }

    public function upsertPushConsumer(Consumer $consumer): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }
    
        $status = $this->addPushConsumer($consumer);
        if ($status === 302) {
            $status = $this->updatePushConsumer($consumer);
        }
    
        return $status;
    }

    public function deletePushConsumer(Consumer $consumer): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        try {
            $response = $this->client->request('DELETE', 'https://hoi.fm/api/push/consumer', [
                'query' => $this->consumerFields($consumer),
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->push_project_token,
                    'Accept' => 'application/json',
                ]
            ]);

            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
        }

        return 0;
    }

    public function resetPushDevice(ConsumerDevice $device, string $push_notification_url = ''): int
    {
        $endpoint = 'https://hoi.fm/api/push/devices/reset';

        try {
            $response = $this->client->request('POST', $endpoint, [
                'form_params' => [
                    'key' => $device->key,
                    'push_notification_url' => $push_notification_url,
                ],
                'headers' => [
                    'usertoken' => config('services.hoi_fm.user_token'),
                    'projecttoken' => config('services.hoi_fm.push_project_token'),
                    'Accept' => 'application/json',
                ],
            ]);

            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
            // Log::error('API HoiFmService error', [
            //     'code' => $e->getCode(),
            //     'message' => $e->getMessage(),
            // ]);
        }

        return 0;
    }

    /**
     * This function is not used at the moment, as there is
     * no need to unsubscribe PUSH consumer in Hoi.fm
     *
     * @param Consumer $consumer
     * @return int
     */
    public function unsubscribePushConsumer(Consumer $consumer): int
    {
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        try {
            $response = $this->client->request('PUT', 'https://hoi.fm/api/push/consumer', [
                'query' => array_merge($this->consumerFields($consumer), [
                    'active' => 0,
                ]),
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->push_project_token,
                    'Accept' => 'application/json',
                ]
            ]);

            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
        }

        return 0;
    }

    protected function exportConsumer(Consumer $consumer, string $endpoint, string $method): int
    {
        $user_agent = get_consumer_user_agent_data();
    
        $post_request = array_merge($this->consumerFields($consumer), [
            'first_name' => $consumer->first_name,
            'last_name' => $consumer->last_name,
            'fingerprint' => $consumer->fingerprint,
            'registration' => 1,
            'token' => $consumer->hoi_token,
            'device' => $user_agent['device'],
            'os' => $user_agent['os'],
            'browser' => $user_agent['browser'],
            'lang' => default_locale(),
        ]);
    
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }

        if (!empty($consumer->devices_firebase)) {
            $devices_firebase = [];
            foreach ($consumer->devices_firebase as $device) {
                $firebase_device = new FirebaseDevice();
                $firebase_device->app_instance_id = $device->key;
                $firebase_device->fcm_token = $device->value;
                if (!empty($firebase_device->fcm_token)) {
                    $devices_firebase[] = $firebase_device->toJson();
                }
            }
            $post_request['devices_firebase'] = $devices_firebase;
        }
    
        try {
            $response = $this->client->request($method, $endpoint, [
                'form_params' => $post_request,
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->push_project_token,
                    'Accept' => 'application/json',
                ],
            ]);
    
            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
        }
    
        return 0;
    }

    protected function exportDevice(ConsumerDevice $device, string $endpoint, string $method): int
    {
        $post_request = [
            'key' => $device->key,
            'value' => $device->value,
        ];
    
        if (!$this->shouldPerformRequest(static::PROJECT_TYPE_PUSH)) {
            return -1;
        }
    
        try {
            $response = $this->client->request($method, $endpoint, [
                'form_params' => $post_request,
                'headers' => [
                    'usertoken' => $this->user_token,
                    'projecttoken' => $this->push_project_token,
                    'Accept' => 'application/json',
                ],
            ]);
    
            return $response->getStatusCode();
        } catch (Throwable $e) {
            report($e);
        }
    
        return 0;
    }


    protected function consumerFields(Consumer $consumer): array
    {
        $request = [];

        if (config('landing_form.require_email')) {
            $request['email'] = $consumer->email;
        }

        if (config('landing_form.require_phone')) {
            $request['phone_number'] = phone_extension() . $consumer->phone;
            $request['country_code'] = phone_extension();
        }

        return $request;
    }

    public function shortenUrl(string $full_url): string
    {
        $endpoint = config('services.hoi_fm.api_url') . '/shorten-url';

        try {
            $response = $this->client->request('POST', $endpoint, [
                'form_params' => [
                    'url' => $full_url,
                ],
                'headers' => [
                    'usertoken' => $this->user_token,
                ]
            ]);

            if (str_starts_with($response->getStatusCode(), '2')) {
                $body = json_decode($response->getBody());

                if ($body && $body->data && isset($body->data->url)) {
                    return $body->data->url;
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return '';
    }

    public function senderName(): string
    {
        return hoi_fm_sender_name() ? hoi_fm_sender_name() : $this->senderDomain();
    }

    public function senderDomain(): string
    {
        return config('services.hoi_fm.sender_domain') ? config('services.hoi_fm.sender_domain') : 'goodday.agency';
    }

    public function fromHeader(): string
    {
        $from_name = $this->senderName();
        $from_domain = $this->senderDomain();

        return ucfirst($from_name) . ' <info@' . $from_domain . ">";
    }
}
