<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 7);
            $table->integer('order');
            $table->string('name');
            $table->string('route_name');
            $table->string('title');
            $table->string('description', 255)->nullable();
            $table->string('keywords')->nullable();
            $table->text('text')->nullable();
            $table->boolean('active')->default(0);
            $table->string('h1', 255)->nullable();
            $table->text('h1_description')->nullable();
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
        Schema::dropIfExists('loan_types');
    }
}
