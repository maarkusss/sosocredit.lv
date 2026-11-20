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
        Schema::table('sections', function (Blueprint $table) {
            $table->after('text', function ($table) {
                $table->string('template')
                    ->nullable();

                $table->boolean('in_header_menu')
                    ->unsigned()
                    ->default(0);

                $table->boolean('in_footer_menu')
                    ->unsigned()
                    ->default(0);
            });
        });

        Schema::table('sections__history', function (Blueprint $table) {
            $table->after('text', function ($table) {
                $table->string('template')
                    ->nullable();

                $table->boolean('in_header_menu')
                    ->unsigned()
                    ->default(0);

                $table->boolean('in_footer_menu')
                    ->unsigned()
                    ->default(0);
            });
        });

        if (!Schema::hasColumn('sections', 'h1')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->string('h1', 255)->nullable()->after('text');
                $table->text('h1_description')->nullable()->after('h1');
            });
        }

        if (!Schema::hasColumn('sections__history', 'h1')) {
            Schema::table('sections__history', function (Blueprint $table) {
                $table->string('h1', 255)->nullable()->after('text');
                $table->text('h1_description')->nullable()->after('h1');
            });
        }

        //        if (Schema::hasColumn('sections', 'contact_us')) {
        //            Schema::dropColumns('sections', ['contact_us']);
        //        }
        //
        //        if (Schema::hasColumn('sections__history', 'contact_us')) {
        //            Schema::dropColumns('sections__history', ['contact_us']);
        //        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn([
                'template',
                'in_header_menu',
                'in_footer_menu',
            ]);
        });

        Schema::table('sections__history', function (Blueprint $table) {
            $table->dropColumn([
                'template',
                'in_header_menu',
                'in_footer_menu',
            ]);
        });

        //        if (!Schema::hasColumn('sections', 'contact_us')) {
        //            Schema::table('sections', function (Blueprint $table) {
        //                $table->tinyInteger('contact_us')
        //                    ->unsigned()
        //                    ->default(0)
        //                    ->after('main_page');
        //            });
        //        }
        //
        //        if (!Schema::hasColumn('sections__history', 'contact_us')) {
        //            Schema::table('sections__history', function (Blueprint $table) {
        //                $table->tinyInteger('contact_us')
        //                    ->unsigned()
        //                    ->default(0)
        //                    ->after('main_page');
        //            });
        //        }
    }
};
