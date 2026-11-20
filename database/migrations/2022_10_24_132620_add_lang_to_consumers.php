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
        if (!Schema::hasColumn('consumers', 'lang')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->string('lang', 7)
                    ->after('subscribed')
                    ->nullable();

                $table->index('lang', 'consumers_lang_ix');
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
        if (Schema::hasColumn('consumers', 'lang') === true) {
            Schema::dropColumns('consumers', [
                'lang',
            ]);
        }
    }
};
