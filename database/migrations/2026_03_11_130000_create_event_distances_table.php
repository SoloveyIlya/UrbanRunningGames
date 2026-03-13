<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Отдельные дистанции гонки: расстояние, набор высоты, чекпоинты, лимит времени, лимит команд.
     */
    public function up(): void
    {
        Schema::create('event_distances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('distance', 64)->nullable()->comment('Расстояние');
            $table->string('elevation_gain', 64)->nullable()->comment('Набор высоты');
            $table->string('checkpoints_count', 64)->nullable()->comment('Чекпоинты с заданиями');
            $table->string('time_limit', 64)->nullable()->comment('Лимит времени');
            $table->string('teams_count', 64)->nullable()->comment('Лимит команд');
            $table->string('title', 255)->nullable()->comment('Название дистанции, например «Короткая»');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_distances');
    }
};
