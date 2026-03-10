@props(['heroVideo' => null, 'title' => null, 'subtitle' => null, 'hideSubtitle' => false, 'heroOrnamentUrl' => null, 'heroOrnamentDesktopUrl' => null, 'heroOrnamentOpacity' => 0.82])

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
        @if($heroOrnamentUrl ?? null)
            <div class="hero__ornament hero__ornament--mobile" style="background-image: url('{{ e($heroOrnamentUrl) }}'); opacity: {{ is_numeric($heroOrnamentOpacity) ? $heroOrnamentOpacity : 0.85 }};"></div>
        @endif
        @if($heroOrnamentDesktopUrl ?? null)
            <div class="hero__ornament hero__ornament--desktop" style="background-image: url('{{ e($heroOrnamentDesktopUrl) }}'); opacity: {{ is_numeric($heroOrnamentOpacity) ? $heroOrnamentOpacity : 0.85 }};"></div>
        @endif
        <div class="hero__logo-wrap hero__logo-wrap--sticky" id="heroLogoWrap">
            <div class="hero__logo" aria-hidden="true">
                <img src="{{ asset('images/logo/sprut.svg') }}" alt="" width="320" height="104">
            </div>
        </div>
    @elseif($posterUrl)
        <div class="hero__poster" style="background-image: url('{{ $posterUrl }}');"></div>
        <div class="hero__overlay"></div>
        @if($heroOrnamentUrl ?? null)
            <div class="hero__ornament hero__ornament--mobile" style="background-image: url('{{ e($heroOrnamentUrl) }}'); opacity: {{ is_numeric($heroOrnamentOpacity) ? $heroOrnamentOpacity : 0.85 }};"></div>
        @endif
        @if($heroOrnamentDesktopUrl ?? null)
            <div class="hero__ornament hero__ornament--desktop" style="background-image: url('{{ e($heroOrnamentDesktopUrl) }}'); opacity: {{ is_numeric($heroOrnamentOpacity) ? $heroOrnamentOpacity : 0.85 }};"></div>
        @endif
        <div class="hero__logo" aria-hidden="true">
            <img src="{{ asset('images/logo/sprut.svg') }}" alt="" width="320" height="104">
        </div>
    @endif


</div>
