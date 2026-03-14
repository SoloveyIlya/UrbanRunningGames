@extends('layouts.app')

@section('title', 'Главная - Urban Running Games')

@section('content')
<x-hero :hero-video="$heroVideo" :hero-ornament-url="$heroOrnamentUrl ?? null" :hero-ornament-desktop-url="$heroOrnamentDesktopUrl ?? null" :hero-ornament-opacity="$heroOrnamentOpacity ?? 0.85">
</x-hero>

<section class="home-stats relative z-[2] mt-36 md:mt-24 sm:mt-16 mt-10 mx-auto w-full max-w-full text-white pb-16 md:pb-10 sm:pb-8 pb-6" aria-labelledby="home-stats-heading">
    <div class="home-stats__bg absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
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
    <div class="container home-stats__container relative z-[1] block max-w-[1200px] w-full mx-auto box-border pl-6 pr-4 sm:pl-12 md:pl-16 sm:pr-4 px-3 md:pr-5">
        <h2 id="home-stats-heading" class="visually-hidden">Статистика проекта</h2>
        <ul class="home-stats__grid home-stats__grid--columns grid gap-5 list-none m-0 p-0 min-w-0">
            @foreach($homeStats ?? [] as $stat)
            <li class="home-stats__item flex flex-col justify-start text-left p-4 sm:p-5 md:p-6 overflow-hidden box-border bg-white/5 backdrop-blur-[5px] rounded-lg">
                <span class="home-stats__number block text-xl sm:text-2xl md:text-3xl font-bold leading-tight text-[#8D49EE] mb-1">{{ $stat['number'] ?? '' }}</span>
                <span class="home-stats__label block text-xs sm:text-sm md:text-base font-medium text-white leading-snug mb-1">{{ $stat['label'] ?? '' }}</span>
                <span class="home-stats__desc block text-xs sm:text-sm text-gray-200 leading-snug max-sm:hidden">{{ $stat['desc'] ?? '' }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="upcoming-races text-white pb-12 pt-12 sm:pb-16 sm:pt-16 md:pt-20 md:pb-20" aria-labelledby="upcoming-races-heading">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <h2 id="upcoming-races-heading" class="upcoming-races__title text-left text-xl sm:text-2xl md:text-3xl font-bold uppercase tracking-wide ml-0 sm:ml-12 md:ml-0 mb-6 sm:mb-8 text-white">Ближайшие гонки</h2>
        @if($upcomingEvents->count() > 0)
            <div class="upcoming-races__list grid gap-4 sm:gap-6 mt-0 items-stretch max-w-full grid-cols-1 md:max-w-full md:mr-0 md:ml-0 lg:grid-cols-2 lg:max-w-none">
                @foreach($upcomingEvents as $event)
                    @php
                        $priorityAlbum = $event->albums->sortBy('sort_order')->first();
                        $coverUrl = $event->cover_url ?? $priorityAlbum?->getCoverUrl();
                    @endphp
                    <article class="race-card race-card--horizontal grid grid-cols-1 sm:grid-cols-[225px_1fr] min-w-0 w-full max-w-[490px] min-h-0 sm:min-h-[303px] rounded-lg overflow-hidden bg-white/5 backdrop-blur-[5px] shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl" @if($coverUrl) style="--race-card-cover: url('{{ e($coverUrl) }}');" @endif>
                        <div class="race-card__image-wrap relative w-full min-h-[180px] sm:min-h-full col-span-1 self-stretch hidden sm:block">
                            <div class="race-card__image absolute inset-0 w-full h-full bg-cover bg-center bg-[#2d2d2d] {{ $coverUrl ? '' : 'race-card__image--no-photo' }}" @if($coverUrl) style="background-image: url('{{ e($coverUrl) }}');" @endif></div>
                        </div>
                        <div class="race-card__body col-span-1 w-full min-w-0 p-4 sm:p-5 flex flex-col justify-between bg-[#363636] sm:bg-transparent">
                            <div class="race-card__head flex flex-col items-start gap-1 mb-2 sm:mb-3">
                                <h3 class="race-card__name text-base sm:text-xl font-bold italic text-white m-0">{{ $event->title }}</h3>
                                <span class="race-card__date text-xs sm:text-sm text-white/60 m-0">{{ $event->starts_at->format('d.m.Y') }}</span>
                            </div>
                            <dl class="race-card__params grid grid-cols-1 gap-0.5 sm:gap-1 m-0 mb-2 text-xs sm:text-sm">
                                <div class="race-card__param flex gap-1 m-0 text-sm"><dt class="text-white/90 font-medium m-0">Расстояние:</dt><dd class="text-white/65 m-0">{{ $event->distance ?? '—' }}</dd></div>
                                <div class="race-card__param flex gap-1 m-0 text-sm"><dt class="text-white/90 font-medium m-0">Локаций:</dt><dd class="text-white/65 m-0">{{ $event->locations_count ?? '—' }}</dd></div>
                                <div class="race-card__param flex gap-1 m-0 text-sm"><dt class="text-white/90 font-medium m-0">Лимит:</dt><dd class="text-white/65 m-0">{{ $event->time_limit ?? '—' }}</dd></div>
                                <div class="race-card__param flex gap-1 m-0 text-sm"><dt class="text-white/90 font-medium m-0">Команд:</dt><dd class="text-white/65 m-0">{{ $event->teams_count ?? '—' }}</dd></div>
                            </dl>
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn--race mt-2">Подробнее</a>
                        </div>
                    </article>
                @endforeach
                <a href="{{ route('events.index') }}" class="race-card race-card--all flex items-start justify-start w-full min-w-0 min-h-[200px] sm:h-[303px] sm:min-h-[303px] no-underline text-white overflow-hidden rounded-lg">
                    <span class="race-card--all__inner relative z-[1] inline-flex items-start justify-start p-4 sm:p-5 box-border">
                        <span class="race-card__all-text relative z-[1] text-base sm:text-xl font-bold uppercase tracking-wide text-left m-0 text-white">Смотреть все<br>гонки</span>
                    </span>
                </a>
            </div>
        @else
            <p class="upcoming-races__empty text-center text-white/80 mb-4">Пока нет предстоящих гонок. Следите за обновлениями!</p>
            <div class="text-center mt-4 flex justify-center">
                <a href="{{ route('events.index') }}" class="btn btn--info-position">
                    <span class="btn--info-position__text">Все гонки</span>
                </a>
            </div>
        @endif
    </div>
</section>

<section class="info-section bg-[#121315] text-white py-8 pb-10 sm:py-12 sm:pb-14 md:py-14 md:pb-16" aria-labelledby="info-heading">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <div class="info-section__head flex flex-row flex-wrap items-center justify-between gap-4 mb-6 sm:mb-8">
            <h2 id="info-heading" class="info-section__title text-left text-xl sm:text-2xl md:text-3xl font-bold uppercase tracking-wide ml-0 sm:ml-12 md:ml-0 mb-0 text-white">{{ $infoSectionTitle }}</h2>
            <div class="info-section__action shrink-0 self-center">
                <a href="{{ route('rules') }}" class="btn btn--info-position">
                    <span class="btn--info-position__text">Положение</span>
                </a>
            </div>
        </div>
        <div class="info-accordion max-w-[998px] mr-auto ml-0 mb-8 sm:mb-10 flex flex-col gap-2 sm:gap-3 w-full min-w-0">
            @foreach($infoAccordionItems ?? [] as $index => $item)
                @php
                    $idx = $index + 1;
                    $bodyId = 'info-body-' . $idx;
                    $btnId = 'info-btn-' . $idx;
                @endphp
                <div class="info-accordion__item w-full max-w-[998px] min-w-0 overflow-hidden" data-accordion-item>
                    <button type="button" class="info-accordion__header w-full min-w-0 box-border flex items-center justify-between gap-3 sm:gap-4 py-3 px-4 sm:py-3.5 sm:px-[18px] min-h-[48px] sm:min-h-[52px] bg-transparent border border-white rounded-lg sm:rounded-[7.5px] text-white text-sm sm:text-base font-normal cursor-pointer text-left transition-colors duration-200 hover:bg-white/5 touch-manipulation" aria-expanded="false" aria-controls="{{ $bodyId }}" id="{{ $btnId }}" data-accordion-trigger>
                        <span class="info-accordion__title-text flex-1 min-w-0 text-left">{{ $item['title'] ?? '' }}</span>
                        <span class="info-accordion__icon shrink-0 w-6 h-6 flex items-center justify-center rounded-xl bg-[#8D49EE] text-white text-base" aria-hidden="true">+</span>
                    </button>
                    <div id="{{ $bodyId }}" class="info-accordion__body min-w-0 overflow-hidden transition-[max-height] duration-300 ease-out" role="region" aria-labelledby="{{ $btnId }}" hidden>
                        <div class="info-accordion__content py-4 px-4 sm:py-5 sm:px-6 pb-4 sm:pb-6 mx-px mb-px rounded-b-lg border border-white/10 border-t-0 text-sm sm:text-base text-white/90 leading-relaxed tracking-tight opacity-0 transition-opacity duration-300 delay-100 bg-white/5 {{ ($item['content_type'] ?? '') === 'prose' ? 'info-accordion__content--prose' : '' }}">
                            @if(($item['content_type'] ?? '') === 'links' && !empty($item['links']))
                                <ul class="info-links info-links--column flex flex-col gap-1 list-none m-0 p-0">
                                    @foreach($item['links'] as $link)
                                        <li><a href="{{ isset($link['url']) ? e($link['url']) : '#' }}">{{ e($link['text'] ?? '') }}</a></li>
                                    @endforeach
                                </ul>
                            @elseif(($item['content_type'] ?? '') === 'prose' && isset($item['content']))
                                {!! $item['content'] !!}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
