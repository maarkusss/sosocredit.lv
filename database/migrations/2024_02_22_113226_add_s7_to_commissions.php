<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('commissions', 'consumer_id')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->string('s4', 255)->nullable()->after('gclid');
                $table->string('s7', 255)->nullable()->after('s4');
                $table->bigInteger('consumer_id')->unsigned()->default(0)->after('s7');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('commissions', 'consumer_id')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('s4');
                $table->dropColumn('s7');
                $table->dropColumn('consumer_id');
            });
        }
    }
};
