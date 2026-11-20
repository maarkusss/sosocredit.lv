<?php

namespace Database\Seeders;

use App\Models\Lender;
use Illuminate\Database\Seeder;

class LenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            Lender::factory()->create([
                'name' => 'Lender 1',
                'active' => 1,
            ]);

            Lender::factory()->create([
                'name' => 'Lender 2',
                'active' => 1,
            ]);

            Lender::factory()->create([
                'name' => 'Lender 3',
                'active' => 1,
            ]);
        }
    }
}
