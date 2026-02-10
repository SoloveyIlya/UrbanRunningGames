@extends('layouts.app')

@section('title', $album->title . ' - Фотогалерея - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="{{ route('gallery.index') }}">Фотогалерея</a>
            <span class="breadcrumb-sep">/</span>
            @if($album->event)
                <a href="{{ route('events.show', $album->event->slug) }}">{{ $album->event->title }}</a>
                <span class="breadcrumb-sep">/</span>
            @endif
            <span>{{ $album->title }}</span>
        </nav>
        <h1>{{ $album->title }}</h1>
        @if($album->event)
            <p class="page-header-sub">
                <a href="{{ route('events.show', $album->event->slug) }}">{{ $album->event->title }}</a>
                · {{ $album->event->starts_at->translatedFormat('d F Y') }}
            </p>
        @endif
    </div>
</div>

<section class="album-section">
    <div class="container">
        @if($album->description)
            <div class="album-description">
                {!! nl2br(e($album->description)) !!}
            </div>
        @endif

        @php($galleryPhotos = $album->getGalleryPhotos())
        @if($galleryPhotos->isNotEmpty())
            <div class="photos-grid">
                @foreach($galleryPhotos as $photo)
                    <div class="photo-item">
                        @if($photo['is_image'])
                            <a href="{{ $photo['url'] }}" target="_blank" rel="noopener" class="photo-link">
                                <img src="{{ $photo['thumb'] }}" alt="{{ $album->title }} - Фото {{ $loop->iteration }}" loading="lazy" decoding="async" data-full="{{ $photo['url'] }}" width="400" height="400">
                            </a>
                        @else
                            <a href="{{ $photo['url'] }}" target="_blank" rel="noopener" class="photo-link photo-link-file">
                                Скачать файл
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>В альбоме пока нет фотографий.</p>
            </div>
        @endif

        <div class="album-actions">
            @if($album->event)
                <a href="{{ route('events.show', $album->event->slug) }}" class="btn btn-secondary">← К событию</a>
            @endif
            <a href="{{ route('gallery.index') }}" class="btn">← Все альбомы</a>
        </div>
    </div>
</section>
@endsection
