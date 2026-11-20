<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const STATUS_MULTIPLIERS = [
        'Approved' => 3,
        'Invalid' => 0.2,
        'Registered' => 1,
        'Waiting' => 1,
        'Reviewing' => 1,
        'Pending' => 1,
        'Cannot' => 0.2,
        'Rejected' => 0.2,
        'Error' => 0.2,
        'Expired' => 0.4,
        'Cancelled' => 0.2,
        'Lender' => 1
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('multiplier', 3, 1); // Changed to decimal for proper decimal values
            $table->timestamps();
        });

        // Populate the table with status multipliers
        foreach (self::STATUS_MULTIPLIERS as $name => $multiplier) {
            DB::table('application_status')->insert([
                'name' => $name,
                'multiplier' => $multiplier,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_status');
    }
};
