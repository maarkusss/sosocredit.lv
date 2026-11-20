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
        if (!Schema::hasColumn('sections', 'json')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->text('json')->nullable()->after('in_footer_menu');
            });
        }

        if (!Schema::hasColumn('sections__history', 'json')) {
            Schema::table('sections__history', function (Blueprint $table) {
                $table->text('json')->nullable()->after('in_footer_menu');
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
        if (Schema::hasColumn('sections', 'json') === true) {
            Schema::dropColumns('sections', [
                'json',
            ]);
        }

        if (Schema::hasColumn('sections__history', 'json') === true) {
            Schema::dropColumns('sections__history', [
                'json',
            ]);
        }
    }
};
