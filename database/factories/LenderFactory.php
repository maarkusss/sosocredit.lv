<?php

namespace Database\Factories;

use App\Models\Lender;
use Illuminate\Database\Eloquent\Factories\Factory;

class LenderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lender::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Lender ' . fake()->title() . ' ' . fake()->numberBetween(1, 100),
            'image' => '',
            'affiliate_link' => '',
            'position' => fake()->numberBetween(1, 100),
            'frequency' => null,
            'daily_epc' => null,
            'daily_epc_before' => null,
            'guaranteed_epc' => 0,
            'earnings' => 0,
            'clicks' => 0,
            'epc' => 0,
            'first_loan' => 0,
            'max_amount' => 100000,
            'min_term' => fake()->numberBetween(30, 60),
            'max_term' => fake()->numberBetween(365, 730),
            'min_years' => fake()->numberBetween(1, 2),
            'max_years' => fake()->numberBetween(5, 10),
            'receiving_time' => '',
            'active' => 1,
            'zero_percent' => fake()->numberBetween(0, 1),
        ];
    }
}
