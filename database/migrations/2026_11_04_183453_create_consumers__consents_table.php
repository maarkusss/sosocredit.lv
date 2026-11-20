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
        Schema::create('consumers__consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('consumers')->restrictOnDelete();
            $table->boolean('terms')->default(false);
            $table->boolean('marketing')->default(false);
            $table->text('user_agent')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->dateTime('consented_at')->useCurrent();
            $table->text('terms_snapshot')->nullable();
            $table->text('marketing_snapshot')->nullable();
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
        Schema::dropIfExists('consumers__consents');
    }
};
