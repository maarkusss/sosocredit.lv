<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lenders', function (Blueprint $table) {
            $table->boolean('is_provider')
                ->after('active')
                ->default(false);
            $table->string('rejected_statuses')
                ->after('is_provider')
                ->nullable();
            $table->string('abbreviation')
                ->after('name')
                ->nullable();
            $table->string('slug')
                ->after('abbreviation')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropColumn('is_provider');
            $table->dropColumn('rejected_statuses');
            $table->dropColumn('abbreviation');
            $table->dropColumn('slug');
        });
    }
};
