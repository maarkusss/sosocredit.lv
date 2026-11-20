<?php

use Database\Seeders\LocalhostLvRuSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Palaist LV un RU seederi kā daļu no migrācijas
        Artisan::call('db:seed', [
            '--class' => LocalhostLvRuSeeder::class,
            '--force' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
