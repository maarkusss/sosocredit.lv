<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications__gocardless_access', function (Blueprint $table) {
            $table->text('refresh_token_updated_at')->nullable()->after('access_token_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications__gocardless_access', function (Blueprint $table) {
            $table->dropColumn('refresh_token_updated_at');
        });
    }
};
