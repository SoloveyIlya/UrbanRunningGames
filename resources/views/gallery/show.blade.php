@extends('layouts.app')

@section('title', $album->title . ' - Фотогалерея - Urban Running Games')

@section('content')
@php($galleryPhotos = $album->getGalleryPhotos())
<div class="album-page">
    <div class="album-photos">
        <nav class="shop-breadcrumb mb-0 gallery-photo-page__breadcrumb" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <a href="{{ route('gallery.index') }}">Фото</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            @if($album->event)
                <a href="{{ route('events.show', $album->event->slug) }}">{{ $album->event->title }}</a>
                <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            @endif
            <span class="shop-breadcrumb__current">{{ $album->title }}</span>
        </nav>
        <h1 class="shop-title gallery-photo-page__title album-photos__title">{{ $album->title }}</h1>

        @if($galleryPhotos->isNotEmpty())
            <div class="album-viewer" id="album-viewer" role="region" aria-label="Просмотр фотографий альбома">
                <div class="album-viewer__main-wrap">
                    <button type="button" class="album-viewer__nav album-viewer__nav--prev" aria-label="Предыдущее фото" data-dir="-1">←</button>
                    <div class="album-viewer__main" aria-live="polite" aria-atomic="true">
                        @foreach($galleryPhotos as $i => $photo)
                            @if($photo['is_image'])
                                <img src="{{ $photo['url'] }}" alt="{{ $album->title }} — фото {{ $i + 1 }}" class="album-viewer__main-img" data-index="{{ $i }}" width="660" height="440" {{ $i === 0 ? '' : 'style="display:none"' }}>
                            @endif
                        @endforeach
                    </div>
                    <button type="button" class="album-viewer__nav album-viewer__nav--next" aria-label="Следующее фото" data-dir="1">→</button>
                    <div class="album-viewer__thumbs" role="tablist" aria-label="Миниатюры фотографий">
                        @foreach($galleryPhotos as $i => $photo)
                            @if($photo['is_image'])
                                <button type="button" class="album-viewer__thumb {{ $i === 0 ? 'album-viewer__thumb--active' : '' }}" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Фото {{ $i + 1 }}" data-index="{{ $i }}" style="background-image: url('{{ $photo['thumb'] }}')"></button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <p>В альбоме пока нет фотографий.</p>
            </div>
        @endif
    </div>
</div>
@if($galleryPhotos->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    var viewer = document.getElementById('album-viewer');
    if (!viewer) return;
    var mainImgs = viewer.querySelectorAll('.album-viewer__main-img');
    var thumbs = viewer.querySelectorAll('.album-viewer__thumb');
    var n = mainImgs.length;
    if (n === 0) return;
    var idx = 0;

    function show(i) {
        var next = parseInt(i, 10);
        if (isNaN(next)) return;
        idx = ((next % n) + n) % n;
        mainImgs.forEach(function(el, k) { el.style.display = k === idx ? 'block' : 'none'; });
        thumbs.forEach(function(btn, k) {
            btn.classList.toggle('album-viewer__thumb--active', k === idx);
            btn.setAttribute('aria-selected', k === idx ? 'true' : 'false');
        });
    }

    viewer.querySelectorAll('.album-viewer__nav').forEach(function(btn) {
        btn.addEventListener('click', function() {
            show(idx + parseInt(this.getAttribute('data-dir'), 10));
        });
    });

    thumbs.forEach(function(btn, k) {
        btn.addEventListener('click', function() { show(k); });
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                show(k);
            }
        });
    });

    viewer.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            show(idx - 1);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            show(idx + 1);
        } else if (e.key === 'Home') {
            e.preventDefault();
            show(0);
        } else if (e.key === 'End') {
            e.preventDefault();
            show(n - 1);
        }
    });

    viewer.setAttribute('tabindex', '0');
});
</script>
@endif
@endsection
