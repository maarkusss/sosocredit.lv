<?php

namespace Tests\Feature\Api;

use Database\Seeders\LendersSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LendersEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_getalllenders_endpoint_exists()
    {
        $this->seed(LendersSeeder::class);
        $this->seed(LendersSeeder::class);

        $response = $this->getJson('/api/getalllenders', $this->apiHeaders());

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has(6)
        );
    }

    public function test_endpoint_returns_expected_format()
    {
        $this->seed(LendersSeeder::class);

        $response = $this->getJson('/api/lenders', $this->apiHeaders());

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has(3)
        );
    }
}
