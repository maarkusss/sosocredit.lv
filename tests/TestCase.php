<?php

namespace Tests;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function apiHeaders(): array
    {
        return [
            'apikey' => $this->apiKey(),
        ];
    }

    protected function apiKey(): string
    {
        $this->seed(SettingsSeeder::class);

        return Setting::retrieve('api_key');
    }
}
