@extends('layouts.app')

@section('title', 'Гонки - Urban Running Games')

@section('content')
<x-hero :hero-video="$heroVideo" title="Гонки" :hide-subtitle="true">
    <a href="{{ route('events.index') }}#events-list" class="btn btn-primary">К списку гонок</a>
</x-hero>
<div id="events-list" class="events-list-anchor"></div>

<section class="events-section upcoming-races" aria-labelledby="events-heading">
    <div class="container">
        <h2 id="events-heading" class="upcoming-races__title">Все гонки</h2>
        @if($events->count() > 0)
            <div class="upcoming-races__list">
                @foreach($events as $event)
                    @php
                        $priorityAlbum = $event->albums->sortBy('sort_order')->first();
                        $coverUrl = $event->cover_url ?? $priorityAlbum?->getCoverUrl();
                    @endphp
                    <article class="race-card race-card--horizontal">
                        <div class="race-card__image-wrap">
                            <span class="race-card__status race-card__status--{{ $event->isUpcoming() ? 'upcoming' : 'past' }}">
                                {{ $event->status_label }}
                            </span>
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
            <p class="upcoming-races__empty">Пока нет опубликованных гонок. Следите за обновлениями!</p>
        @endif
    </div>
</section>
@endsection
