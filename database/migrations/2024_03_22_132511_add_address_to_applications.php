<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('address_street', 63)->nullable()->after('phone_verified_at');
            $table->string('address_city', 31)->nullable()->after('address_street');
            $table->string('address_zipcode', 15)->nullable()->after('address_city');

            $table->string('martial_status', 31)->nullable()->after('employment_status');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('address_street');
            $table->dropColumn('address_city');
            $table->dropColumn('address_zipcode');
            $table->dropColumn('martial_status');
        });
    }
};
