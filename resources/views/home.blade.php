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
                        $isUpcoming = $event->isUpcoming();
                        $cityPlace = trim(implode(', ', array_filter([$event->city?->name ?? null, $event->location_text ?? null], fn($v) => $v !== null && $v !== '')));
                        if ($cityPlace === '') { $cityPlace = '—'; }
                        $timeText = $event->starts_at->format('H:i');
                    @endphp
                    <article class="race-card race-card--horizontal race-card--events-page grid min-w-0 w-full rounded-lg overflow-hidden" @if($coverUrl) style="--race-card-cover: url('{{ e($coverUrl) }}');" @endif>
                        <div class="race-card__image-wrap race-card__image-wrap--events relative col-span-1 self-stretch sm:block">
                            @if(!$isUpcoming)
                                <span class="race-card__status race-card__status--past">Завершено</span>
                            @endif
                            <div class="race-card__image race-card__image--events absolute inset-0 w-full h-full bg-cover bg-center bg-[#2d2d2d] {{ $coverUrl ? '' : 'race-card__image--no-photo' }}" @if($coverUrl) style="background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('{{ e($coverUrl) }}');" @endif></div>
                        </div>
                        <div class="race-card__body col-span-1 w-full min-w-0 p-4 sm:p-5 flex flex-col justify-between">
                            <div class="race-card__head flex flex-col items-start gap-1 mb-2 sm:mb-3">
                                <h3 class="race-card__name text-base sm:text-xl font-bold text-white m-0">{{ $event->title }}</h3>
                                <span class="race-card__date text-xs sm:text-sm text-white/60 m-0">{{ $event->starts_at->format('d.m.Y') }}</span>
                            </div>
                            <dl class="race-card__params race-card__params--events grid grid-cols-1 gap-0.5 sm:gap-1 m-0 mb-2">
                                <div class="race-card__param race-card__param--with-icon flex items-center gap-2 m-0 text-sm">
                                    <span class="race-card__param-icon shrink-0" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0856 15.0254L11.4954 14.5626V14.5626L12.0856 15.0254ZM7.91441 15.0254L7.32423 15.4882L7.91441 15.0254ZM10 16.7541V16.0041V16.7541ZM16 7.45652H15.25C15.25 8.32461 14.7922 9.51914 14.0396 10.8497C13.3036 12.1509 12.3503 13.4724 11.4954 14.5626L12.0856 15.0254L12.6758 15.4882C13.5532 14.3693 14.5571 12.9815 15.3452 11.5882C16.1167 10.2242 16.75 8.73884 16.75 7.45652H16ZM7.91441 15.0254L8.50458 14.5626C7.6497 13.4724 6.6964 12.1509 5.96039 10.8497C5.20778 9.51914 4.75 8.32461 4.75 7.45652H4H3.25C3.25 8.73884 3.88326 10.2242 4.65478 11.5882C5.44289 12.9815 6.44679 14.3693 7.32423 15.4882L7.91441 15.0254ZM4 7.45652H4.75C4.75 4.25215 7.15135 1.75 10 1.75V1V0.25C6.22123 0.25 3.25 3.52921 3.25 7.45652H4ZM10 1V1.75C12.8486 1.75 15.25 4.25215 15.25 7.45652H16H16.75C16.75 3.52921 13.7788 0.25 10 0.25V1ZM12.0856 15.0254L11.4954 14.5626C11.0271 15.1598 10.7325 15.5313 10.4733 15.7668C10.2462 15.9731 10.1225 16.0041 10 16.0041V16.7541V17.5041C10.6074 17.5041 11.0746 17.247 11.4819 16.8771C11.857 16.5363 12.2403 16.0435 12.6758 15.4882L12.0856 15.0254ZM7.91441 15.0254L7.32423 15.4882C7.75968 16.0435 8.14295 16.5363 8.51811 16.8771C8.92538 17.247 9.3926 17.5041 10 17.5041V16.7541V16.0041C9.87748 16.0041 9.75379 15.9731 9.52669 15.7668C9.26747 15.5313 8.97291 15.1598 8.50458 14.5626L7.91441 15.0254ZM7.75 7.75H7C7 9.40685 8.34315 10.75 10 10.75V10V9.25C9.17157 9.25 8.5 8.57843 8.5 7.75H7.75ZM10 10V10.75C11.6569 10.75 13 9.40685 13 7.75H12.25H11.5C11.5 8.57843 10.8284 9.25 10 9.25V10ZM12.25 7.75H13C13 6.09315 11.6569 4.75 10 4.75V5.5V6.25C10.8284 6.25 11.5 6.92157 11.5 7.75H12.25ZM10 5.5V4.75C8.34315 4.75 7 6.09315 7 7.75H7.75H8.5C8.5 6.92157 9.17157 6.25 10 6.25V5.5Z" fill="white"/></svg>
                                    </span>
                                    <span class="race-card__param-text">{{ $cityPlace }}</span>
                                </div>
                                <div class="race-card__param race-card__param--with-icon flex items-center gap-2 m-0 text-sm">
                                    <span class="race-card__param-icon shrink-0" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.3333 9.16667V14.1667M10 5.83333V14.1667M6.66667 10.8333V14.1667M10 17.5C6.87522 17.5 5.31283 17.5 4.21756 16.7042C3.86383 16.4472 3.55276 16.1362 3.29576 15.7824C2.5 14.6872 2.5 13.1248 2.5 10C2.5 6.87522 2.5 5.31283 3.29576 4.21756C3.55276 3.86383 3.86383 3.55276 4.21756 3.29576C5.31283 2.5 6.87522 2.5 10 2.5C13.1248 2.5 14.6872 2.5 15.7824 3.29576C16.1362 3.55276 16.4472 3.86383 16.7042 4.21756C17.5 5.31283 17.5 6.87522 17.5 10C17.5 13.1248 17.5 14.6872 16.7042 15.7824C16.4472 16.1362 16.1362 16.4472 15.7824 16.7042C14.6872 17.5 13.1248 17.5 10 17.5Z" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </span>
                                    <span class="race-card__param-text">{{ $timeText }}</span>
                                </div>
                            </dl>
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn--race mt-2">Подробнее</a>
                        </div>
                    </article>
                @endforeach
                <a href="{{ route('events.index') }}" class="race-card race-card--all flex items-start justify-start w-full min-w-0 min-h-[200px] sm:h-[277px] sm:min-h-[277px] no-underline text-white overflow-hidden rounded-lg">
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
