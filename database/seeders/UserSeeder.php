<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public const TEST_LOGIN = 'goodadmin';
    public const TEST_PASSWORD = 'GoodPassword!987';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal()) {
            $adminuser = User::factory()->create([
                'email' => 'admin@localhost',
                'first_name' => 'Goodday',
                'last_name' => 'Admin',
                'username' => static::TEST_LOGIN,
                'password' => Hash::make(static::TEST_PASSWORD),
            ]);
        }
    }
}
