<?php

namespace Database\Seeders;

use App\Models\LevelTranslation;
use Illuminate\Database\Seeder;

class LevelTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['level_key' => 'beginner', 'label_en' => 'Beginner', 'label_ru' => 'Начальный'],
            ['level_key' => 'advanced', 'label_en' => 'Advanced', 'label_ru' => 'Продвинутый'],
            ['level_key' => 'pro', 'label_en' => 'Pro', 'label_ru' => 'Профи'],
        ];

        foreach ($rows as $row) {
            LevelTranslation::updateOrCreate(
                ['level_key' => $row['level_key']],
                ['label_en' => $row['label_en'], 'label_ru' => $row['label_ru']]
            );
        }
    }
}
