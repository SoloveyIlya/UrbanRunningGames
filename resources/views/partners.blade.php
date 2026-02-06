@extends('layouts.app')

@section('title', 'Партнёры и спонсоры - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Партнёры и спонсоры</h1>
    </div>
</div>

<section class="partners-section">
    <div class="container">
        @if($partners->count() > 0)
            @foreach($partners as $level => $levelPartners)
                @if($level)
                    <h2>{{ ucfirst($level) }}</h2>
                @else
                    <h2>Партнёры</h2>
                @endif
                <div class="partners-grid">
                    @foreach($levelPartners as $partner)
                        <div class="partner-card">
                            @if($partner->logo_media_id)
                                <img src="#" alt="{{ $partner->name }}" class="partner-logo">
                            @endif
                            <h3>{{ $partner->name }}</h3>
                            @if($partner->description)
                                <p>{{ $partner->description }}</p>
                            @endif
                            @if($partner->website_url)
                                <a href="{{ $partner->website_url }}" target="_blank" rel="noopener" class="btn btn-sm">Сайт партнёра</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <p>Информация о партнёрах будет добавлена позже.</p>
            </div>
        @endif
    </div>
</section>
@endsection
