<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token')->nullable();
            $table->integer('visitor_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('birth', 63)->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->integer('amount')->unsigned()->nullable();
            $table->string('term')->nullable();
            $table->string('type')->nullable();
            $table->boolean('confirmed')->unsigned()->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->boolean('subscribed')->unsigned()->nullable();
            $table->string('ip', 63);
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content', 255)->nullable();
            $table->string('gclid', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumers');
    }
}
