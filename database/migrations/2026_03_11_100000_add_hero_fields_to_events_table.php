<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hero страницы гонки: видео, орнамент и прозрачность — настраиваются отдельно для каждой гонки.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('hero_video_media_id')->nullable()->after('cover_media_id')->constrained('media_assets')->nullOnDelete();
            $table->foreignId('hero_ornament_media_id')->nullable()->after('hero_video_media_id')->constrained('media_assets')->nullOnDelete();
            $table->decimal('hero_ornament_opacity', 3, 2)->nullable()->after('hero_ornament_media_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['hero_video_media_id']);
            $table->dropForeign(['hero_ornament_media_id']);
            $table->dropColumn(['hero_ornament_opacity']);
        });
    }
};
