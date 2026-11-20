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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('token', 63);
            $table->string('fingerprint', 20)->nullable();
            $table->string('lang', 7)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 20);

            $table->string('amount', 15)->nullable();
            $table->smallInteger('term_months')->unsigned()->default(0);

            $table->string('first_name', 63)->nullable();
            $table->string('last_name', 127)->nullable();
            $table->string('personal_code', 31)->nullable();
            $table->boolean('is_political_person')->unsigned()->default(0);
            $table->boolean('has_bank_attachment')->unsigned()->default(0);
            $table->string('coupon_code', 63)->nullable();

            $table->boolean('agree_to_terms')->unsigned()->nullable();
            $table->boolean('subscribed')->unsigned()->nullable();
            $table->smallInteger('type_id')->unsigned()->default(0);
            $table->string('pdf', 255)->nullable();

            $table->string('referrer', 255)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('ip', 63)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content', 255)->nullable();

            $table->tinyInteger('completion_percentage')->unsigned()->default(0);

            $table->text('phones')->nullable();
            $table->text('emails')->nullable();

            $table->string('pdf_downloaded_from_ip', 63)->nullable();
            $table->dateTime('pdf_downloaded_at')->nullable();
            $table->timestamps();

            $table->unique('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicants');
    }
};
