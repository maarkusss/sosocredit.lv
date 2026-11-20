<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLendersVisitsSortingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lenders__visits_sorting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('visit_id')->unsigned();
            $table->integer('lender_id')->unsigned();
            $table->integer('position');
            $table->timestamps();

            $table->index(['visit_id', 'lender_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lenders__visits_sorting');
    }
}
