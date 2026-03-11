@extends('layouts.app')

@section('title', $event->title . ' - Urban Running Games')

@section('content')
@php
    $coverUrl = $event->cover_url ?? $event->albums->sortBy('sort_order')->first()?->getCoverUrl();
    $useVideo = $event->hero_video_media_id && $event->hero_video_url;
    $posterUrl = $coverUrl ?? $event->cover_url;
    $levelLabelEn = $event->level ? \App\Models\LevelTranslation::labelFor($event->level, 'en') : null;
    $levelLabelRu = $event->level ? \App\Models\LevelTranslation::labelFor($event->level, 'ru') : null;
@endphp
<div class="race-detail-page">
    {{-- Hero: видео/постер и орнамент настраиваются в самой гонке --}}
    <div class="race-detail-hero hero {{ $useVideo ? 'hero--with-video' : '' }}">
        @if($useVideo)
            <video
                class="hero__video"
                autoplay
                muted
                loop
                playsinline
                @if($posterUrl) poster="{{ $posterUrl }}" @endif
            >
                <source src="{{ $event->hero_video_url }}" type="video/mp4">
            </video>
        @elseif($posterUrl)
            <div class="hero__poster" style="background-image: url('{{ e($posterUrl) }}');"></div>
        @endif
        <div class="hero__overlay"></div>
        @if($event->hero_ornament_url ?? null)
            <div class="hero__ornament hero__ornament--mobile" style="background-image: url('{{ e($event->hero_ornament_url) }}'); opacity: {{ max(0, min(1, $event->hero_ornament_opacity)) }};"></div>
        @endif
        @if($event->hero_ornament_url ?? null)
            <div class="hero__ornament hero__ornament--desktop" style="background-image: url('{{ e($event->hero_ornament_url) }}'); opacity: {{ max(0, min(1, $event->hero_ornament_opacity)) }};"></div>
        @endif
        <div class="race-detail-hero__inner">
            <nav class="race-detail-breadcrumb" aria-label="Хлебные крошки">
                <a href="{{ route('home') }}">Главная</a>
                <span class="race-detail-breadcrumb__sep">/</span>
                <a href="{{ route('events.index') }}">Гонки</a>
                <span class="race-detail-breadcrumb__sep">/</span>
                <span class="race-detail-breadcrumb__current" aria-current="page">{{ $event->title }}</span>
            </nav>
            <div class="race-detail-hero__illustration" style="background-image: url('{{ asset('images/illustration-races.png') }}');" role="img" aria-hidden="true"></div>
            <h1 class="race-detail-hero__title">{{ $event->title }}</h1>
            @if($event->level && $levelLabelEn)
                <div class="race-detail-hero__level">
                    <span class="race-detail-hero__level-en">Level: {{ $levelLabelEn }}</span>
                    @if($levelLabelRu)
                        <span class="race-detail-hero__level-ru">(Уровень: {{ $levelLabelRu }})</span>
                    @else
                        <span class="race-detail-hero__level-ru">(Уровень: {{ $levelLabelEn }})</span>
                    @endif
                </div>
            @endif
            <p class="race-detail-hero__date">{{ $event->starts_at->translatedFormat('d F Y, H:i') }}</p>
            <a href="#register" class="btn btn--race mt-2">Регистрация</a>
        </div>
    </div>

    <div class="race-detail-content">
        <div class="race-detail-content__inner">
            <h2 class="race-detail-content__heading">Уникальная беговая игра, единая история и легенда</h2>
            @if($event->description)
                <div class="race-detail-content__text">
                    {!! nl2br(e($event->description)) !!}
                </div>
            @else
                <p class="race-detail-content__text">Динамичный сценарий с историей, где каждая загадка приближает вас к финишу. Вы будете не просто бежать, а исследовать, искать подсказки и видеть образ города под новым углом. Пройдя игру, вы станете частью уникальной истории, которая больше не повторится.</p>
            @endif

            {{-- Карточки дистанций --}}
            <div class="race-detail-cards">
                @forelse($event->distances as $d)
                <article class="race-detail-card">
                    <h3 class="race-detail-card__label">Дистанция</h3>
                    <div class="race-detail-card__heading">
                        <h4 class="race-detail-card__title">{{ strtoupper($d->title ?: ($d->distance ?? 'Дистанция')) }}</h4>
                        <span class="race-detail-card__subtitle">({{ $d->title_ru ?? $d->title ?? $d->distance ?? 'Дистанция' }})</span>
                    </div>
                    <dl class="race-detail-card__params">
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--map" aria-hidden="true"></span>
                            <dd>Расстояние от {{ $d->distance ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--up" aria-hidden="true"></span>
                            <dd>Набор высоты {{ $d->elevation_gain ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--location" aria-hidden="true"></span>
                            <dd>Чекпоинты с заданиями {{ $d->checkpoints_count ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--time" aria-hidden="true"></span>
                            <dd>Лимит времени {{ $d->time_limit ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--group" aria-hidden="true"></span>
                            <dd>Лимит команд {{ $d->teams_count ?? '—' }} ед</dd>
                        </div>
                    </dl>
                </article>
                @empty
                <article class="race-detail-card">
                    <h3 class="race-detail-card__label">Дистанция</h3>
                    <div class="race-detail-card__heading">
                        <h4 class="race-detail-card__title">{{ strtoupper($event->distance ?? 'Дистанция') }}</h4>
                        <span class="race-detail-card__subtitle">({{ $event->distance ?? 'Дистанция' }})</span>
                    </div>
                    <dl class="race-detail-card__params">
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--map" aria-hidden="true"></span>
                            <dd>Расстояние от {{ $event->distance ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--up" aria-hidden="true"></span>
                            <dd>Набор высоты от 100 м</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--location" aria-hidden="true"></span>
                            <dd>Чекпоинты с заданиями {{ $event->locations_count ?? '—' }} шт</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--time" aria-hidden="true"></span>
                            <dd>Лимит времени {{ $event->time_limit ?? '—' }}</dd>
                        </div>
                        <div class="race-detail-card__row">
                            <span class="race-detail-card__icon race-detail-card__icon--group" aria-hidden="true"></span>
                            <dd>Лимит команд {{ $event->teams_count ?? '—' }} ед</dd>
                        </div>
                    </dl>
                </article>
                @endforelse
            </div>

            @if($event->rules)
                <h2 class="race-detail-content__heading">Правила</h2>
                <div class="race-detail-rules">
                    <div class="race-detail-rules__text">{!! nl2br(e($event->rules)) !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
