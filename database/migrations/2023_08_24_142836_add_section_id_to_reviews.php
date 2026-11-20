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
        if (!Schema::hasColumn('reviews', 'section_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->integer('section_id')
                    ->unsigned()
                    ->default(0)
                    ->after('text');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('reviews', 'section_id') === true) {
            Schema::dropColumns('reviews', [
                'section_id',
            ]);
        }
    }
};
