<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('employment_status', 45)->nullable()->after('loantype');
            $table->string('disbursement_iban', 60)->nullable()->after('employment_status');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('employment_status');
            $table->dropColumn('disbursement_iban');
        });
    }
};
