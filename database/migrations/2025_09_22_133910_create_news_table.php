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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id')->nullable()->index('category_id');
            $table->unsignedInteger('author_id')->nullable();
            $table->string('lang', 7)->nullable()->index('lang');
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('title', 512)->nullable();
            $table->string('tiny_description', 512)->nullable();
            $table->text('text')->nullable();
            $table->boolean('active')->unsigned()->default(0)->index('active');
            $table->string('meta_title', 512)->nullable();
            $table->string('meta_description', 512)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
};
