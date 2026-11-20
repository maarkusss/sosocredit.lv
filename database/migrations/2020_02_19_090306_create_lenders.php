<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lenders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image');
            $table->string('affiliate_link');
            $table->integer('position')->nullable();
            $table->integer('frequency')->nullable();
            $table->integer('daily_epc')->nullable();
            $table->integer('daily_epc_before')->nullable();
            $table->float('guaranteed_epc')->nullable();
            $table->float('earnings')->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->float('epc')->default(0);
            $table->string('first_loan', 31);
            $table->string('max_amount', 31);
            $table->string('min_term', 63);
            $table->string('max_term', 63);
            $table->string('min_years', 63);
            $table->string('max_years', 63);
            $table->string('receiving_time', 63);
            $table->boolean('active')->unsigned()->default(0);
            $table->boolean('zero_percent')->unsigned()->default(0);
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
        Schema::dropIfExists('lenders');
    }
}
