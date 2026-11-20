<?php

namespace Tests\Feature\Api;

use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Models\Consumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_with_no_params_returns_bad_request()
    {
        $response = $this->getJson('/api/user', $this->apiHeaders());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_user_endpoint_with_phone_creates_consumer()
    {
        $query = http_build_query([
            'phone' => '71234567890',
        ]);

        $response = $this->getJson('/api/user?' . $query, $this->apiHeaders());

        $response->assertCreated();

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
                ->etc()
        );
    }

    public function test_user_verification_phone_endpoint_with_no_params_returns_bad_request()
    {
        $response = $this->getJson('/api/user/verification/phone', $this->apiHeaders());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_user_verification_phone_endpoint_with_wrong_verification_code_returns_bad_request()
    {
        $query = http_build_query([
            'phone' => '71234567890',
            'verification_code' => 'abcdef'
        ]);

        $response = $this->getJson('/api/user/verification/phone?' . $query, $this->apiHeaders());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response->assertJson(
            fn (AssertableJson $json) =>
        $json->has('message')
            ->has('data')
        );
    }

    public function test_user_verification_phone_endpoint_with_non_existing_phone_number_returns_not_found()
    {
        $phone_number = '71234567890';
        $verification_code = '123456';

        ConsumerSMSVerificationCode::factory()
            ->create([
                'phone_number' => $phone_number,
                'code' => $verification_code,
            ]);

        $query = http_build_query([
            'phone' => $phone_number,
            'verification_code' => $verification_code
        ]);

        $response = $this->getJson('/api/user/verification/phone?' . $query, $this->apiHeaders());

        $response->assertNotFound();
    }

    public function test_user_verification_phone_endpoint_with_right_verification_code_returns_success()
    {
        $phone_number = '71234567890';
        $verification_code = '123456';

        Consumer::factory()
            ->create([
                'phone' => $phone_number,
            ]);

        ConsumerSMSVerificationCode::factory()
            ->create([
                'phone_number' => $phone_number,
                'code' => $verification_code,
            ]);

        $query = http_build_query([
            'phone' => $phone_number,
            'verification_code' => $verification_code
        ]);

        $response = $this->getJson('/api/user/verification/phone?' . $query, $this->apiHeaders());

        $response->assertOk();
    }
}
