<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_returns_expected_format()
    {
        $response = $this->getJson('/api/sessions', $this->apiHeaders());

        $response->assertJson([]);
    }
}
