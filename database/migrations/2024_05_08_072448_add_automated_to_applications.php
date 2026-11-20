<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('applications', 'automated')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->boolean('automated')->unsigned()->default(0)->after('subscribed');
            });
        }
    }

    public function down(): void
    {

    }
};
