<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Event;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Фотогалерея: альбомы по событиям.
     * Показываем только события, у которых есть хотя бы один опубликованный альбом.
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->whereHas('albums', fn ($q) => $q->published())
            ->with(['city', 'albums' => fn ($q) => $q->published()->with(['coverMedia', 'media'])->withCount('items')->orderBy('sort_order')])
            ->orderBy('starts_at', 'desc');

        $cityId = $request->integer('city_id');
        if ($cityId > 0) {
            $query->where('city_id', $cityId);
        }

        $year = $request->integer('year');
        $events = $query->get();

        if ($year > 0) {
            $events = $events->filter(fn ($e) => (int) $e->starts_at->format('Y') === $year)->values();
        }

        $cities = \App\Models\City::whereHas('events', fn ($q) => $q->where('status', 'published')
            ->whereHas('albums', fn ($q2) => $q2->published()))
            ->orderBy('name')
            ->get(['id', 'name']);

        $years = Event::where('status', 'published')
            ->whereHas('albums', fn ($q) => $q->published())
            ->get()
            ->map(fn ($e) => (int) $e->starts_at->format('Y'))
            ->unique()
            ->sort()
            ->reverse()
            ->values()
            ->all();

        return view('gallery.index', compact('events', 'cities', 'years'));
    }

    /**
     * Просмотр одного альбома (только опубликованные).
     */
    public function show(Album $album)
    {
        if ($album->published_at === null) {
            abort(404);
        }

        $album->load(['event', 'coverMedia', 'items', 'media']);

        return view('gallery.show', compact('album'));
    }
}
