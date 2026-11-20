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
        Schema::create('hoi__logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 16);
            $table->string('to', 127);
            $table->string('subject', 127);
            $table->string('htmllink', 500);
            $table->smallInteger('attempts')->unsigned()->default(0);
            $table->timestamp('created_at')->nullable();

            $table->index(['to', 'subject', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hoi__logs');
    }
};
