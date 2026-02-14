@extends('layouts.app')

@section('title', ($title ?? 'Правила забега') . ' - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $title ?? 'Правила забега' }}</h1>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="content">
            @if(!empty($content))
                {!! $content !!}
            @else
                <p>Здесь будут размещены правила и условия участия в забегах-играх.</p>
                <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
            @endif
        </div>
    </div>
</section>
@endsection
