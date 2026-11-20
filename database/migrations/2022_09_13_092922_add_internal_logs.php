<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('provider', 191);
            $table->string('env', 31);
            $table->string('method', 191);
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('sender', 500)->nullable();
            $table->string('message', 500)->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
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
        Schema::dropIfExists('internal_logs');
    }
}
