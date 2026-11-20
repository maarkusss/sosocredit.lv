<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->boolean('subscribed_email')->default(0)->after('subscribed');
            $table->boolean('subscribed_sms')->default(0)->after('subscribed_email');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('subscribed_email')->default(0)->after('subscribed');
            $table->boolean('subscribed_sms')->default(0)->after('subscribed_email');
        });

        // Sync existing subscribed values
        \DB::table('consumers')->where('subscribed', 1)->update([
            'subscribed_email' => 1,
            'subscribed_sms' => 1,
        ]);

        \DB::table('applications')->where('subscribed', 1)->update([
            'subscribed_email' => 1,
            'subscribed_sms' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->dropColumn(['subscribed_email', 'subscribed_sms']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['subscribed_email', 'subscribed_sms']);
        });
    }
};
