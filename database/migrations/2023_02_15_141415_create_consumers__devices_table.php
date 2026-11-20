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
        Schema::create('consumers__devices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consumer_id')->unsigned()->default(0);
            $table->string('provider', 31);
            $table->string('key', 255)->nullable();
            $table->string('value', 255)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->dateTime('exported_at')->nullable();

            $table->index('consumer_id', 'consumers_devices_consumer_ix');
            $table->index('provider', 'consumers_devices_provider_ix');
            $table->index('exported_at', 'consumers_devices_exported_at_ix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumers__devices');
    }
};
