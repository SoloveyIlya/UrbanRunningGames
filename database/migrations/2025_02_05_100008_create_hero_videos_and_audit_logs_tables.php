<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hero-видео по ТЗ: управление из админки (Главная, События). Журнал действий админов.
     */
    public function up(): void
    {
        Schema::create('hero_videos', function (Blueprint $table) {
            $table->id();
            $table->string('page', 64)->unique(); // main, events
            $table->foreignId('video_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('poster_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('title', 255)->nullable();
            $table->string('button_text', 64)->nullable();
            $table->string('button_url', 512)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 64);
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old')->nullable();
            $table->json('new')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('hero_videos');
    }
};
