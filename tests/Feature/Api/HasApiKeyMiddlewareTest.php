<?php

namespace Tests\Feature\Api;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HasApiKeyMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_not_available_without_apikey()
    {
        $response = $this->getJson('/api/sessions');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_api_forbidden_with_wrong_apikey()
    {
        $wrong_apikey = 'wrong_apikey_' . sha1(date('YmdHis'));
        $response = $this->getJson('/api/sessions', [
            'apikey' => $wrong_apikey,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_api_available_with_correct_apikey()
    {
        $this->seed(SettingsSeeder::class);
        $correct_apikey = Setting::retrieve('api_key');

        $response = $this->getJson('/api/sessions', [
            'apikey' => $correct_apikey,
        ]);

        $response->assertOk();
    }
}
