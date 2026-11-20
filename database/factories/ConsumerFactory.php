<?php

namespace Database\Factories;

use App\Models\Consumer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Consumer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $utm_source = fake()->randomElement([
            null,
            'google',
            'facebook',
        ]);
        $utm_campaign = null;
        $utm_medium = null;
        $utm_content = null;
        $gclid = null;

        if (!empty($utm_source)) {
            $utm_medium = match ($utm_source) {
                'google' => 'search',
                'facebook' => 'mobile',
            };

            $utm_campaign = fake()->randomElement([
                'adp-m1001',
                'adp-atk3-m1003	',
                'adp-m1003',
                'adp-atk2-m1003',
                'adp-m1001',
                'adp-m1004',
                'ad001_female',
                'ad002_female',
                'ad003_female',
                'ad001_male',
                'ad005_male',
                'ad004_male',
                'ad005_neutral',
                'mobile_ru_ad006_neutral',
                'ad1',
                'ad2',
                'ad3',
                'ad4',
                'ad5',
            ]);

            if (str_contains($utm_campaign, 'female')) {
                $utm_content = 'female';
            } elseif (str_contains($utm_campaign, 'male')) {
                $utm_content = 'male';
            }

            if ($utm_source === 'google') {
                $gclid = fake()->randomElement([
                    null,
                    fake()->regexify('[A-Za-z0-9-_]{92}'),
                    fake()->regexify('[A-Za-z0-9-_]{56}'),
                ]);
            }
        }

        return [
            'token' => fake()->text(12),
            'fingerprint' => fake()->text(12),
            'visitor_id' => 0,
            'first_name' => fake()->firstname(),
            'last_name' => fake()->lastName(),
            'birth' => null,
            'email' => config('landing_form.require_email') ? fake()->email() : null,
            'phone' => config('landing_form.require_phone') ? fake()->randomNumber(strlen(config('landing_form.phone_placeholder'))) : null,
            'amount' => fake()->numberBetween(150, 25000),
            'hoi_token' => fake()->text(6),
            'subscribed' => fake()->numberBetween(0, 1),
            'confirmed' => fake()->numberBetween(0, 1),
            'ip' => fake()->ipv4(),
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'utm_content' => $utm_content,
            'gclid' => $gclid,
            'lang' => fake()->randomElement(config('app.available_locales')),
        ];
    }
}
