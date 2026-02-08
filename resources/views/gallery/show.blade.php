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

        @if($album->items->count() > 0)
            <div class="photos-grid">
                @foreach($album->items as $item)
                    <div class="photo-item">
                        @if($item->isImage())
                            <a href="{{ $item->url }}" target="_blank" rel="noopener" class="photo-link">
                                <img src="{{ $item->url }}" alt="" loading="lazy">
                            </a>
                        @else
                            <a href="{{ $item->url }}" target="_blank" rel="noopener" class="photo-link photo-link-file">
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
