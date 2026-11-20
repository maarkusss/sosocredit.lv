<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Consumer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class ExportUnqualifiedConsumersToGoogleSheets extends Command
{
    protected $signature = 'unqialified-consumers:export-google';
    protected $description = 'Export Unqualified Consumers to Google Sheets';

    public function handle()
    {
        $sheetId = env('GOOGLE_UNQUALIFIED_CONSUMERS_SHEET_ID');

        // 1. Google autentifikācija
        $path = storage_path('app/google/credentials.json');

        if (!file_exists($path)) {
            $this->error("Credentials file NOT FOUND at: " . $path);
            return 1;
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

        $rejectedStatuses = ["Refused", "Rejected", "Cancelled", "Error", "Cannot", "Invalid", "Expired"];
        $rejectedLookup = array_flip($rejectedStatuses);
        $countryCode = config('app.country_code');

        // 2. Notīrām esošos datus Google Sheet lapā
        $client->post("{$sheetId}/values/consumers!A:Z:clear");

        $rows = [
            ['Email', 'Phone', 'First Name', 'Last Name', 'Country', 'Zip']
        ];

        $this->info('Sāk apstrādāt datus...');
        $processedCount = 0;

        // 3. Atlasām Consumer ierakstus pa 500 vienlaicīgi
        Consumer::query()
            ->where('updated_at', '>=', now()->subDays(540))
            ->chunkById(500, function ($consumers) use (&$rows, &$processedCount, $rejectedLookup, $countryCode) {
                
                $consumerIds = $consumers->pluck('id');

                // Iegūstam pēdējo pieteikumu visiem 500 lietotājiem ar 1 vaicājumu
                $latestApplications = Application::query()
                    ->whereIn('consumer_id', $consumerIds)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->groupBy('consumer_id')
                    ->map(fn($apps) => $apps->first());

                foreach ($consumers as $consumer) {
                    $processedCount++;

                    if ($consumer->email && Str::contains($consumer->email, '@goodday.group')) {
                        continue;
                    }

                    $last_application = $latestApplications->get($consumer->id);

                    if (!$last_application) {
                        continue;
                    }

                    $exports_summary = $last_application->exports_summary ?? null;

                    if (empty($exports_summary)) {
                        continue;
                    }

                    if (is_string($exports_summary)) {
                        $exports_summary = json_decode($exports_summary, true) ?: [];
                    }

                    if (empty($exports_summary) || !is_array($exports_summary)) {
                        continue;
                    }

                    // Pārbaudām: vai VISI aizdevēji iekš exports_summary ir ar atteikuma statusu
                    $all_providers_rejected = true;
                    foreach ($exports_summary as $decision) {
                        if (!isset($rejectedLookup[$decision])) {
                            $all_providers_rejected = false;
                            break;
                        }
                    }

                    if (!$all_providers_rejected) {
                        continue;
                    }

                    $email = $consumer->email ?: $last_application->email;
                    $first_name = $last_application->first_name;
                    $last_name  = $last_application->last_name;
                    $zipcode    = $last_application->address_zipcode;

                    $rows[] = [
                        $email,
                        $consumer->phone,
                        $first_name,
                        $last_name,
                        $countryCode,
                        $zipcode
                    ];
                }

                $this->output->write("\rApstrādāti: {$processedCount} | Atlasītas rindas: " . (count($rows) - 1));
            });

        $this->newLine();
        $totalRows = count($rows) - 1;
        $this->info("Kopā atlasītas {$totalRows} rindas. Augšupielādē Google Sheets...");

        if ($totalRows === 0) {
            $this->warn('Netika atrasts neviens atbilstošs ieraksts.');
            return 0;
        }

        // 4. Augšupielādē pa daļām ar PUT (update)
        $chunks = array_chunk($rows, 2000);
        $startRow = 1;

        foreach ($chunks as $index => $chunk) {
            $endRow = $startRow + count($chunk) - 1;
            $range = "consumers!A{$startRow}:F{$endRow}";

            $client->put("{$sheetId}/values/{$range}?valueInputOption=RAW", [
                'json' => [
                    'values' => $chunk
                ]
            ]);

            $startRow = $endRow + 1;
            $uploaded = min(($index + 1) * 2000, $totalRows + 1);
            $this->output->write("\rAugšupielādētas: {$uploaded} / " . ($totalRows + 1) . " rindas");
        }

        $this->newLine();
        $this->info('Consumers veiksmīgi eksportēti uz Google Sheets!');
        return 0;
    }

    private function generateAccessToken($credentials)
    {
        $now = time();

        $jwtPayload = [
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $jwt = JWT::encode($jwtPayload, $credentials['private_key'], 'RS256');
        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt
            ]
        ]);

        return json_decode($response->getBody(), true)['access_token'];
    }
}
