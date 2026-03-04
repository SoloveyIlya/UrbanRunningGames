@extends('layouts.app')

@section('title', 'Гонки - Urban Running Games')

@section('content')
<div class="page-events">
<x-hero :hero-video="$heroVideo" title="Гонки" :hide-subtitle="true">
    <a href="{{ route('events.index') }}#events-list" class="btn btn-primary">К списку гонок</a>
</x-hero>
<div id="events-list" class="events-list-anchor"></div>

<section class="events-section events-filters-section" aria-label="Фильтры">
    <div class="container">
        <p class="events-section__rules-link">
            <a href="{{ route('rules') }}" class="btn btn--rules">Правила забега</a>
        </p>
        <form method="get" action="{{ route('events.index') }}#events-list" class="events-filters">
            <div class="events-filters__row">
                <label class="events-filters__label">
                    <span class="events-filters__label-text">Город</span>
                    <select name="city_id" class="events-filters__select" aria-label="Выберите город">
                        <option value="">Все города</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="events-filters__label">
                    <span class="events-filters__label-text">Год (архив)</span>
                    <select name="year" class="events-filters__select" aria-label="Выберите год">
                        <option value="">Все годы</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="btn btn-primary events-filters__submit">Показать</button>
            </div>
            <details class="events-filters__params-details" @if(request('distance') || request('locations_count_min') || request('locations_count_max') || request('time_limit') || request('teams_count_min') || request('teams_count_max')) open @endif>
                <summary class="events-filters__params-toggle">Расширенные фильтры</summary>
                <div class="events-filters__row events-filters__row--params">
                    <label class="events-filters__label">
                        <span class="events-filters__label-text">Расстояние</span>
                        <input type="text" name="distance" class="events-filters__input" placeholder="например, 10" value="{{ request('distance') }}" aria-label="Фильтр по расстоянию">
                    </label>
                    <div class="events-filters__label events-filters__label--range">
                        <span class="events-filters__label-text">Локаций</span>
                        <div class="events-filters__range">
                            <input type="number" name="locations_count_min" class="events-filters__input events-filters__input--short" min="0" placeholder="от" value="{{ request('locations_count_min') ?: '' }}" aria-label="Локаций от">
                            <span class="events-filters__range-sep">—</span>
                            <input type="number" name="locations_count_max" class="events-filters__input events-filters__input--short" min="0" placeholder="до" value="{{ request('locations_count_max') ?: '' }}" aria-label="Локаций до">
                        </div>
                    </div>
                    <label class="events-filters__label">
                        <span class="events-filters__label-text">Лимит</span>
                        <input type="text" name="time_limit" class="events-filters__input" placeholder="например, 2 ч" value="{{ request('time_limit') }}" aria-label="Фильтр по лимиту времени">
                    </label>
                    <div class="events-filters__label events-filters__label--range">
                        <span class="events-filters__label-text">Команд</span>
                        <div class="events-filters__range">
                            <input type="number" name="teams_count_min" class="events-filters__input events-filters__input--short" min="0" placeholder="от" value="{{ request('teams_count_min') ?: '' }}" aria-label="Команд от">
                            <span class="events-filters__range-sep">—</span>
                            <input type="number" name="teams_count_max" class="events-filters__input events-filters__input--short" min="0" placeholder="до" value="{{ request('teams_count_max') ?: '' }}" aria-label="Команд до">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary events-filters__submit">Показать</button>
                </div>
            </details>
        </form>
    </div>
</section>

<section class="events-section upcoming-races" aria-labelledby="upcoming-heading">
    <div class="container">
        <h2 id="upcoming-heading" class="upcoming-races__title">Предстоящие гонки</h2>
        @if($upcomingEvents->count() > 0)
            <div class="upcoming-races__list">
                @foreach($upcomingEvents as $event)
                    @php
                        $priorityAlbum = $event->albums->sortBy('sort_order')->first();
                        $coverUrl = $event->cover_url ?? $priorityAlbum?->getCoverUrl();
                    @endphp
                    <article class="race-card race-card--horizontal">
                        <div class="race-card__image-wrap">
                            <span class="race-card__status race-card__status--upcoming">Предстоящее</span>
                            <div class="race-card__image {{ $coverUrl ? '' : 'race-card__image--no-photo' }}" @if($coverUrl) style="background-image: url('{{ e($coverUrl) }}');" @endif></div>
                        </div>
                        <div class="race-card__body">
                            <div class="race-card__head">
                                <h3 class="race-card__name">{{ $event->title }}</h3>
                                <span class="race-card__date">{{ $event->starts_at->format('d.m.Y') }}</span>
                            </div>
                            <dl class="race-card__params">
                                <div class="race-card__param"><dt>Расстояние:</dt><dd>{{ $event->distance ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Локаций:</dt><dd>{{ $event->locations_count ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Лимит:</dt><dd>{{ $event->time_limit ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Команд:</dt><dd>{{ $event->teams_count ?? '—' }}</dd></div>
                            </dl>
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn--race">Подробнее</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="upcoming-races__empty">Пока нет предстоящих гонок. Следите за обновлениями!</p>
        @endif
    </div>
</section>

<section class="events-section archive-races" aria-labelledby="archive-heading">
    <div class="container">
        <h2 id="archive-heading" class="archive-races__title">Архив гонок</h2>
        @if($pastEventsPaginator->count() > 0)
            <div class="upcoming-races__list archive-races__list">
                @foreach($pastEventsPaginator as $event)
                    @php
                        $priorityAlbum = $event->albums->sortBy('sort_order')->first();
                        $coverUrl = $event->cover_url ?? $priorityAlbum?->getCoverUrl();
                    @endphp
                    <article class="race-card race-card--horizontal">
                        <div class="race-card__image-wrap">
                            <span class="race-card__status race-card__status--past">Завершено</span>
                            <div class="race-card__image {{ $coverUrl ? '' : 'race-card__image--no-photo' }}" @if($coverUrl) style="background-image: url('{{ e($coverUrl) }}');" @endif></div>
                        </div>
                        <div class="race-card__body">
                            <div class="race-card__head">
                                <h3 class="race-card__name">{{ $event->title }}</h3>
                                <span class="race-card__date">{{ $event->starts_at->format('d.m.Y') }}</span>
                            </div>
                            <dl class="race-card__params">
                                <div class="race-card__param"><dt>Расстояние:</dt><dd>{{ $event->distance ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Локаций:</dt><dd>{{ $event->locations_count ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Лимит:</dt><dd>{{ $event->time_limit ?? '—' }}</dd></div>
                                <div class="race-card__param"><dt>Команд:</dt><dd>{{ $event->teams_count ?? '—' }}</dd></div>
                            </dl>
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn--race">Подробнее</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="archive-races__pagination">
                {{ $pastEventsPaginator->links() }}
            </div>
        @else
            <p class="upcoming-races__empty archive-races__empty">В архиве пока нет гонок по выбранным фильтрам.</p>
        @endif
    </div>
</section>
</div>
@endsection
