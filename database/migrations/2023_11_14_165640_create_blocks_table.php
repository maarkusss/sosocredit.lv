<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('lang', 7);
            $table->string('key', 63);
            $table->text('value')->nullable();
            $table->string('tag', 31)->nullable();
            $table->timestamps();

            $table->unique(['lang', 'key']);
            $table->index('tag');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
