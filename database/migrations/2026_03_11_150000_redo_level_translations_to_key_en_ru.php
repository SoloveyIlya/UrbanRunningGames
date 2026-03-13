<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Переделка таблицы уровней: одна строка на уровень — ключ, английское название, русское название.
     */
    public function up(): void
    {
        Schema::create('level_translations_new', function (Blueprint $table) {
            $table->id();
            $table->string('level_key', 64)->unique();
            $table->string('label_en', 255)->nullable();
            $table->string('label_ru', 255)->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('level_translations')) {
            $keys = DB::table('level_translations')->distinct()->pluck('level_key');
            foreach ($keys as $key) {
                $en = DB::table('level_translations')->where('level_key', $key)->where('locale', 'en')->first();
                $ru = DB::table('level_translations')->where('level_key', $key)->where('locale', 'ru')->first();
                DB::table('level_translations_new')->insert([
                    'level_key' => $key,
                    'label_en' => $en->label ?? null,
                    'label_ru' => $ru->label ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Schema::drop('level_translations');
        }

        Schema::rename('level_translations_new', 'level_translations');
    }

    public function down(): void
    {
        Schema::rename('level_translations', 'level_translations_old');

        Schema::create('level_translations', function (Blueprint $table) {
            $table->id();
            $table->string('level_key', 64);
            $table->string('locale', 8);
            $table->string('label', 255);
            $table->timestamps();
            $table->unique(['level_key', 'locale']);
        });

        $rows = DB::table('level_translations_old')->get();
        foreach ($rows as $row) {
            if ($row->label_en) {
                DB::table('level_translations')->insert([
                    'level_key' => $row->level_key,
                    'locale' => 'en',
                    'label' => $row->label_en,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
            if ($row->label_ru) {
                DB::table('level_translations')->insert([
                    'level_key' => $row->level_key,
                    'locale' => 'ru',
                    'label' => $row->label_ru,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }
        Schema::drop('level_translations_old');
    }
};
