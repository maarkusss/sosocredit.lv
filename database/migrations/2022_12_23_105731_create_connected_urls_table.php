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
        Schema::create('connected_urls', function (Blueprint $table) {
            $table->id();
            $table->string('url', 255);
            $table->string('lang', 7);
            $table->longText('related_urls')->nullable();

            $table->index('url', 'connected_urls_url_ix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connected_urls');
    }
};
