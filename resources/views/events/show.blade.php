@extends('layouts.app')

@section('title', $event->title . ' - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $event->title }}</h1>
        <span class="event-status {{ $event->isUpcoming() ? 'upcoming' : 'past' }}">
            {{ $event->status_label }}
        </span>
    </div>
</div>

<section class="event-detail">
    <div class="container">
        <div class="event-main-info">
            <div class="info-block">
                <h3>Дата и время</h3>
                <p>{{ $event->starts_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            
            @if($event->city || $event->location_text)
                <div class="info-block">
                    <h3>Место проведения</h3>
                    <p>
                        @if($event->city)
                            {{ $event->city->name }}
                        @endif
                        @if($event->location_text)
                            @if($event->city), @endif
                            {{ $event->location_text }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if($event->description)
            <div class="content-block">
                <h2>Описание формата</h2>
                <div class="content">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>
        @endif

        @if($event->rules)
            <div class="content-block">
                <h2>Правила и требования</h2>
                <div class="content">
                    {!! nl2br(e($event->rules)) !!}
                </div>
            </div>
        @endif

        @if($event->partners->count() > 0)
            <div class="content-block">
                <h2>Партнёры события</h2>
                <div class="partners-list">
                    @foreach($event->partners as $partner)
                        <div class="partner-item">
                            @if($partner->logo_media_id)
                                <img src="#" alt="{{ $partner->name }}" class="partner-logo">
                            @endif
                            <h4>{{ $partner->name }}</h4>
                            @if($partner->description)
                                <p>{{ $partner->description }}</p>
                            @endif
                            @if($partner->website_url)
                                <a href="{{ $partner->website_url }}" target="_blank" rel="noopener">Сайт партнёра</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($event->albums->count() > 0)
            <div class="content-block">
                <h2>Фотогалерея</h2>
                <div class="albums-grid">
                    @foreach($event->albums as $album)
                        <div class="album-card">
                            <h4>{{ $album->title }}</h4>
                            @if($album->description)
                                <p>{{ $album->description }}</p>
                            @endif
                            <a href="#" class="btn btn-sm">Смотреть альбом</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="event-actions">
            <a href="{{ route('events.index') }}" class="btn">← Вернуться к событиям</a>
        </div>
    </div>
</section>
@endsection
