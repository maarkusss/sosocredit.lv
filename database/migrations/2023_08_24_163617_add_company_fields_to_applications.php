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
        if (!Schema::hasColumn('applications', 'company_name')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('company_name', 100)->nullable()->after('loantype');
                $table->string('company_reg_nr', 31)->nullable()->after('company_name');
                $table->string('company_contact_person', 63)->nullable()->after('company_reg_nr');
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
        if (Schema::hasColumn('applications', 'company_name') === true) {
            Schema::dropColumns('applications', [
                'company_name',
                'company_reg_nr',
                'company_contact_person',
            ]);
        }
    }
};
