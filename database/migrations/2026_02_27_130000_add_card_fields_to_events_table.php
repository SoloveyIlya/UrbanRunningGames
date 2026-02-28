<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Поля для карточки события на главной.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('distance', 64)->nullable()->after('cover_media_id');
            $table->string('locations_count', 64)->nullable()->after('distance');
            $table->string('time_limit', 64)->nullable()->after('locations_count');
            $table->string('teams_count', 64)->nullable()->after('time_limit');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['distance', 'locations_count', 'time_limit', 'teams_count']);
        });
    }
};
