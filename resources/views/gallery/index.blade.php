@extends('layouts.app')

@section('title', 'Фотогалерея - Urban Running Games')

@section('content')
<div class="page-gallery">
<section class="page-hero relative min-h-[38vh] flex items-center justify-center text-center bg-gradient-to-br from-[#1a1525] via-[#0c0c10] to-[#1a1525] text-white">
    <div class="hero__overlay absolute inset-0 pointer-events-none"></div>
    <div class="container relative z-10 max-w-[1200px] mx-auto px-4 sm:px-5 py-12">
        <h1 class="text-3xl md:text-4xl font-bold italic uppercase tracking-wide text-white mb-2">Фотогалерея</h1>
        <p class="page-hero__sub text-lg text-white/85 m-0">Альбомы по событиям</p>
    </div>
</section>

<section class="gallery-section gallery-filters-section py-12 md:py-16" aria-label="Фильтры">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <form method="get" action="{{ route('gallery.index') }}" class="gallery-filters">
            <div class="gallery-filters__row flex flex-wrap items-end gap-4">
                <label class="gallery-filters__label">
                    <span class="gallery-filters__label-text">Город</span>
                    <select name="city_id" class="gallery-filters__select" aria-label="Выберите город">
                        <option value="">Все города</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="gallery-filters__label">
                    <span class="gallery-filters__label-text">Год</span>
                    <select name="year" class="gallery-filters__select" aria-label="Выберите год">
                        <option value="">Все годы</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="btn btn-primary gallery-filters__submit">Показать</button>
            </div>
        </form>
    </div>
</section>

<section class="gallery-section gallery-content-section">
    <div class="container">
        @if($events->count() > 0)
            @foreach($events as $event)
                <div class="gallery-event-block">
                    <h2 class="gallery-event-title">
                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                    </h2>
                    <p class="gallery-event-meta">
                        {{ $event->starts_at->translatedFormat('d F Y') }}
                        @if($event->city)
                            · {{ $event->city->name }}
                        @endif
                    </p>
                    <div class="albums-grid">
                        @foreach($event->albums as $album)
                            <a href="{{ route('gallery.show', $album) }}" class="album-card">
                                <div class="album-cover">
                                    @if($album->getCoverUrl())
                                        <img src="{{ $album->getCoverUrl() }}" alt="{{ $album->title }}" loading="lazy" decoding="async" width="400" height="300">
                                    @else
                                        <div class="album-cover-placeholder">
                                            <span>Фото</span>
                                        </div>
                                    @endif
                                    @if($album->photos_count > 0)
                                        <span class="album-count">{{ $album->photos_count }}</span>
                                    @endif
                                </div>
                                <div class="album-info">
                                    <h3>{{ $album->title }}</h3>
                                    @if($album->description)
                                        <p>{{ \Illuminate\Support\Str::limit($album->description, 80) }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="gallery-empty">
                <p>По выбранным фильтрам альбомов не найдено. Попробуйте изменить параметры или следите за обновлениями после событий!</p>
            </div>
        @endif
    </div>
</section>
</div>
@endsection
