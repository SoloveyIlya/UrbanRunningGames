@extends('layouts.app')

@section('title', 'Главная - Urban Running Games')

@section('content')
<x-hero :hero-video="$heroVideo">
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
        <ul class="home-stats__grid home-stats__grid--columns">
            @foreach($homeStats ?? [] as $stat)
            <li class="home-stats__item">
                <span class="home-stats__number">{{ $stat['number'] ?? '' }}</span>
                <span class="home-stats__label">{{ $stat['label'] ?? '' }}</span>
                <span class="home-stats__desc">{{ $stat['desc'] ?? '' }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="upcoming-races" aria-labelledby="upcoming-races-heading">
    <div class="container">
        <h2 id="upcoming-races-heading" class="upcoming-races__title">Ближайшие гонки</h2>
        @if($upcomingEvents->count() > 0)
            <div class="upcoming-races__list">
                @foreach($upcomingEvents as $event)
                    @php
                        $priorityAlbum = $event->albums->sortBy('sort_order')->first();
                        $coverUrl = $event->cover_url ?? $priorityAlbum?->getCoverUrl();
                    @endphp
                    <article class="race-card race-card--horizontal">
                        <div class="race-card__image-wrap">
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
                <a href="{{ route('events.index') }}" class="race-card race-card--all">
                    <svg class="race-card--all__svg" viewBox="0 0 618 327" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <filter id="raceCardAllFilter" x="0" y="0" width="618" height="327" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="2"/>
                                <feComposite in2="hardAlpha" operator="out"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
                            </filter>
                            <linearGradient id="raceCardAllGradient" x1="4" y1="160" x2="614" y2="160" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#26203A"/>
                                <stop offset="1" stop-color="#6958A0"/>
                            </linearGradient>
                        </defs>
                        <g filter="url(#raceCardAllFilter)">
                            <path d="M4 20C4 8.9543 12.9543 0 24 0H594C605.046 0 614 8.95431 614 20V210.716C614 216.02 611.893 221.107 608.142 224.858L519.858 313.142C516.107 316.893 511.02 319 505.716 319H24C12.9543 319 4 310.046 4 299V20Z" fill="url(#raceCardAllGradient)"/>
                        </g>
                    </svg>
                    <span class="race-card--all__inner">
                        <span class="race-card__all-text">Смотреть остальные гонки</span>
                        <span class="race-card__all-arrow" aria-hidden="true">↗</span>
                    </span>
                </a>
            </div>
        @else
            <p class="upcoming-races__empty">Пока нет предстоящих гонок. Следите за обновлениями!</p>
            <div class="text-center mt-4">
                <a href="{{ route('events.index') }}" class="btn btn--primary">Все гонки</a>
            </div>
        @endif
    </div>
</section>

<section class="info-section" aria-labelledby="info-heading">
    <div class="container">
        <h2 id="info-heading" class="info-section__title">ИНФОРМАЦИЯ</h2>
        <div class="info-accordion">
            <div class="info-accordion__item" data-accordion-item>
                <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="info-body-1" id="info-btn-1" data-accordion-trigger>
                    <span class="info-accordion__title-text">Коротко о главном</span>
                    <span class="info-accordion__icon" aria-hidden="true">+</span>
                </button>
                <div id="info-body-1" class="info-accordion__body" role="region" aria-labelledby="info-btn-1" hidden>
                    <div class="info-accordion__content">
                        <a href="{{ route('home') }}">Главная</a>,
                        <a href="{{ route('events.index') }}">Гонки</a>,
                        <a href="{{ route('rating') }}">Рейтинг</a>,
                        <a href="{{ route('gallery.index') }}">Фото</a>,
                        <a href="{{ route('shop.index') }}">Магазин</a>,
                        <a href="{{ route('about') }}">О нас</a>,
                        <a href="{{ route('partners') }}">Партнёры</a>,
                        <a href="{{ route('contact') }}">Контакты</a>.
                    </div>
                </div>
            </div>
            <div class="info-accordion__item" data-accordion-item>
                <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="info-body-2" id="info-btn-2" data-accordion-trigger>
                    <span class="info-accordion__title-text">Программа Т-Банк Dagestan Wild Trail 2026</span>
                    <span class="info-accordion__icon" aria-hidden="true">+</span>
                </button>
                <div id="info-body-2" class="info-accordion__body" role="region" aria-labelledby="info-btn-2" hidden>
                    <div class="info-accordion__content">
                        Информация о программе готовится.
                    </div>
                </div>
            </div>
            <div class="info-accordion__item" data-accordion-item>
                <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="info-body-3" id="info-btn-3" data-accordion-trigger>
                    <span class="info-accordion__title-text">Основные условия</span>
                    <span class="info-accordion__icon" aria-hidden="true">+</span>
                </button>
                <div id="info-body-3" class="info-accordion__body" role="region" aria-labelledby="info-btn-3" hidden>
                    <div class="info-accordion__content">
                        <a href="{{ route('partners') }}">Страница для партнёров</a> — условия сотрудничества и контакты.
                    </div>
                </div>
            </div>
            <div class="info-accordion__item" data-accordion-item>
                <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="info-body-4" id="info-btn-4" data-accordion-trigger>
                    <span class="info-accordion__title-text">Место старта, финиша, выдача номеров и стартовых пакетов</span>
                    <span class="info-accordion__icon" aria-hidden="true">+</span>
                </button>
                <div id="info-body-4" class="info-accordion__body" role="region" aria-labelledby="info-btn-4" hidden>
                    <div class="info-accordion__content">
                        <a href="{{ route('legal.privacy') }}">Политика конфиденциальности</a>,
                        <a href="{{ route('legal.consent') }}">Согласие на обработку ПДн</a>.
                    </div>
                </div>
            </div>
            <div class="info-accordion__item" data-accordion-item>
                <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="info-body-5" id="info-btn-5" data-accordion-trigger>
                    <span class="info-accordion__title-text">Где жить, как добраться до места старта</span>
                    <span class="info-accordion__icon" aria-hidden="true">+</span>
                </button>
                <div id="info-body-5" class="info-accordion__body" role="region" aria-labelledby="info-btn-5" hidden>
                    <div class="info-accordion__content">
                        <a href="{{ route('legal.terms') }}">Условия продажи мерча</a>,
                        <a href="{{ route('legal.returns') }}">Правила возвратов</a>.
                    </div>
                </div>
            </div>
        </div>
        <div class="info-section__action">
            <a href="{{ route('legal.terms') }}" class="btn btn--info-position">
                <span class="btn--info-position__text">ПОЛОЖЕНИЕ</span>
                <span class="btn--info-position__arrow" aria-hidden="true">↗</span>
            </a>
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
