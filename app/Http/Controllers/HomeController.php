<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HeroVideo;
use App\Models\Partner;
use App\Models\SitePage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $heroVideo = HeroVideo::where('page', HeroVideo::PAGE_MAIN)->first();

        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->limit(3)
            ->get();

        $partners = Partner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $homeInfo = SitePage::getBySlug(SitePage::SLUG_HOME_INFO);

        return view('home', compact('heroVideo', 'upcomingEvents', 'partners', 'homeInfo'));
    }
}
