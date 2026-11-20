<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            $available_locales = available_locales();
            if (empty($available_locales)) {
                $available_locales = config('app.available_locales');
            }

            if (!empty($available_locales)) {
                foreach ($available_locales as $lang) {
                    $name = 'Кредиты';
                    if ($lang === 'en') {
                        $name = 'Loans';
                    } elseif ($lang === 'lv') {
                        $name = 'Kredīti';
                    }

                    LoanType::factory()->create([
                        'lang' => $lang,
                        'name' => $name,
                        'route_name' => Str::slug($name),
                        'title' => $name,
                    ]);
                }
            }
        }
    }
}
