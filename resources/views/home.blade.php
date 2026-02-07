@extends('layouts.app')

@section('title', 'Главная - Urban Running Games')

@section('content')
<div class="hero">
    <div class="container">
        <h1>Urban Running Games</h1>
        <p class="hero-subtitle">Командные забеги-игры в городской среде</p>
        <div class="hero-actions">
            <a href="{{ route('events.index') }}" class="btn btn-primary">Предстоящие события</a>
            <a href="{{ route('about') }}" class="btn btn-secondary">О команде</a>
        </div>
    </div>
</div>

<section class="upcoming-events">
    <div class="container">
        <h2>Ближайшие события</h2>
        @if($upcomingEvents->count() > 0)
            <div class="events-grid">
                @foreach($upcomingEvents as $event)
                    <div class="event-card">
                        <div class="event-date">
                            <span class="day">{{ $event->starts_at->format('d') }}</span>
                            <span class="month">{{ $event->starts_at->translatedFormat('M') }}</span>
                        </div>
                        <div class="event-info">
                            <h3>{{ $event->title }}</h3>
                            <p class="event-location">
                                @if($event->city)
                                    {{ $event->city->name }}
                                @endif
                                @if($event->location_text)
                                    , {{ $event->location_text }}
                                @endif
                            </p>
                            @if($event->description)
                                <p class="event-description">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                            @endif
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm">Подробнее</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>Пока нет предстоящих событий. Следите за обновлениями!</p>
        @endif
        <div class="text-center mt-4">
            <a href="{{ route('events.index') }}" class="btn">Все события</a>
        </div>
    </div>
</section>

@if($partners->count() > 0)
<section class="partners">
    <div class="container">
        <h2>Наши партнёры</h2>
        <div class="partners-grid">
            @foreach($partners as $partner)
                <div class="partner-item">
                    @if($partner->logo_media_id)
                        <img src="#" alt="{{ $partner->name }}" class="partner-logo">
                    @endif
                    <h4>{{ $partner->name }}</h4>
                    @if($partner->description)
                        <p>{{ $partner->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('partners') }}" class="btn">Все партнёры</a>
        </div>
    </div>
</section>
@endif
@endsection
