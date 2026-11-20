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
        if (Schema::hasColumn('consumers', 'second_offer_email_sent_at') !== true) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->dateTime('second_offer_email_sent_at')->nullable()->after('offer_email_sent_at');
                $table->dateTime('second_offer_sms_sent_at')->nullable()->after('offer_sms_sent_at');

                $table->index('offer_email_sent_at', 'consumers_email_at_ix');
                $table->index('offer_sms_sent_at', 'consumers_sms_at_ix');

                $table->index('second_offer_email_sent_at', 'consumers_second_email_at_ix');
                $table->index('second_offer_sms_sent_at', 'consumers_second_sms_at_ix');
            });
        }

        if (Schema::hasColumn('applications', 'first_offer_hash') !== true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('first_offer_hash', 15)->nullable()->after('gclid');

                $table->index('first_offer_hash', 'applications_first_offer_ix');
            });
        }

        if (Schema::hasColumn('applications', 'second_offer_hash') !== true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('second_offer_hash', 15)->nullable()->after('first_offer_hash');

                $table->index('second_offer_hash', 'applications_second_offer_ix');
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
        if (Schema::hasColumn('consumers', 'second_offer_email_sent_at') === true) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->dropColumn('second_offer_email_sent_at');
                $table->dropColumn('second_offer_sms_sent_at');
            });
        }

        if (Schema::hasColumn('applications', 'first_offer_hash') === true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('first_offer_hash');
            });
        }

        if (Schema::hasColumn('applications', 'second_offer_hash') === true) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('second_offer_hash');
            });
        }
    }
};
