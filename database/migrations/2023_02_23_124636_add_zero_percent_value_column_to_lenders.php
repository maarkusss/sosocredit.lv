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
        if (!Schema::hasColumn('lenders', 'zero_percent_value')) {
            Schema::table('lenders', function (Blueprint $table) {
                $table->string('zero_percent_value', 15)
                    ->after('zero_percent')
                    ->nullable();
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
        if (Schema::hasColumn('lenders', 'zero_percent_value') === true) {
            Schema::dropColumns('lenders', [
                'zero_percent_value',
            ]);
        }
    }
};
