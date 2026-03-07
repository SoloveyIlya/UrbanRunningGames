@props(['heroVideo' => null, 'title' => null, 'subtitle' => null, 'hideSubtitle' => false])

@php
    $useVideo = $heroVideo && $heroVideo->is_enabled && $heroVideo->video_url;
    $usePoster = $heroVideo && ($heroVideo->poster_url || $heroVideo->video_url);
    $posterUrl = $heroVideo?->poster_url;
    $videoUrl = $heroVideo?->video_url;
    $heroTitle = $title ?? $heroVideo?->title;
    $buttonText = $heroVideo?->button_text;
    $buttonUrl = $heroVideo?->button_url;
@endphp

<div class="hero {{ $useVideo ? 'hero--with-video' : '' }}">
    @if($useVideo)
        <video
            class="hero__video"
            autoplay
            muted
            loop
            playsinline
            @if($posterUrl) poster="{{ $posterUrl }}" @endif
        >
            <source src="{{ $videoUrl }}" type="{{ $heroVideo->videoMedia->mime_type ?? 'video/mp4' }}">
        </video>
        <div class="hero__overlay"></div>
    @elseif($posterUrl)
        <div class="hero__poster" style="background-image: url('{{ $posterUrl }}');"></div>
        <div class="hero__overlay"></div>
    @endif

    <div class="hero__content container">
        <h1>{{ $heroTitle ?? 'Urban Running Games' }}</h1>
        @if(!$hideSubtitle)
            <p class="hero-subtitle">{{ $subtitle ?? 'Командные забеги-игры в городской среде' }}</p>
        @endif
        <div class="hero-actions">
            @if(trim($slot ?? '') !== '')
                {{ $slot }}
            @elseif($buttonText && $buttonUrl)
                <a href="{{ $buttonUrl }}" class="btn btn-primary">{{ $buttonText }}</a>
            @endif
        </div>
    </div>
</div>
