<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HeroVideo;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index(Request $request)
    {
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

        $statusFilter = $request->input('status', '');
        if (!in_array($statusFilter, ['', 'upcoming', 'past'], true)) {
            $statusFilter = '';
        }

        $perPage = 12;
        $page = $request->integer('page', 1);

        if ($statusFilter === 'upcoming') {
            $eventsList = $upcomingEvents;
            $eventsPaginator = null;
        } elseif ($statusFilter === 'past') {
            $eventsList = $pastEvents->forPage($page, $perPage)->values();
            $eventsPaginator = new LengthAwarePaginator(
                $eventsList,
                $pastEvents->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $allEvents = $upcomingEvents->concat($pastEvents)->values();
            $eventsList = $allEvents->forPage($page, $perPage)->values();
            $eventsPaginator = new LengthAwarePaginator(
                $eventsList,
                $allEvents->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

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

        return view('events.index', compact(
            'eventsList',
            'eventsPaginator',
            'statusFilter',
            'cities',
            'years',
            'infoSectionTitle',
            'infoAccordionItems'
        ));
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
