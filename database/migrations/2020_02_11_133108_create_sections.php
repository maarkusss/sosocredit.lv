<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->unsigned()->default(1);
            $table->integer('order')->unsigned();
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

            $table->index('route_name', 'sections_route_ix');
            $table->index('lang', 'sections_lang_ix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
