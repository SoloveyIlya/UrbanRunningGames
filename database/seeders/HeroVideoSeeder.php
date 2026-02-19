<?php

namespace Database\Seeders;

use App\Models\HeroVideo;
use Illuminate\Database\Seeder;

class HeroVideoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([HeroVideo::PAGE_MAIN, HeroVideo::PAGE_EVENTS] as $page) {
            HeroVideo::firstOrCreate(
                ['page' => $page],
                [
                    'title' => $page === HeroVideo::PAGE_MAIN ? 'Urban Running Games' : 'События',
                    'button_text' => $page === HeroVideo::PAGE_MAIN ? 'Предстоящие события' : null,
                    'button_url' => $page === HeroVideo::PAGE_MAIN ? '/events' : null,
                    'is_enabled' => true,
                ]
            );
        }
    }
}
