@extends('layouts.app')

@section('title', 'Фото - Urban Running Games')

@section('content')
<div class="gallery-photo-page">
    {{-- Блок Фото: 1200×2154, фон #121315 --}}
    <div class="gallery-photo-page__inner">
        <nav class="shop-breadcrumb mb-0 gallery-photo-page__breadcrumb" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current">Фото</span>
        </nav>
        <h1 class="shop-title gallery-photo-page__title">Фото</h1>

        @if($events->count() > 0)
            <div class="gallery-photo-page__grid">
                @foreach($events as $event)
                    @foreach($event->albums as $album)
                        <a href="{{ route('gallery.show', $album) }}" class="gallery-photo-page__card">
                            <div class="gallery-photo-page__card-image">
                                @if($album->getCoverUrl())
                                    <img src="{{ $album->getCoverUrl() }}" alt="{{ $album->title }}" loading="lazy" decoding="async" width="320" height="321">
                                @else
                                    <span class="gallery-photo-page__card-placeholder">Фото</span>
                                @endif
                            </div>
                            <h3 class="gallery-photo-page__card-title">{{ $album->title }}</h3>
                            <p class="gallery-photo-page__card-date">{{ $event->starts_at->format('d.m.Y') }}</p>
                        </a>
                    @endforeach
                @endforeach
            </div>
        @else
            <div class="gallery-empty">
                <p>По выбранным фильтрам альбомов не найдено.</p>
            </div>
        @endif
    </div>
</div>
@endsection
