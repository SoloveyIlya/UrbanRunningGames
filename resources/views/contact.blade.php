@extends('layouts.app')

@section('title', 'Контакты - Urban Running Games')

@section('content')
<div class="contact-page">
    <div class="container pt-8 md:pt-12">
        <nav class="shop-breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Контакты</span>
        </nav>

        <h1 class="shop-title">{{ $contactPageTitle ?? 'Контакты' }}</h1>

        <p class="shop-subtitle">{{ $contactPageSubtitle ?? 'Вопросы, партнёрство, забеги — напишите нам, мы ответим.' }}</p>
    </div>

    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5 py-8 md:py-12">
        {{-- Блок как на макете: серый контейнер с размытием + три карточки в ряд --}}
        <div class="contact-page__content">
            <div class="contact-page__cards">
                <div class="contact-page__card contact-page__card--contact">
                    <span class="contact-page__card-label">Email</span>
                    <a href="mailto:{{ e($siteContact['email'] ?? 'main@sprut.run') }}" class="contact-page__card-value">{{ e($siteContact['email'] ?? 'main@sprut.run') }}</a>
                    <span class="contact-page__card-label">Телефон</span>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteContact['phone'] ?? '') }}" class="contact-page__card-value">{{ e($siteContact['phone'] ?? '+7(917)806-09-95') }}</a>
                </div>
                <div class="contact-page__card contact-page__card--schedule">
                    <p class="contact-page__schedule">{!! str_replace(' — ', '<br>', e($siteContact['schedule_weekdays'] ?? 'Понедельник–пятница — 9:00–18:00')) !!}</p>
                    <p class="contact-page__schedule">{!! str_replace(' — ', '<br>', e($siteContact['schedule_events'] ?? 'В дни мероприятий — 6:00–0:00')) !!}</p>
                    <p class="contact-page__schedule-note">Отвечаем в <a href="{{ $siteContact['telegram_url'] ?? 'https://t.me/urbanrunninggames' }}" target="_blank" rel="noopener" class="contact-page__card-value">Telegram</a></p>
                </div>
                <div class="contact-page__card contact-page__card--social">
                    <span class="contact-page__card-label">Соц. сети</span>
                    <div class="contact-page__social">
                        <a href="{{ $siteContact['vk_url'] ?? 'https://vk.com/urbanrunninggames' }}" target="_blank" rel="noopener" class="contact-page__social-link" aria-label="VK">
                            <svg class="contact-page__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-vk"/></svg>
                        </a>
                        <a href="{{ $siteContact['telegram_url'] ?? 'https://t.me/urbanrunninggames' }}" target="_blank" rel="noopener" class="contact-page__social-link" aria-label="Telegram">
                            <svg class="contact-page__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-telegram"/></svg>
                        </a>
                        @if(!empty($siteContact['rutube_url']) && $siteContact['rutube_url'] !== '#')
                            <a href="{{ $siteContact['rutube_url'] }}" target="_blank" rel="noopener" class="contact-page__social-link" aria-label="RuTube">
                                <svg class="contact-page__social-icon" width="24" height="24" aria-hidden="true"><use href="#icon-nav-rutube"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
