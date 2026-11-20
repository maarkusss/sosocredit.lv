<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = ['hoi_logs', 'hoi__logs', 'hoi_log'];

        foreach ($tableNames as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->index(['to', 'subject', 'created_at'], 'idx_hoi_to_subject_created');
                });
                break;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = ['hoi_logs', 'hoi__logs', 'hoi_log'];

        foreach ($tableNames as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex('idx_hoi_to_subject_created');
                });
                break;
            }
        }
    }
};