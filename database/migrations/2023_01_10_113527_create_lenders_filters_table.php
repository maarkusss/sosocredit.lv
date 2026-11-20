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
        Schema::create('lenders__filters', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->unsigned()->default(0);

            $table->string('utm_source', 255)->nullable();
            $table->string('utm_medium', 255)->nullable();
            $table->string('utm_campaign', 255)->nullable();
            $table->string('utm_content', 255)->nullable();
            $table->string('token', 191)->nullable();
            $table->longText('rules')->nullable();
            $table->bigInteger('hits')->unsigned()->default(0);
            $table->string('source', 31)->nullable();
            $table->string('short_url', 191)->nullable();

            $table->timestamps();

            $table->index('active', 'lenders_filters_active_ix');
            $table->index(['utm_source', 'token'], 'lenders_filters_usource_token_ix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lenders__filters');
    }
};
