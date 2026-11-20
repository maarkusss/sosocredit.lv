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
        if (!Schema::hasColumn('google_reviews', 'lang')) {
            Schema::table('google_reviews', function (Blueprint $table) {
                $table->string('lang', 7)
                    ->after('text')
                    ->nullable();

                $table->index('lang', 'google_reviews_lang_ix');
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
        if (Schema::hasColumn('google_reviews', 'lang') === true) {
            Schema::dropColumns('google_reviews', [
                'lang',
            ]);
        }
    }
};
