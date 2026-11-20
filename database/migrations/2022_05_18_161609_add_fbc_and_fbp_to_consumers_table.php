<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFbcAndFbpToConsumersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->string('fbc', 255)->nullable()->after('gclid');
            $table->string('fbp', 255)->nullable()->after('fbc');

            // Indexes
            $table->index('fbc');
            $table->index('fbp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->dropColumn('fbc');
            $table->dropColumn('fbp');
        });
    }
}
