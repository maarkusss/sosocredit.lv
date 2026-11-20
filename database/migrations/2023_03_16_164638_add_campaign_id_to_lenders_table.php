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
        if (!Schema::hasColumn('lenders', 'campaign_id')) {
            Schema::table('lenders', function (Blueprint $table) {
                $table->after('zero_percent', function ($table) {
                    $table->bigInteger('campaign_id')
                        ->unsigned()
                        ->default(0);
                });
            });
        }

        if (!Schema::hasColumn('lenders__filters', 'token')) {
            Schema::table('lenders__filters', function (Blueprint $table) {
                $table->string('token', 191)->nullable()->after('utm_content');
            });
        }

        if (!Schema::hasColumn('lenders__filters', 'hits')) {
            Schema::table('lenders__filters', function (Blueprint $table) {
                $table->bigInteger('hits')->unsigned()->default(0)->after('rules');
            });
        }

        if (!Schema::hasColumn('lenders__filters', 'source')) {
            Schema::table('lenders__filters', function (Blueprint $table) {
                $table->string('source', 31)->nullable()->after('hits');
            });
        }

        if (!Schema::hasColumn('lenders__filters', 'short_url')) {
            Schema::table('lenders__filters', function (Blueprint $table) {
                $table->string('short_url', 191)->nullable()->after('source');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropColumn([
                'campaign_id',
            ]);
        });
    }
};
