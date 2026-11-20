<?php

namespace Tests\Feature\Api;

use Database\Seeders\VisitorsSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VisitorsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_returns_expected_format()
    {
        $response = $this->getJson('/api/visitors', $this->apiHeaders());

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
        );
    }

    public function test_endpoint_returns_realtime_data()
    {
        $this->seed(VisitorsSeeder::class);
        $this->seed(VisitorsSeeder::class);
        $this->seed(VisitorsSeeder::class);

        $response = $this->getJson('/api/visitors', $this->apiHeaders());

        $response->assertJson([
            'message' => 3,
        ]);
    }
}
