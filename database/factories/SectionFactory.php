<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->words(rand(1, 3));
        $title = implode(' ', $title);
        $route_name = Str::slug($title);
        $lang = default_locale();

        return [
            'active' => fake()->numberBetween(0, 1),
            'order' => 0,
            'parent_section_id' => null,
            'main_page' => 0,
            'contact_us' => 0,
            'lang' => $lang,
            'title' => $title,
            'description' => fake()->sentence(),
            'name' => $title,
            'route_name' => $route_name,
            'source' => fake()->sentence(15),
            'text' => fake()->text(rand(400, 5000)),
        ];
    }
}
