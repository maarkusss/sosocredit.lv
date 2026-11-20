<?php

namespace App\Services\GoodAff;

use App\Models\Setting;
use GuzzleHttp\Client;

class GoodAffService
{
    private string $api_url;
    private string $api_key;

    private string $api_user;
    private string $api_user_token;

    public function __construct(private Client $client)
    {
        $this->api_url = config('services.goodaff.url');
        $this->api_key = config('services.goodaff.api_key');

        $this->api_user = config('services.goodaff.api_user');
        $this->api_user_token = config('services.goodaff.api_user_token');
    }

    public function withApiDetails(string $api_user, string $api_user_token): static
    {
        $this->api_user = $api_user;
        $this->api_user_token = $api_user_token;

        return $this;
    }

    public function getCampaignNames(): array
    {
        $endpoint = $this->getEndpoint('/campaigns/names');
        $endpoint .= '?' . http_build_query([
            'country' => config('services.goodaff.country'),
        ]);

        $options = [
            'headers' => [
                'apikey' => $this->api_key,
            ]
        ];

        $campaign_names = [];

        try {
            $response = $this->client->get($endpoint, $options);
            if (str_starts_with($response->getStatusCode(), '2')) {
                $data = json_decode($response->getBody());

                if (!empty($data)) {
                    foreach ($data as $id => $details) {
                        $campaign_names[$id] = [
                            'name' => $details->name,
                            'campaign_domain' => $details->campaign_domain,
                            'active' => $details->active,
                        ];
                    }
                }
            }
        } catch (\Exception $exception) {
            report($exception);
        }

        return $campaign_names;
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCommissions(string $date_from, string $date_to): array
    {
        $endpoint = $this->getEndpoint('/commissions');

        $prefix = Setting::retrieve('prefix');

        $commissions = [];

        try {
            $response = $this->client->post($endpoint, [
                'query' => [
                    'user' => $this->api_user,
                    'token' => $this->api_user_token,
                    'datefrom' => $date_from,
                    'dateto' => $date_to,
                    's1' => $prefix,
                ]
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $commissions = json_decode($response->getBody()) ?? [];
            }
        } catch (\Exception $exception) {
            report($exception);
        }

        return $commissions;
    }

    protected function getEndpoint(string $endpoint): string
    {
        $base_url = $this->api_url;
        if (str_ends_with($base_url, '/')) {
            $base_url = substr($this->api_url, 0, -1);
        }

        if (str_starts_with($endpoint, '/')) {
            $endpoint = substr($endpoint, 1);
        }

        return $base_url . '/' . $endpoint;
    }
}
