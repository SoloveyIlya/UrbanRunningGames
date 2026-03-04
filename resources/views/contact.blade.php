@extends('layouts.app')

@section('title', 'Контакты - Urban Running Games')

@section('content')
<div class="contact-page">
    {{-- Hero в стиле партнёров: тёмный, с блобами и акцентной полосой --}}
    <header class="contact-hero">
        <div class="contact-hero__blobs" aria-hidden="true">
            <span class="contact-hero__blob contact-hero__blob--1"></span>
            <span class="contact-hero__blob contact-hero__blob--2"></span>
            <span class="contact-hero__blob contact-hero__blob--3"></span>
        </div>
        <div class="contact-hero__accent-line"></div>
        <div class="container contact-hero__container">
            <div class="contact-hero__content">
                <p class="contact-hero__label">Связь</p>
                <h1 class="contact-hero__title">Контакты</h1>
                <p class="contact-hero__sub">Вопросы, партнёрство, забеги — напишите нам, мы ответим</p>
            </div>
        </div>
    </header>

    <div class="container">
        {{-- Вводный блок — цитата с левой полосой, как на странице партнёров --}}
        <section class="contact-intro">
            <blockquote class="contact-intro__quote">
                Мы на связи в будни и в дни мероприятий. Напишите по почте или в мессенджер — подберём удобный формат общения.
            </blockquote>
        </section>

        <div class="contact-page__content">
            <aside class="contact-page__info">
                <div class="contact-page__card">
                    <h2 class="contact-page__card-title">Контактная информация</h2>
                    <ul class="contact-page__details">
                        <li>
                            <span class="contact-page__detail-label">Email</span>
                            <a href="mailto:{{ e($siteContact['email'] ?? 'main@sprut.run') }}" class="contact-page__detail-value">{{ e($siteContact['email'] ?? 'main@sprut.run') }}</a>
                        </li>
                        <li>
                            <span class="contact-page__detail-label">Телефон</span>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteContact['phone'] ?? '') }}" class="contact-page__detail-value">{{ e($siteContact['phone'] ?? '+7 (917) 806-09-95') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="contact-page__card">
                    <h2 class="contact-page__card-title">Режим работы</h2>
                    <p class="contact-page__schedule">{{ $siteContact['schedule_weekdays'] ?? 'Понедельник–пятница — 9:00–18:00' }}</p>
                    <p class="contact-page__schedule">{{ $siteContact['schedule_events'] ?? 'В дни мероприятий — 6:00–0:00' }}</p>
                    <p class="contact-page__schedule-note">{{ $siteContact['schedule_note'] ?? 'Отвечаем в Telegram' }}</p>
                </div>
                <div class="contact-page__card contact-page__card--social">
                    <h2 class="contact-page__card-title">Мы в соцсетях</h2>
                    <div class="contact-page__social">
                        <a href="{{ $siteContact['telegram_url'] ?? 'https://t.me/urbanrunninggames' }}" target="_blank" rel="noopener" class="contact-page__social-link contact-page__social-link--tg" aria-label="Telegram">
                            <svg class="contact-page__social-icon" width="30" height="30" aria-hidden="true"><use href="#icon-footer-telegram"/></svg>
                        </a>
                        <a href="{{ $siteContact['vk_url'] ?? 'https://vk.com/urbanrunninggames' }}" target="_blank" rel="noopener" class="contact-page__social-link contact-page__social-link--vk" aria-label="VK">
                            <svg class="contact-page__social-icon" width="30" height="30" aria-hidden="true"><use href="#icon-footer-vk"/></svg>
                        </a>
                        @if(!empty($siteContact['rutube_url']) && $siteContact['rutube_url'] !== '#')
                            <a href="{{ $siteContact['rutube_url'] }}" target="_blank" rel="noopener" class="contact-page__social-link contact-page__social-link--r" aria-label="RuTube">
                                <svg class="contact-page__social-icon" width="30" height="30" aria-hidden="true"><use href="#icon-footer-rutube"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>

            <div class="contact-page__form-wrap">
                <div class="contact-page__card contact-page__card--form">
                    <h2 class="contact-page__card-title">Написать нам</h2>
                    <p class="contact-page__form-intro">Заполните форму — мы ответим в ближайшее время.</p>
                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="contact-form__group">
                            <label for="full_name" class="contact-form__label">Ваше имя *</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" class="contact-form__input" required>
                            @error('full_name')
                                <span class="contact-form__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="contact-form__group">
                            <label for="topic" class="contact-form__label">Тема обращения *</label>
                            <select id="topic" name="topic" class="contact-form__input contact-form__select" required>
                                <option value="">Выберите тему</option>
                                <option value="participation" {{ old('topic') == 'participation' ? 'selected' : '' }}>Участие в забеге</option>
                                <option value="merch" {{ old('topic') == 'merch' ? 'selected' : '' }}>Мерч</option>
                                <option value="partnership" {{ old('topic') == 'partnership' ? 'selected' : '' }}>Партнёрство</option>
                                <option value="other" {{ old('topic') == 'other' ? 'selected' : '' }}>Другое</option>
                            </select>
                            @error('topic')
                                <span class="contact-form__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="contact-form__row">
                            <div class="contact-form__group">
                                <label for="phone" class="contact-form__label">Телефон</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="contact-form__input">
                                @error('phone')
                                    <span class="contact-form__error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="contact-form__group">
                                <label for="email" class="contact-form__label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="contact-form__input">
                                @error('email')
                                    <span class="contact-form__error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="contact-form__group">
                            <label for="message" class="contact-form__label">Сообщение *</label>
                            <textarea id="message" name="message" rows="5" class="contact-form__input contact-form__textarea" required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="contact-form__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-turnstile-widget />
                        <div class="contact-form__group contact-form__group--consent">
                            <label class="contact-form__checkbox-label">
                                <input type="checkbox" name="consent" value="1" required {{ old('consent') ? 'checked' : '' }} class="contact-form__checkbox">
                                Я согласен с <a href="{{ route('legal.consent') }}" target="_blank">обработкой персональных данных</a> *
                            </label>
                            @error('consent')
                                <span class="contact-form__error">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="contact-form__submit">Отправить сообщение</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if(config('turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endpush
