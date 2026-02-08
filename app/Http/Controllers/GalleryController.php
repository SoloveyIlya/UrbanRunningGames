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
    public function index()
    {
        $events = Event::where('status', 'published')
            ->whereHas('albums', fn ($q) => $q->published())
            ->with(['albums' => fn ($q) => $q->published()->with('coverMedia')->withCount('items')->orderBy('sort_order')])
            ->orderBy('starts_at', 'desc')
            ->get();

        return view('gallery.index', compact('events'));
    }

    /**
     * Просмотр одного альбома (только опубликованные).
     */
    public function show(Album $album)
    {
        if ($album->published_at === null) {
            abort(404);
        }

        $album->load(['event', 'coverMedia', 'items']);

        return view('gallery.show', compact('album'));
    }
}
