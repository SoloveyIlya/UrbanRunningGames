@extends('layouts.app')

@section('title', 'Архив событий - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Архив событий</h1>
    </div>
</div>

<section class="archive-section">
    <div class="container">
        @if($events->count() > 0)
            @foreach($events as $year => $yearEvents)
                <div class="archive-year">
                    <h2>{{ $year }} год</h2>
                    <div class="events-grid">
                        @foreach($yearEvents as $event)
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
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <p>Архив событий пока пуст.</p>
            </div>
        @endif
    </div>
</section>
@endsection
