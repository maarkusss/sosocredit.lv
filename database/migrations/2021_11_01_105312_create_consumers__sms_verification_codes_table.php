<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumersSmsVerificationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumers__sms_verification_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('phone_number');
            $table->integer('code');
            $table->datetime('expires_at');
            $table->timestamps();
            // Indexes
            $table->index('phone_number');
            $table->index('code');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumers__sms_verification_codes');
    }
}
