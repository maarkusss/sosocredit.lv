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
        Schema::create('consumers__email_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consumer_id')->unsigned();
            $table->bigInteger('application_id')->unsigned()->default(0)->nullable();
            $table->string('email', 127)->nullable();
            $table->dateTime('email_verification_link_sent_at')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->timestamps();

            $table->index('consumer_id', 'consumers__verifications_cone_id_ix');
            $table->index('email_verification_link_sent_at', 'consumers__verifications_email_sent_ix');
            $table->index('email_verified_at', 'consumers__verifications_email_verified_ix');
        });

        Schema::create('consumers__phone_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consumer_id')->unsigned();
            $table->bigInteger('application_id')->unsigned()->default(0)->nullable();
            $table->string('phone', 31);
            $table->string('sms_verification_hash', 17);
            $table->dateTime('sms_verification_link_sent_at')->nullable();
            $table->dateTime('phone_verified_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->index('consumer_id', 'consumers__verifications_conp_id_ix');
            $table->index('sms_verification_hash', 'consumers__verifications_sms_hash_ix');
            $table->index('sms_verification_link_sent_at', 'consumers__verifications_sms_sent_ix');
            $table->index('phone_verified_at', 'consumers__verifications_phone_verified_ix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumers__email_verification_requests');
        Schema::dropIfExists('consumers__phone_verification_requests');
    }
};
