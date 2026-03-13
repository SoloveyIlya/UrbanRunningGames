@extends('layouts.app')

@section('title', ($title ?? 'О команде организатора') . ' - Urban Running Games')

@section('content')
{{-- Макет по Figma: хлебные крошки, заголовок, подзаголовок, миссия, блок «О нас» с видео и текстом, карусель, соцсети, футер-полоса --}}
<div class="team-page-figma">
    <div class="container">
        <nav class="team-page-figma__breadcrumb shop-breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Команда</span>
        </nav>
        <h1 class="team-page-figma__title">{{ $hero_title ?? 'Команда' }}</h1>
        <p class="team-page-figma__subtitle">{{ $hero_subtitle ?? 'Люди, которые делают Urban Running Games — забеги-игры в вашем городе' }}</p>
    </div>

    <div class="container">
        {{-- Наша миссия --}}
        <section class="team-page-figma__mission" aria-labelledby="team-mission-heading">
            <h2 id="team-mission-heading" class="team-page-figma__mission-title">{{ $mission_title ?? 'Наша миссия' }}</h2>
            <div class="team-page-figma__mission-text">
                {!! $mission_content ?? '' !!}
            </div>
        </section>

        {{-- Две колонки: видео слева, «О нас» справа --}}
        <div class="team-page-figma__row">
            <div class="team-page-figma__video-col">
                @if(!empty($hero_background_url))
                    <img src="{{ $hero_background_url }}" alt="" class="team-page-figma__video" width="490" height="340" loading="lazy">
                @else
                    <div class="team-page-figma__video" role="img" aria-hidden="true"></div>
                @endif
            </div>
            <div class="team-page-figma__about-col">
                <h2 class="team-page-figma__about-title">О нас</h2>
                <div class="team-page-figma__about-text">
                    @if(!empty($content))
                        {!! $content !!}
                    @else
                        <p>SPRUT — это городские забеги-игры: вы бежите по маршруту, выполняете задания на точках, соревнуетесь командами и получаете заряд эмоций и движения. Мы придумываем форматы, прокладываем маршруты, договариваемся с локациями и делаем так, чтобы каждый забег был по-настоящему интересным. Присоединяйтесь к гонкам в вашем городе, следите за анонсами и пишите нам в <a href="{{ route('contact') }}">контакты</a>, если хотите стать партнёром или помочь в организации.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Большое видео + карусель (как album-viewer__main-wrap); на мобильном — свайп, макс. 5 миниатюр по центру --}}
        <section class="team-page-figma__carousel-section" id="team-carousel" aria-label="Команда">
            <button type="button" class="team-page-figma__carousel-btn team-page-figma__carousel-btn--prev" aria-label="Назад">→</button>
            <div class="team-page-figma__video-wrap" id="team-carousel-track">
                @if(!empty($team_members) && isset($team_members[0]['photo_url']))
                    <img src="{{ $team_members[0]['photo_url'] }}" alt="" class="team-page-figma__video-large" id="team-carousel-main" width="660" height="440" loading="lazy">
                @else
                    <div class="team-page-figma__video-large" id="team-carousel-main" aria-hidden="true"></div>
                @endif
            </div>
            <button type="button" class="team-page-figma__carousel-btn team-page-figma__carousel-btn--next" aria-label="Вперёд">→</button>
            <div class="team-page-figma__thumbs-wrap">
                <div class="team-page-figma__thumbs" id="team-carousel-thumbs">
                    @forelse($team_members ?? [] as $index => $member)
                        @if(!empty($member['photo_url']))
                            <button type="button" class="team-page-figma__thumb {{ $index === 0 ? 'team-page-figma__thumb--active' : '' }}" aria-label="Фото {{ $index + 1 }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" data-index="{{ $index }}" data-src="{{ $member['photo_url'] }}" style="background-image: url('{{ addslashes($member['photo_url']) }}')"></button>
                        @else
                            <button type="button" class="team-page-figma__thumb {{ $index === 0 ? 'team-page-figma__thumb--active' : '' }}" aria-label="Фото {{ $index + 1 }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" data-index="{{ $index }}" data-src=""></button>
                        @endif
                    @empty
                        <button type="button" class="team-page-figma__thumb team-page-figma__thumb--active" aria-selected="true" data-index="0" data-src=""></button>
                    @endforelse
                </div>
            </div>
        </section>

    </div>
</div>

@push('scripts')
<script>
(function() {
    var section = document.getElementById('team-carousel');
    if (!section) return;
    var track = document.getElementById('team-carousel-track');
    var mainEl = document.getElementById('team-carousel-main');
    var thumbsContainer = document.getElementById('team-carousel-thumbs');
    if (!track || !mainEl || !thumbsContainer) return;
    var thumbs = thumbsContainer.querySelectorAll('.team-page-figma__thumb');
    var prevBtn = section.querySelector('.team-page-figma__carousel-btn--prev');
    var nextBtn = section.querySelector('.team-page-figma__carousel-btn--next');
    var currentIndex = 0;
    var isTouch = 'ontouchstart' in window;

    function goTo(index) {
        var n = thumbs.length;
        if (n === 0) return;
        index = (index % n + n) % n;
        currentIndex = index;
        thumbs.forEach(function(t, k) {
            var active = k === index;
            t.classList.toggle('team-page-figma__thumb--active', active);
            t.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        var src = thumbs[index].getAttribute('data-src');
        if (mainEl.tagName === 'IMG' && src) {
            mainEl.src = src;
        }
        scrollThumbsToActive();
    }

    function scrollThumbsToActive() {
        if (!thumbs.length || !thumbsContainer.parentElement) return;
        var wrap = thumbsContainer.parentElement;
        var thumb = thumbs[currentIndex];
        var wrapWidth = wrap.offsetWidth;
        var thumbWidth = thumb.offsetWidth;
        var scrollLeft = thumb.offsetLeft - (wrapWidth / 2) + (thumbWidth / 2);
        var maxScroll = wrap.scrollWidth - wrap.offsetWidth;
        wrap.scrollTo({ left: Math.max(0, Math.min(scrollLeft, maxScroll)), behavior: 'smooth' });
    }

    thumbs.forEach(function(thumb, index) {
        thumb.addEventListener('click', function() { goTo(index); });
    });
    if (prevBtn) prevBtn.addEventListener('click', function() { goTo(currentIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { goTo(currentIndex + 1); });

    if (isTouch && track) {
        var startX = 0;
        track.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        }, { passive: true });
        track.addEventListener('touchend', function(e) {
            var endX = e.changedTouches[0].clientX;
            var delta = startX - endX;
            if (Math.abs(delta) > 50) {
                goTo(currentIndex + (delta > 0 ? 1 : -1));
            }
        }, { passive: true });
    }

    window.addEventListener('resize', scrollThumbsToActive);
})();
</script>
@endpush
@endsection
