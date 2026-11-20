<?php

namespace Database\Factories;

use App\Models\Consumer\ConsumerSMSVerificationCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumerSMSVerificationCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConsumerSMSVerificationCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'phone_number' => fake()->phoneNumber(),
            'code' => fake()->randomNumber(6),
            'expires_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+15 minutes')),
        ];
    }
}
