<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Models\SitePage;
use App\Models\SiteSetting;

class AboutController extends Controller
{
    public function index()
    {
        $page = SitePage::getBySlug(SitePage::SLUG_ABOUT);
        $teamJson = SiteSetting::get(SiteSetting::KEY_ABOUT_TEAM_MEMBERS);
        $teamMembers = $teamJson ? (json_decode($teamJson, true) ?: []) : [];
        if (empty($teamMembers)) {
            $teamMembers = [
                ['name' => 'Алексей', 'role' => 'Организатор, основатель', 'description' => 'Идеолог формата забегов-игр.', 'experience' => 'Опыт 8 лет'],
                ['name' => 'Мария', 'role' => 'Координатор мероприятий', 'description' => 'Следит за логистикой гонок.', 'experience' => 'Опыт 5 лет'],
            ];
        }
        $missionContent = SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_CONTENT);
        if ($missionContent === null || $missionContent === '') {
            $missionContent = '<p>Делаем городские забеги-игры доступными и по-настоящему весёлыми. Объединяем людей через движение, азарт и командный дух.</p><p>Если хотите стать партнёром — пишите нам в <a href="' . url('/contact') . '">Контакты</a>.</p>';
        }

        $heroBgMediaId = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_BACKGROUND_MEDIA_ID);
        $heroBackgroundUrl = $heroBgMediaId ? (MediaAsset::find($heroBgMediaId)?->url) : null;
        $heroOverlayOpacity = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_OVERLAY_OPACITY, '0.35');
        if (!is_numeric($heroOverlayOpacity)) {
            $heroOverlayOpacity = '0.35';
        }

        return view('about', [
            'title' => $page?->title ?? 'О команде организатора',
            'content' => $page?->content,
            'hero_title' => SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_TITLE, 'Команда'),
            'hero_subtitle' => SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_SUBTITLE, 'Люди, которые делают Urban Running Games — забеги-игры в вашем городе'),
            'hero_background_url' => $heroBackgroundUrl,
            'hero_overlay_opacity' => $heroOverlayOpacity,
            'mission_title' => SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_TITLE, 'Наша миссия'),
            'mission_content' => $missionContent,
            'team_members' => $teamMembers,
        ]);
    }
}
