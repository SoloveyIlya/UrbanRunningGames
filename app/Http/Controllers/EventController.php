<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'published')
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('events.index', compact('events'));
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->with(['city', 'partners', 'albums'])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    public function archive()
    {
        $events = Event::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->orderBy('starts_at', 'desc')
            ->get()
            ->groupBy(function ($event) {
                return $event->starts_at->format('Y');
            });

        return view('events.archive', compact('events'));
    }
}
