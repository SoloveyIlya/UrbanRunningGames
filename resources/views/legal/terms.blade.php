@extends('layouts.app')

@section('title', ($title ?? 'Условия продажи мерча') . ' - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $title ?? 'Условия продажи мерча' }}</h1>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="legal-content">
            @if(!empty($content))
                {!! $content !!}
            @else
                <p>Здесь будут размещены условия продажи мерча.</p>
                <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
            @endif
        </div>
    </div>
</section>
@endsection
