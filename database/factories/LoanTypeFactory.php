<?php

namespace Database\Factories;

use App\Models\LoanType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LoanTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = fake()->title();
        $route_name = Str::slug($name);
        $title = $name;

        return [
            'lang' => fake()->locale(),
            'order' => 1,
            'name' => $name,
            'route_name' => $route_name,
            'title' => $title,
            'description' => null,
            'keywords' => null,
            'text' => null,
            'active' => 1,
        ];
    }
}
