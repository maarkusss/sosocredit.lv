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
        if (!Schema::hasColumn('applications', 'cadastral_number')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('cadastral_number')->nullable()->after('company_contact_person');
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
        if (Schema::hasColumn('applications', 'cadastral_number') === true) {
            Schema::dropColumns('applications', [
                'cadastral_number',
            ]);
        }
    }
};
