<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dateTime('phone_verified_at')->nullable()->after('phone');

            $table->string('salary', 15)->nullable()->after('loantype');
            $table->string('age', 15)->nullable()->after('salary');
            $table->string('credit_history', 7)->nullable()->after('age');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('salary');
            $table->dropColumn('age');
            $table->dropColumn('credit_history');
        });
    }
};
