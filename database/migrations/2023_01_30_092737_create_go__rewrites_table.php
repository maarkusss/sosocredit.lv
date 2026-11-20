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
        Schema::create('go__rewrites', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->text('params_from')->nullable();
            $table->string('domain_from', 63)->nullable();
            $table->string('domain_to', 63)->nullable();
            $table->string('path_ending_from', 63)->nullable();
            $table->string('path_ending_to', 63)->nullable();
            $table->text('params_to')->nullable();
            $table->boolean('active')->unsigned()->default(0);

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
        Schema::dropIfExists('go__rewrites');
    }
};
