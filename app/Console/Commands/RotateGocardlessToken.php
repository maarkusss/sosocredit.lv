<?php

namespace App\Console\Commands;

use App\Models\ApplicationGocardlessAccess;
use App\Services\NordigenService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Nordigen\NordigenPHP\Exceptions\NordigenExceptions\NordigenException;
use Throwable;

class RotateGocardlessToken extends Command
{
    protected $signature = 'gocardless:rotate';

    protected $description = 'Rotate Access token of Gocardless integration';

    private NordigenService $nordigen;

    public function handle(NordigenService $nordigen)
    {
        if (config('app.broker_enabled') !== true) {
            $this->warn('Doing nothing as Broker functionality is not enabled');
            return 0;
        }

        $this->nordigen = $nordigen;

        try {
            $this->nordigen->getListOfInstitutions('lv');
            $this->info('Successfully retrieved data with existing access tokens.');
            return 0;
        } catch (NordigenException $e) {
            if ($e->getCode() > 400 && $e->getCode() < 404) {
                $this->rotateExpiredAccessToken();
            }
        } catch (Throwable $e) {
            Log::error('RotateGocardlessToken error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        return 0;
    }

    protected function rotateExpiredAccessToken()
    {
        $this->warn('Rotating access token..');
        $refresh_token = ApplicationGocardlessAccess::getRefreshToken();

        try {
            $json = $this->nordigen->getClient()->refreshAccessToken($refresh_token);
            $new_access_token = !empty($json) && !empty($json['access']) ? $json['access'] : '';

            if (!empty($new_access_token)) {
                ApplicationGocardlessAccess::updateAccessToken($new_access_token);

                $this->info('Access token rotated successfully.');
                Log::info('Gocardless access token updated');
            }
        } catch (Throwable $e) {
            Log::debug('Gocardless refresh token', [$e]);
            $this->warn('Refresh token expired. Rotating refresh token.');
            if ($e->getCode() === 401) {
                $tokens = $this->nordigen->getClient()->createAccessToken();
                $new_refresh_token = $tokens['refresh'];
                $new_access_token =  $tokens['access'];

                if (!empty($new_access_token) && !empty($new_refresh_token)) {
                    ApplicationGocardlessAccess::updateBothTokens($new_access_token, $new_refresh_token);
                    $this->info('New tokens updated successfully');
                    Log::info('GoCardless access and refresh tokens updated');
                    return 0;
                } else {
                    $this->error('Refresh tokens not updated');
                    Log::error('Access or Refresh token error!');
                }
            } else {
                Log::error('Gocardless unknown error', [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]);
                return 1;
            }
        }
        return 0;
    }
}
