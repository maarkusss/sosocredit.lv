<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->dateTime('offer_email_sent_at')->nullable()->after('email');
            $table->dateTime('offer_sms_sent_at')->nullable()->after('phone');
        });

        if (Schema::hasColumn('applications', 'applicant_id') === true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->renameColumn('applicant_id', 'consumer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('consumers', 'offer_sms_sent_at') === true) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->dropColumn('offer_sms_sent_at');
                $table->dropColumn('offer_email_sent_at');
            });
        }

        if (Schema::hasColumn('applications', 'consumer_id') === true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->renameColumn('consumer_id', 'applicant_id');
            });
        }
    }
};
