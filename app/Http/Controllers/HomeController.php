<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HeroVideo;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $heroVideo = HeroVideo::where('page', HeroVideo::PAGE_MAIN)->first();

        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->with(['albums.media', 'city', 'coverMedia'])
            ->limit(6)
            ->get();

        $homeStats = [];
        if (Schema::hasTable('site_settings')) {
            for ($i = 1; $i <= 4; $i++) {
                $homeStats[] = [
                    'number' => SiteSetting::get("home_stat_{$i}_number", $this->defaultStatNumber($i)),
                    'label' => SiteSetting::get("home_stat_{$i}_label", $this->defaultStatLabel($i)),
                    'desc' => SiteSetting::get("home_stat_{$i}_desc", $this->defaultStatDesc($i)),
                ];
            }
        } else {
            for ($i = 1; $i <= 4; $i++) {
                $homeStats[] = [
                    'number' => $this->defaultStatNumber($i),
                    'label' => $this->defaultStatLabel($i),
                    'desc' => $this->defaultStatDesc($i),
                ];
            }
        }

        return view('home', compact('heroVideo', 'upcomingEvents', 'homeStats'));
    }

    private function defaultStatNumber(int $i): string
    {
        return match ($i) { 1 => '12', 2 => '34', 3 => '300+', 4 => '600+', default => '' };
    }

    private function defaultStatLabel(int $i): string
    {
        return match ($i) {
            1 => 'оригинальных тематических забегов',
            2 => 'увлекательных маршрутов',
            3 => 'ключевых локаций',
            4 => 'интеллектуальных и активных заданий',
            default => '',
        };
    }

    private function defaultStatDesc(int $i): string
    {
        return match ($i) {
            1 => 'Получайте очки для победы в общем зачёте',
            2 => 'Определяйте лучшую логистику для победы',
            3 => 'Узнавайте редкие места, погружайтесь в легендарные истории',
            4 => 'Разгадывайте и узнавайте',
            default => '',
        };
    }
}
