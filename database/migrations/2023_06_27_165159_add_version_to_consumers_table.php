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
        if (!Schema::hasColumn('consumers', 'version')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->tinyInteger('version')
                    ->after('fbp')
                    ->unsigned()
                    ->default(1);

                $table->index('version');
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
        if (Schema::hasColumn('consumers', 'version') === true) {
            Schema::dropColumns('consumers', [
                'version',
            ]);
        }
    }
};
