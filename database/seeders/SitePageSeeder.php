<?php

namespace Database\Seeders;

use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            SitePage::SLUG_HOME_INFO => ['title' => 'Информация', 'content' => null],
            SitePage::SLUG_ABOUT => ['title' => 'О команде организатора', 'content' => null],
            SitePage::SLUG_RULES => ['title' => 'Правила забега', 'content' => null],
            SitePage::SLUG_PRIVACY => ['title' => 'Политика конфиденциальности', 'content' => null],
            SitePage::SLUG_TERMS => ['title' => 'Условия продажи мерча', 'content' => null],
            SitePage::SLUG_CONSENT => ['title' => 'Согласие на обработку данных', 'content' => null],
            SitePage::SLUG_RETURNS => ['title' => 'Возврат и обмен', 'content' => null],
        ];

        foreach ($pages as $slug => $data) {
            SitePage::query()->firstOrCreate(
                ['slug' => $slug],
                ['title' => $data['title'], 'content' => $data['content']]
            );
        }
    }
}
