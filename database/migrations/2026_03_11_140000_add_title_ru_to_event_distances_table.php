<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_distances', function (Blueprint $table) {
            $table->string('title_ru', 255)->nullable()->after('title')->comment('Название дистанции на русском');
        });
    }

    public function down(): void
    {
        Schema::table('event_distances', function (Blueprint $table) {
            $table->dropColumn('title_ru');
        });
    }
};
