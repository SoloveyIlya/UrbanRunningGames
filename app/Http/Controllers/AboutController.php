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

        $heroTitle = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_TITLE, 'Команда');
        $heroSubtitle = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_SUBTITLE, 'Люди, которые делают Urban Running Games — забеги-игры в вашем городе');
        $missionTitle = SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_TITLE, 'Наша миссия');
        $missionContent = SiteSetting::get(SiteSetting::KEY_ABOUT_MISSION_CONTENT, '');

        $heroBackgroundUrl = null;
        $bgMediaId = SiteSetting::get(SiteSetting::KEY_ABOUT_HERO_BACKGROUND_MEDIA_ID);
        if ($bgMediaId) {
            $heroBackgroundUrl = MediaAsset::find($bgMediaId)?->url;
        }

        $teamMembers = [];
        $teamJson = SiteSetting::get(SiteSetting::KEY_ABOUT_TEAM_MEMBERS);
        if ($teamJson) {
            $raw = json_decode($teamJson, true) ?: [];
            foreach ($raw as $member) {
                $photoUrl = null;
                if (!empty($member['photo_media_id'])) {
                    $asset = MediaAsset::find($member['photo_media_id']);
                    $photoUrl = $asset?->url;
                }
                $teamMembers[] = ['photo_url' => $photoUrl];
            }
        }

        return view('about', [
            'title' => $page?->title ?? 'О команде организатора',
            'content' => $page?->content,
            'hero_title' => $heroTitle,
            'hero_subtitle' => $heroSubtitle,
            'mission_title' => $missionTitle,
            'mission_content' => $missionContent,
            'hero_background_url' => $heroBackgroundUrl,
            'team_members' => $teamMembers,
        ]);
    }
}
