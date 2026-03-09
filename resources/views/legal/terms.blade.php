@extends('layouts.app')

@section('title', ($title ?? 'Условия продажи мерча') . ' - Urban Running Games')

@section('content')
<div class="page-rules">
    <div class="page-header page-header--rules">
        <div class="container">
            <h1>{{ $title ?? 'Условия продажи мерча' }}</h1>
        </div>
    </div>

    <section class="rules-section">
        <div class="container">
            <div class="rules-card">
                <div class="rules-card__content content">
                    @if(!empty($content))
                        {!! $content !!}
                    @else
                        <p>Здесь будут размещены условия продажи мерча.</p>
                        <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
