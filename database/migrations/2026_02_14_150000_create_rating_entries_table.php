<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Строки сводного рейтинга команд (место, команда, очки, кол-во событий).
     */
    public function up(): void
    {
        Schema::create('rating_entries', function (Blueprint $table) {
            $table->id();
            $table->string('team_name');
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('events_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_entries');
    }
};
