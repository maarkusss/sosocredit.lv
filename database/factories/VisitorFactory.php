<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Visitor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'clicks' => fake()->numberBetween(0, 7),
            'user_agent' => fake()->userAgent(),
            'ip' => fake()->ipv4(),
            'country' => fake()->countryCode(),
            'utm_source' => strtolower(fake()->word()),
            'utm_campaign' => strtolower(fake()->word()),
            'gclid' => null,
            'from' => null,
            'to' => null,
        ];
    }
}
