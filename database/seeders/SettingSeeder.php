<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            $settings = [
                'api_key',
                'individual_days_epc',
                'prefix',
                'sitemap',
                'hoi_fm_sender_name',
                'api_default_zero_percent_value',
            ];

            Setting::factory()->create([
                'name' => 'name',
                'value' => config('app.name'),
            ]);

            $i = 1;
            foreach ($settings as $setting) {
                Setting::factory()->create([
                    'name' => $setting,
                    'value' => ++$i,
                ]);
            }

            $langs = config('app.available_locales');

            foreach ($langs as $lang) {
                Setting::factory()->create([
                    'name' => 'lang',
                    'value' => $lang,
                ]);
            }

            $country_code_value = '7'; // Russia / Kazakhstan
            if (in_array('lv', $langs)) {
                $country_code_value = '371'; // Latvia
            }
            if (in_array('uk', $langs)) {
                $country_code_value = '380'; // Ukraine
            }
            if (in_array('vi', $langs)) {
                $country_code_value = '84'; // Vietnam
            }
            if (in_array('pl', $langs)) {
                $country_code_value = '48'; // Poland
            }

            Setting::factory()->create([
                'name' => 'country_code',
                'value' => $country_code_value,
            ]);
        }
    }
}
