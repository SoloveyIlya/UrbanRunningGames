<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HeroVideo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $heroVideo = HeroVideo::where('page', HeroVideo::PAGE_EVENTS)->first();

        $query = Event::where('status', 'published')
            ->with(['albums' => fn ($q) => $q->orderBy('sort_order')->with('media'), 'city', 'coverMedia']);

        $cityId = $request->integer('city_id');
        if ($cityId > 0) {
            $query->where('city_id', $cityId);
        }

        $year = $request->integer('year');
        $events = $query->get();

        $upcomingEvents = $events->filter(fn ($e) => $e->isUpcoming())->sortBy('starts_at')->values();
        $pastEvents = $events->filter(fn ($e) => $e->isPast())->sortByDesc('starts_at')->values();

        if ($year > 0) {
            $pastEvents = $pastEvents->filter(fn ($e) => (int) $e->starts_at->format('Y') === $year)->values();
        }

        $distanceFilter = $request->filled('distance') ? trim((string) $request->input('distance')) : null;
        $locationsCountMin = $request->integer('locations_count_min');
        $locationsCountMax = $request->filled('locations_count_max') ? $request->integer('locations_count_max') : null;
        $timeLimitFilter = $request->filled('time_limit') ? trim((string) $request->input('time_limit')) : null;
        $teamsCountMin = $request->integer('teams_count_min');
        $teamsCountMax = $request->filled('teams_count_max') ? $request->integer('teams_count_max') : null;

        $filterByParams = function ($collection) use ($distanceFilter, $locationsCountMin, $locationsCountMax, $timeLimitFilter, $teamsCountMin, $teamsCountMax) {
            if ($distanceFilter !== null && $distanceFilter !== '') {
                $collection = $collection->filter(fn ($e) => $e->distance && stripos((string) $e->distance, $distanceFilter) !== false);
            }
            if ($locationsCountMin > 0) {
                $collection = $collection->filter(fn ($e) => (int) $e->locations_count >= $locationsCountMin);
            }
            if ($locationsCountMax !== null && $locationsCountMax > 0) {
                $collection = $collection->filter(fn ($e) => (int) $e->locations_count <= $locationsCountMax);
            }
            if ($timeLimitFilter !== null && $timeLimitFilter !== '') {
                $collection = $collection->filter(fn ($e) => $e->time_limit && stripos((string) $e->time_limit, $timeLimitFilter) !== false);
            }
            if ($teamsCountMin > 0) {
                $collection = $collection->filter(fn ($e) => (int) $e->teams_count >= $teamsCountMin);
            }
            if ($teamsCountMax !== null && $teamsCountMax > 0) {
                $collection = $collection->filter(fn ($e) => (int) $e->teams_count <= $teamsCountMax);
            }
            return $collection->values();
        };

        $upcomingEvents = $filterByParams($upcomingEvents);
        $pastEvents = $filterByParams($pastEvents);

        $pastEventsPerPage = 6;
        $page = $request->integer('page', 1);
        $pastEventsPaginator = new LengthAwarePaginator(
            $pastEvents->forPage($page, $pastEventsPerPage)->values(),
            $pastEvents->count(),
            $pastEventsPerPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $cities = \App\Models\City::whereHas('events', fn ($q) => $q->where('status', 'published'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $years = Event::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->get()
            ->map(fn ($e) => (int) $e->starts_at->format('Y'))
            ->unique()
            ->sort()
            ->reverse()
            ->values()
            ->all();

        return view('events.index', compact('heroVideo', 'upcomingEvents', 'pastEventsPaginator', 'cities', 'years'));
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->with(['city', 'partners', 'albums' => fn ($q) => $q->published()->orderBy('sort_order')])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    public function archive(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->orderBy('starts_at', 'desc')
            ->with(['city']);

        $perPage = 6;
        $events = $query->paginate($perPage)->withQueryString();

        return view('events.archive', compact('events'));
    }
}
