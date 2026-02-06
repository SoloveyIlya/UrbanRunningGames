<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc')
            ->limit(3)
            ->get();

        $partners = Partner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('home', compact('upcomingEvents', 'partners'));
    }
}
