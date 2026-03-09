@extends('layouts.app')

@section('title', 'Партнёры и спонсоры - Urban Running Games')

@section('content')
@php
    $levelLabels = [
        'general' => 'Генеральные партнёры',
        'generalny' => 'Генеральные партнёры',
        'partner' => 'Партнёры',
        'partners' => 'Партнёры',
        'sponsor' => 'Спонсоры',
        'sponsors' => 'Спонсоры',
        'media' => 'Информационные партнёры',
        'tech' => 'Технические партнёры',
    ];
@endphp

{{-- Hero — тёмный, асимметричный, без полноэкранного градиента (уникально для сайта) --}}
<header class="partners-hero relative overflow-hidden">
    <div class="partners-hero__blobs absolute inset-0 pointer-events-none" aria-hidden="true">
        <span class="partners-hero__blob partners-hero__blob--1"></span>
        <span class="partners-hero__blob partners-hero__blob--2"></span>
        <span class="partners-hero__blob partners-hero__blob--3"></span>
    </div>
    <div class="partners-hero__accent-line"></div>
    <div class="container partners-hero__container relative z-10 max-w-[1200px] mx-auto px-5 py-12 md:py-16">
        <div class="partners-hero__content text-center max-w-2xl mx-auto">
            <p class="partners-hero__label text-sm uppercase tracking-wider text-white/70 mb-1">Кто с нами</p>
            <h1 class="partners-hero__title text-4xl md:text-5xl font-bold text-white mb-2">Партнёры<br>и спонсоры</h1>
            <p class="partners-hero__sub text-lg text-white/85 m-0">Компании и люди, которые делают Urban Running Games возможными</p>
        </div>
    </div>
</header>

<div class="container max-w-[1200px] mx-auto px-5 py-8 md:py-12">
    {{-- Вводный блок — цитата с левой полосой, не как на других страницах --}}
    <section class="partners-intro">
        <blockquote class="partners-intro__quote">
            Мы благодарим каждого партнёра за доверие и поддержку. Вместе мы создаём незабываемые забеги-игры в вашем городе.
        </blockquote>
    </section>

    @if($partners->count() > 0)
        @foreach($partners as $level => $levelPartners)
            @php
                $sectionTitle = $levelLabels[$level ?? ''] ?? (is_string($level) && $level !== '' ? ucfirst($level) : 'Партнёры');
                $sectionNum = str_pad((string)($loop->iteration), 2, '0', STR_PAD_LEFT);
            @endphp
            <section class="partners-block" data-level="{{ $level ?? 'default' }}">
                <div class="partners-block__head">
                    <span class="partners-block__num" aria-hidden="true">{{ $sectionNum }}</span>
                    <h2 class="partners-block__title">{{ $sectionTitle }}</h2>
                </div>
                <ul class="partners-list">
                    @foreach($levelPartners as $partner)
                        <li class="partners-list__item">
                            @if($partner->website_url)
                                <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="partner-tile">
                                    <span class="partner-tile__logo">
                                        @if($partner->logoMedia?->url)
                                            <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="160" height="96">
                                        @else
                                            <span class="partner-tile__initials">{{ mb_substr($partner->name, 0, 2) }}</span>
                                        @endif
                                    </span>
                                    <span class="partner-tile__info">
                                        <span class="partner-tile__name">{{ $partner->name }}</span>
                                        @if($partner->description)
                                            <span class="partner-tile__desc">{{ $partner->description }}</span>
                                        @endif
                                        <span class="partner-tile__link">Сайт партнёра</span>
                                    </span>
                                </a>
                            @else
                                <div class="partner-tile partner-tile--static">
                                    <span class="partner-tile__logo">
                                        @if($partner->logoMedia?->url)
                                            <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="160" height="96">
                                        @else
                                            <span class="partner-tile__initials">{{ mb_substr($partner->name, 0, 2) }}</span>
                                        @endif
                                    </span>
                                    <span class="partner-tile__info">
                                        <span class="partner-tile__name">{{ $partner->name }}</span>
                                        @if($partner->description)
                                            <span class="partner-tile__desc">{{ $partner->description }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        {{-- CTA — полноширинная наклонная полоса, только на странице партнёров --}}
        <section class="partners-cta">
            <div class="partners-cta__strip">
                <div class="partners-cta__inner">
                    <h2 class="partners-cta__title">Хотите стать партнёром?</h2>
                    <p class="partners-cta__text">Расскажите о вашей компании — обсудим форматы сотрудничества.</p>
                    <a href="{{ route('contact') }}" class="partners-cta__btn">Написать нам</a>
                </div>
            </div>
        </section>
    @else
        <section class="partners-empty">
            <div class="partners-empty__shape" aria-hidden="true"></div>
            <h2 class="partners-empty__title">Скоро здесь появятся партнёры</h2>
            <p class="partners-empty__text">Ведём переговоры с компаниями. Хотите стать первым?</p>
            <a href="{{ route('contact') }}" class="partners-cta__btn">Связаться с нами</a>
        </section>
    @endif
</div>
@endsection
