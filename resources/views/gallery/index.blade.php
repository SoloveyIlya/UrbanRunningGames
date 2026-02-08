@extends('layouts.app')

@section('title', 'Фотогалерея - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Фотогалерея</h1>
        <p class="page-header-sub">Альбомы по событиям</p>
    </div>
</div>

<section class="gallery-section">
    <div class="container">
        @if($events->count() > 0)
            @foreach($events as $event)
                <div class="gallery-event-block">
                    <h2 class="gallery-event-title">
                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                    </h2>
                    <p class="gallery-event-meta">
                        {{ $event->starts_at->translatedFormat('d F Y') }}
                        @if($event->city)
                            · {{ $event->city->name }}
                        @endif
                    </p>
                    <div class="albums-grid">
                        @foreach($event->albums as $album)
                            <a href="{{ route('gallery.show', $album) }}" class="album-card">
                                <div class="album-cover">
                                    @if($album->getCoverUrl())
                                        <img src="{{ $album->getCoverUrl() }}" alt="{{ $album->title }}" loading="lazy" decoding="async" width="400" height="300">
                                    @else
                                        <div class="album-cover-placeholder">
                                            <span>Фото</span>
                                        </div>
                                    @endif
                                    @if($album->photos_count > 0)
                                        <span class="album-count">{{ $album->photos_count }}</span>
                                    @endif
                                </div>
                                <div class="album-info">
                                    <h3>{{ $album->title }}</h3>
                                    @if($album->description)
                                        <p>{{ \Illuminate\Support\Str::limit($album->description, 80) }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <p>Пока нет опубликованных фотоальбомов. Следите за обновлениями после событий!</p>
            </div>
        @endif
    </div>
</section>
@endsection
