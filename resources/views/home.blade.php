@extends('layouts.app')

@section('title', 'Главная - Urban Running Games')

@section('content')
<x-hero :hero-video="$heroVideo">
    <a href="{{ route('events.index') }}" class="btn btn-primary">Предстоящие события</a>
    <a href="{{ route('about') }}" class="btn btn-secondary">О команде</a>
</x-hero>

<section class="home-stats" aria-labelledby="home-stats-heading">
    <div class="home-stats__bg" aria-hidden="true">
        <svg class="home-stats__shape" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1340 636" preserveAspectRatio="xMidYMid meet">
            <defs>
                <linearGradient id="home-stats-gradient" x1="0" y1="318" x2="1340" y2="318" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#000000"/>
                    <stop offset="1" stop-color="#424141"/>
                </linearGradient>
                <clipPath id="home-stats-shape-clip">
                    <path d="M0 124.69C0 120.189 1.51817 115.82 4.30899 112.288L87.05 7.59871C90.843 2.79948 96.6238 0 102.741 0H1320C1331.05 0 1340 8.9543 1340 20V527.716C1340 533.02 1337.89 538.107 1334.14 541.858L1245.86 630.142C1242.11 633.893 1237.02 636 1231.72 636H20C8.95429 636 0 627.046 0 616V124.69Z"/>
                </clipPath>
            </defs>
            <g clip-path="url(#home-stats-shape-clip)">
                <path fill="url(#home-stats-gradient)" d="M0 124.69C0 120.189 1.51817 115.82 4.30899 112.288L87.05 7.59871C90.843 2.79948 96.6238 0 102.741 0H1320C1331.05 0 1340 8.9543 1340 20V527.716C1340 533.02 1337.89 538.107 1334.14 541.858L1245.86 630.142C1242.11 633.893 1237.02 636 1231.72 636H20C8.95429 636 0 627.046 0 616V124.69Z"/>
                <image href="{{ asset('images/image.png') }}" x="670" y="0" width="670" height="636" preserveAspectRatio="xMidYMid slice"/>
            </g>
        </svg>
    </div>
    <div class="container home-stats__container">
        <h2 id="home-stats-heading" class="visually-hidden">Статистика проекта</h2>
        <ul class="home-stats__grid">
            <li class="home-stats__item">
                <span class="home-stats__number">12</span>
                <span class="home-stats__label">оригинальных тематических забегов</span>
                <span class="home-stats__desc">Получайте очки для победы в общем зачёте</span>
            </li>
            <li class="home-stats__item">
                <span class="home-stats__number">34</span>
                <span class="home-stats__label">увлекательных маршрутов</span>
                <span class="home-stats__desc">Определяйте лучшую логистику для победы</span>
            </li>
            <li class="home-stats__item">
                <span class="home-stats__number">300+</span>
                <span class="home-stats__label">ключевых локаций</span>
                <span class="home-stats__desc">Узнавайте редкие места, погружайтесь в легендарные истории</span>
            </li>
            <li class="home-stats__item">
                <span class="home-stats__number">600+</span>
                <span class="home-stats__label">интеллектуальных и активных заданий</span>
                <span class="home-stats__desc">Разгадывайте и узнавайте</span>
            </li>
        </ul>
    </div>
</section>

<section class="upcoming-events">
    <div class="container">
        <h2>Ближайшие события</h2>
        @if($upcomingEvents->count() > 0)
            <div class="events-grid">
                @foreach($upcomingEvents as $event)
                    <div class="event-card">
                        <div class="event-date">
                            <span class="day">{{ $event->starts_at->format('d') }}</span>
                            <span class="month">{{ $event->starts_at->translatedFormat('M') }}</span>
                        </div>
                        <div class="event-info">
                            <h3>{{ $event->title }}</h3>
                            <p class="event-location">
                                @if($event->city)
                                    {{ $event->city->name }}
                                @endif
                                @if($event->location_text)
                                    , {{ $event->location_text }}
                                @endif
                            </p>
                            @if($event->description)
                                <p class="event-description">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                            @endif
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm">Подробнее</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Пока нет предстоящих событий. Следите за обновлениями!</p>
        @endif
        <div class="text-center mt-4">
            <a href="{{ route('events.index') }}" class="btn">Все события</a>
        </div>
    </div>
</section>

@if($partners->count() > 0)
<section class="partners">
    <div class="container">
        <h2>Наши партнёры</h2>
        <div class="partners-grid">
            @foreach($partners as $partner)
                <div class="partner-item">
                    @if($partner->logo_media_id)
                        <img src="#" alt="{{ $partner->name }}" class="partner-logo">
                    @endif
                    <h4>{{ $partner->name }}</h4>
                    @if($partner->description)
                        <p>{{ $partner->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('partners') }}" class="btn">Все партнёры</a>
        </div>
    </div>
</section>
@endif
@endsection
