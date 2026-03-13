<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Уровень гонки и переводы для отображения (level + level_translations).
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('level', 64)->nullable()->after('status');
        });

        Schema::create('level_translations', function (Blueprint $table) {
            $table->id();
            $table->string('level_key', 64);
            $table->string('locale', 8);
            $table->string('label', 255);
            $table->timestamps();
            $table->unique(['level_key', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        Schema::dropIfExists('level_translations');
    }
};
