<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('visitor_id')->unsigned()->nullable();
            $table->integer('uid');
            $table->string('transaction_id');
            $table->float('commission');
            $table->integer('lender_id')->unsigned();
            $table->string('referrer', 500)->nullable();
            $table->string('ip', 63)->nullable();
            $table->string('status');
            $table->string('utm_campaign')->nullable();
            $table->string('gclid', 255)->nullable();
            $table->timestamp('commission_date');
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
        Schema::dropIfExists('commissions');
    }
}
