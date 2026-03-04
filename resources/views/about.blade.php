@extends('layouts.app')

@section('title', ($title ?? 'О команде организатора') . ' - Urban Running Games')

@section('content')
@php
    $heroBgStyle = !empty($hero_background_url)
        ? 'background-image: url(' . e($hero_background_url) . '); background-size: cover; background-position: center;'
        : '';
@endphp
{{-- Hero — заголовок и подзаголовок из админки; фон и оверлей настраиваются в Hero-контент → Hero страницы «О команде» --}}
<div class="hero about-hero" @if($heroBgStyle) style="{{ $heroBgStyle }}" @endif>
    <div class="about-hero__overlay" style="opacity: {{ is_numeric($hero_overlay_opacity ?? '') ? (float) $hero_overlay_opacity : 0.35 }};"></div>
    <div class="about-hero__content">
        <div class="container">
            <h1 class="about-hero__title">{{ $hero_title ?? 'Команда' }}</h1>
            <p class="about-hero__sub">{{ $hero_subtitle ?? 'Люди, которые делают Urban Running Games — забеги-игры в вашем городе' }}</p>
        </div>
    </div>
</div>

<section class="about-mission" aria-labelledby="about-mission-heading">
    <div class="about-mission__bg" aria-hidden="true"></div>
    <div class="about-mission__wrap">
        <h2 id="about-mission-heading" class="visually-hidden">Наша миссия</h2>
        <div class="about-mission__inner">
            <h3 class="about-mission__title">{{ $mission_title ?? 'Наша миссия' }}</h3>
            <div class="about-mission__text">
                {!! $mission_content ?? '' !!}
            </div>
        </div>
    </div>
</section>

<div class="container">
    {{-- Вводный текст из админки --}}
    @if(!empty($content))
        <section class="about-intro">
            <h2 class="about-intro__title">О нас</h2>
            <div class="about-intro__text">
                {!! $content !!}
            </div>
        </section>
    @else
        <section class="about-intro">
            <h2 class="about-intro__title">О нас</h2>
            <div class="about-intro__text">
                <p>Мы — команда организаторов городских забегов-игр. Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта → О команде.</p>
            </div>
        </section>
    @endif

    <h2 class="about-section-title">Наша команда</h2>

    {{-- Аккордеон: в заголовке — должность, по клику — данные сотрудника (как секция «Информация» на главной) --}}
    <section class="about-team info-section">
        <div class="info-accordion about-team-accordion">
            @foreach($team_members ?? [] as $index => $member)
                @php
                    $id = 'about-team-' . ($index + 1);
                    $btnId = 'about-team-btn-' . ($index + 1);
                @endphp
                <div class="info-accordion__item" data-accordion-item>
                    <button type="button" class="info-accordion__header" aria-expanded="false" aria-controls="{{ $id }}" id="{{ $btnId }}" data-accordion-trigger>
                        <span class="info-accordion__title-text">{{ $member['role'] ?? 'Должность' }}</span>
                        <span class="info-accordion__icon" aria-hidden="true">+</span>
                    </button>
                    <div id="{{ $id }}" class="info-accordion__body" role="region" aria-labelledby="{{ $btnId }}" hidden>
                        <div class="info-accordion__content">
                            <div class="about-team-member-card">
                                <div class="about-team-member-card__photo">
                                    @if(!empty($member['photo_url']))
                                        <img src="{{ $member['photo_url'] }}" alt="{{ $member['name'] ?? '' }}" loading="lazy" width="400" height="300">
                                    @else
                                        <span aria-hidden="true">👤</span>
                                    @endif
                                </div>
                                <div class="about-team-member-card__body">
                                    <h3 class="about-team-member-card__name">{{ $member['name'] ?? '' }}</h3>
                                    <p class="about-team-member-card__role">{{ $member['role'] ?? '' }}</p>
                                    @if(!empty($member['description']))
                                        <p class="about-team-member-card__desc">{{ $member['description'] }}</p>
                                    @endif
                                    @if(!empty($member['experience']))
                                        <p class="about-team-member-card__exp">{{ $member['experience'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
