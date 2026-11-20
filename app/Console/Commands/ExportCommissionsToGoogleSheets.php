<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Consumer;
use App\Models\Application;
use App\Services\GoodAff\GoodAffStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class ExportCommissionsToGoogleSheets extends Command
{
    protected $signature = 'commissions:export-google';
    protected $description = 'Export commissions to Google Sheets';

    public function handle()
    {
        $sheetId = env('GOOGLE_COMMISSIONS_SHEET_ID');

        // Load service account credentials
        $path = storage_path('app/google/credentials.json');

        if (!file_exists($path)) {
            $this->error("Credentials NOT found: " . $path);
            return;
        }

        $credentials = json_decode(file_get_contents($path), true);
        $token = $this->generateAccessToken($credentials);

        $client = new Client([
            'base_uri' => 'https://sheets.googleapis.com/v4/spreadsheets/',
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json'
            ]
        ]);

        // Clear the existing data in "commissions" sheet
        $client->post("{$sheetId}/values/commissions!A:Z:clear");

        // Header row
        $rows = [
            ['Email', 'Phone', 'First Name', 'Last Name', 'Country', 'Zip']
        ];

        // Fetch commissions
        Commission::query()
            ->where('consumer_id', '>', 0)
            ->where('status', GoodAffStatus::APPROVED)
            ->orderBy('id', 'DESC')
            ->chunk(250, function ($commissions) use (&$rows) {
                foreach ($commissions as $commission) {

                    $consumer = Consumer::find($commission->consumer_id);
                    if (!$consumer) {
                        continue;
                    }

                    // Skip internal test data
                    if (Str::contains($consumer->email, '@goodday.group')) {
                        continue;
                    }

                    $email = $consumer->email;
                    $first_name = '';
                    $last_name = '';
                    $zipcode = '';

                    // Fetch latest application
                    $last_application = Application::query()
                        ->where('consumer_id', $consumer->id)
                        ->orderBy('id', 'DESC')
                        ->first();

                    if ($last_application) {
                        $first_name = $last_application->first_name;
                        $last_name = $last_application->last_name;

                        if (!$email) {
                            $email = $last_application->email;
                        }

                        $zipcode = $last_application->address_zipcode;
                    }

                    // Add row
                    $rows[] = [
                        $email,
                        $consumer->phone,
                        $first_name,
                        $last_name,
                        config('app.country_code'),
                        $zipcode
                    ];
                }
            });

        // Upload to Google Sheets (overwrite sheet)
        $client->put("{$sheetId}/values/commissions!A1?valueInputOption=RAW", [
            'json' => [
                'values' => $rows
            ]
        ]);

        $this->info('Approved Commissions exported to Google Sheets.');
    }

    private function generateAccessToken($credentials)
    {
        $now = time();

        $jwtPayload = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = JWT::encode($jwtPayload, $credentials['private_key'], 'RS256');

        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]
        ]);

        return json_decode($response->getBody(), true)['access_token'];
    }
}
