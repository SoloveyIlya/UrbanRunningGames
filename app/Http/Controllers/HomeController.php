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
        $heroVideo = HeroVideo::with(['videoMedia', 'videoMobileMedia', 'posterMedia', 'posterMobileMedia'])
            ->where('page', HeroVideo::PAGE_MAIN)->first();

        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->with(['albums.media', 'city', 'coverMedia'])
            ->limit(5)
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

        $infoSectionTitle = Schema::hasTable('site_settings')
            ? (SiteSetting::get(SiteSetting::KEY_HOME_INFO_SECTION_TITLE) ?? 'ИНФОРМАЦИЯ')
            : 'ИНФОРМАЦИЯ';

        $infoAccordionJson = Schema::hasTable('site_settings')
            ? SiteSetting::get(SiteSetting::KEY_HOME_INFO_ACCORDION_ITEMS)
            : null;

        if ($infoAccordionJson !== null && $infoAccordionJson !== '') {
            $infoAccordionItems = json_decode($infoAccordionJson, true);
            $infoAccordionItems = is_array($infoAccordionItems) ? $infoAccordionItems : $this->defaultInfoAccordionItems();
        } else {
            $infoAccordionItems = $this->defaultInfoAccordionItems();
        }

        $heroOrnamentUrl = null;
        $heroOrnamentOpacity = 0.85;
        if (Schema::hasTable('site_settings')) {
            $ornamentDisabled = SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_DISABLED) === '1';
            if (!$ornamentDisabled) {
                $mediaId = SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_MEDIA_ID);
                $heroOrnamentOpacity = (float) (SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_OPACITY) ?? '0.85');
                $heroOrnamentOpacity = max(0, min(1, $heroOrnamentOpacity));
                if ($mediaId && $mediaId !== 'none') {
                    $asset = \App\Models\MediaAsset::find($mediaId);
                    $heroOrnamentUrl = $asset?->url;
                }
                if (!$heroOrnamentUrl) {
                    $heroOrnamentUrl = asset('images/ornaments/maze-1.svg');
                }
            }
        } else {
            $heroOrnamentUrl = asset('images/ornaments/maze-1.svg');
        }

        $heroOrnamentDesktopUrl = $heroOrnamentUrl;
        if (Schema::hasTable('site_settings')) {
            $desktopOrnamentId = SiteSetting::get(SiteSetting::KEY_HERO_ORNAMENT_DESKTOP_MEDIA_ID);
            if ($desktopOrnamentId) {
                $desktopAsset = \App\Models\MediaAsset::find($desktopOrnamentId);
                if ($desktopAsset?->url) {
                    $heroOrnamentDesktopUrl = $desktopAsset->url;
                }
            }
        }

        return view('home', compact('heroVideo', 'upcomingEvents', 'homeStats', 'infoSectionTitle', 'infoAccordionItems', 'heroOrnamentUrl', 'heroOrnamentDesktopUrl', 'heroOrnamentOpacity'));
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

    /** @return array<int, array{title: string, content_type: string, links?: array, content?: string}> */
    private function defaultInfoAccordionItems(): array
    {
        return [
            [
                'title' => 'Коротко о главном',
                'content_type' => 'links',
                'links' => [
                    ['text' => 'Гонки', 'url' => '/events'],
                    ['text' => 'Магазин', 'url' => '/shop'],
                    ['text' => 'О нас', 'url' => '/about'],
                    ['text' => 'Контакты', 'url' => '/contact'],
                ],
            ],
            [
                'title' => 'Основные условия',
                'content_type' => 'links',
                'links' => [
                    ['text' => 'Политика конфиденциальности', 'url' => '/privacy'],
                    ['text' => 'Согласие на обработку ПДн', 'url' => '/consent'],
                    ['text' => 'Условия продажи мерча', 'url' => '/terms'],
                    ['text' => 'Правила возвратов', 'url' => '/returns'],
                ],
            ],
            [
                'title' => 'Место старта, финиша, выдача номеров и стартовых пакетов',
                'content_type' => 'prose',
                'content' => '<p>Старт и финиш каждой гонки указаны на странице конкретного события. Там же — время и место выдачи стартовых номеров и стартовых пакетов.</p><p>Актуальную информацию по каждой гонке смотрите в разделе <a href="/events">Гонки</a>. По вопросам организации обращайтесь в <a href="/contact">Контакты</a>.</p>',
            ],
            [
                'title' => 'Где жить, как добраться до места старта',
                'content_type' => 'prose',
                'content' => '<p>Рекомендации по проживанию и проезду до места старта — на отдельной странице.</p><a href="/travel" class="btn btn--info-inline">Где жить и как добраться →</a>',
            ],
        ];
    }
}
