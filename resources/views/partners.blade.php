@extends('layouts.app')

@section('title', 'Партнёры и спонсоры - Urban Running Games')

@section('content')
@php
    $partnersList = collect();
    $sponsorsList = collect();
    foreach (['partner', 'partners', 'general', 'generalny', 'media', 'tech'] as $level) {
        if ($partners->has($level)) {
            $partnersList = $partnersList->merge($partners->get($level));
        }
    }
    foreach (['sponsor', 'sponsors'] as $level) {
        if ($partners->has($level)) {
            $sponsorsList = $sponsorsList->merge($partners->get($level));
        }
    }
@endphp

<div class="partners-page-figma">
    <div class="container">
        <nav class="partners-page-figma__breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Партнёры</span>
        </nav>
        <h1 class="partners-page-figma__title">{{ $partnersPageTitle ?? 'Партнёры и спонсоры' }}</h1>
        <p class="partners-page-figma__subtitle">{{ $partnersPageSubtitle ?? 'Компании и люди, которые делают Urban Running Games возможными' }}</p>
    </div>

    <div class="partners-page-figma__section-row">
        <section class="partners-page-figma__section">
            <h2 class="partners-page-figma__section-title">Партнёры</h2>
            <div class="partners-page-figma__grid">
                @forelse($partnersList as $partner)
                    <div class="partners-page-figma__card">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="partners-page-figma__card-logo">
                                @if($partner->logoMedia?->url)
                                    <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="235" height="92">
                                @else
                                    <span style="font-size: 1.25rem; color: rgba(255,255,255,0.5);">{{ mb_substr($partner->name, 0, 2) }}</span>
                                @endif
                            </a>
                        @else
                            <div class="partners-page-figma__card-logo">
                                @if($partner->logoMedia?->url)
                                    <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="235" height="92">
                                @else
                                    <span style="font-size: 1.25rem; color: rgba(255,255,255,0.5);">{{ mb_substr($partner->name, 0, 2) }}</span>
                                @endif
                            </div>
                        @endif
                        <p class="partners-page-figma__card-caption">Мы благодарим каждого партнёра за доверие</p>
                    </div>
                @empty
                    <p class="partners-page-figma__card-caption" style="grid-column: 1 / -1;">Скоро здесь появятся партнёры</p>
                @endforelse
            </div>
        </section>
        <section class="partners-page-figma__section">
            <h2 class="partners-page-figma__section-title">Спонсоры</h2>
            <div class="partners-page-figma__grid">
                @forelse($sponsorsList as $partner)
                    <div class="partners-page-figma__card">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="partners-page-figma__card-logo">
                                @if($partner->logoMedia?->url)
                                    <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="235" height="92">
                                @else
                                    <span style="font-size: 1.25rem; color: rgba(255,255,255,0.5);">{{ mb_substr($partner->name, 0, 2) }}</span>
                                @endif
                            </a>
                        @else
                            <div class="partners-page-figma__card-logo">
                                @if($partner->logoMedia?->url)
                                    <img src="{{ $partner->logoMedia->url }}" alt="{{ $partner->name }}" loading="lazy" width="235" height="92">
                                @else
                                    <span style="font-size: 1.25rem; color: rgba(255,255,255,0.5);">{{ mb_substr($partner->name, 0, 2) }}</span>
                                @endif
                            </div>
                        @endif
                        <p class="partners-page-figma__card-caption">Мы благодарим каждого партнёра за доверие</p>
                    </div>
                @empty
                    <p class="partners-page-figma__card-caption" style="grid-column: 1 / -1;">Скоро здесь появятся спонсоры</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="partners-page-figma__cta-box">
        <h2 class="partners-page-figma__cta-title">{{ $partnersCtaTitle ?? 'Хотите стать партнёром?' }}</h2>
        <form action="{{ route('contact.store') }}" method="POST" class="partners-page-figma__form">
            @csrf
            <input type="hidden" name="topic" value="partnership">
            <input type="text" name="full_name" class="partners-page-figma__input" placeholder="Как к вам обращаться?" value="{{ old('full_name') }}" required>
            @error('full_name')<span class="partners-page-figma__error">{{ $message }}</span>@enderror
            <input type="tel" name="phone" class="partners-page-figma__input" placeholder="+7 (000) 000-00-00" value="{{ old('phone') }}">
            <input type="email" name="email" class="partners-page-figma__input" placeholder="Электронная почта" value="{{ old('email') }}">
            <textarea name="message" class="partners-page-figma__textarea" placeholder="Комментарий" rows="4" required>{{ old('message') }}</textarea>
            @error('message')<span class="partners-page-figma__error">{{ $message }}</span>@enderror
            <x-turnstile-widget />
            <label class="partners-page-figma__consent">
                <input type="checkbox" name="consent" value="1" required {{ old('consent') ? 'checked' : '' }}>
                Я согласен с <a href="{{ route('legal.consent') }}" target="_blank">обработкой персональных данных</a> *
            </label>
            @error('consent')<span class="partners-page-figma__error">{{ $message }}</span>@enderror
            <button type="submit" class="partners-page-figma__submit">Отправить</button>
        </form>
    </section>
</div>
@endsection

@push('scripts')
    @if(config('turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endpush
