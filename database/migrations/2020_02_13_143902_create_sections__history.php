<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections__history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->integer('section_id');
            $table->integer('parent_section_id')->unsigned()->nullable();
            $table->boolean('main_page')->unsigned()->default(0);
            $table->boolean('contact_us')->unsigned()->default(0);
            $table->string('lang', 7);
            $table->string('title');
            $table->string('description', 255)->nullable();
            $table->string('keywords')->nullable();
            $table->string('route_name');
            $table->string('name');
            $table->text('redirect_link')->nullable();
            $table->text('source')->nullable();
            $table->text('text')->nullable();
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
        Schema::dropIfExists('sections__history');
    }
}
