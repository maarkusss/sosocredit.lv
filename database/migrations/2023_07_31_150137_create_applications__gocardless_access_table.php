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
        Schema::create('applications__gocardless_access', function (Blueprint $table) {
            $table->id();
            $table->string('access_token', 500)->nullable();
            $table->string('refresh_token', 500)->nullable();
            $table->dateTime('access_token_updated_at')->nullable();
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
        Schema::dropIfExists('applications__gocardless_access');
    }
};
