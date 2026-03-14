<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('hero_videos', 'video_mobile_media_id')) {
            Schema::table('hero_videos', function (Blueprint $table) {
                $table->foreignId('video_mobile_media_id')
                    ->nullable()
                    ->after('video_media_id')
                    ->constrained('media_assets')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('hero_videos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('video_mobile_media_id');
        });
    }
};
