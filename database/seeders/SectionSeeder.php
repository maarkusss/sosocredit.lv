<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            $langs = Setting::query()
                ->where('name', 'lang')
                ->pluck('value')
                ->toArray();

            foreach ($langs as $lang) {
                Section::factory()->create([
                    'active' => 1,
                    'main_page' => 1,
                    'name' => 'Home',
                    'route_name' => 'home',
                    'lang' => $lang,
                ]);

                Section::factory()->create([
                    'active' => 1,
                    'contact_us' => 1,
                    'name' => 'Contacts',
                    'route_name' => 'contacts',
                    'lang' => $lang,
                ]);

                Section::factory()->create([
                    'active' => 1,
                    'name' => 'Cookies',
                    'route_name' => 'cookies',
                    'lang' => $lang,
                ]);
            }
        }
    }
}
