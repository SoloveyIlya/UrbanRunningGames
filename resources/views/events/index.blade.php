@extends('layouts.app')

@section('title', 'События - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>События</h1>
    </div>
</div>

<section class="events-section">
    <div class="container">
        @if($events->count() > 0)
            <div class="events-grid">
                @foreach($events as $event)
                    <div class="event-card">
                        <div class="event-date">
                            <span class="day">{{ $event->starts_at->format('d') }}</span>
                            <span class="month">{{ $event->starts_at->translatedFormat('M') }}</span>
                            <span class="year">{{ $event->starts_at->format('Y') }}</span>
                        </div>
                        <div class="event-info">
                            <span class="event-status {{ $event->isUpcoming() ? 'upcoming' : 'past' }}">
                                {{ $event->status_label }}
                            </span>
                            <h3>{{ $event->title }}</h3>
                            <p class="event-location">
                                @if($event->city)
                                    <strong>Город:</strong> {{ $event->city->name }}<br>
                                @endif
                                @if($event->location_text)
                                    <strong>Место:</strong> {{ $event->location_text }}
                                @endif
                            </p>
                            @if($event->description)
                                <p class="event-description">{{ \Illuminate\Support\Str::limit($event->description, 150) }}</p>
                            @endif
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm">Подробнее</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>Пока нет опубликованных событий. Следите за обновлениями!</p>
            </div>
        @endif
    </div>
</section>
@endsection
