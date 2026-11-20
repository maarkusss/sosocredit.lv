<?php

namespace App\Services;

use Illuminate\Http\Request;
use Throwable;

class GeoIp
{
    public function __construct(protected string $base_url = 'https://geoip.goodday.group')
    {
    }

    public function getCountryCodeFromRequest(Request $request): string
    {
        return $this->getCountryCodeByIp($request->ip());
    }

    public function getCountryCodeByIp(string $ip): string
    {
        $country_code = '';

        try {
            $curl = curl_init();

            $opt_array = [
                CURLOPT_URL => $this->base_url . '/?ip=' . $ip,
                CURLOPT_RETURNTRANSFER => true
            ];
            curl_setopt_array($curl, $opt_array);

            $result = curl_exec($curl);
            $result = json_decode($result);

            if ($result && $result->countryCode) {
                $country_code = $result->countryCode;
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $country_code;
    }
}
