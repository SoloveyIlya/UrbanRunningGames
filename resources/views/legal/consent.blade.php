@extends('layouts.app')

@section('title', ($title ?? 'Согласие на обработку данных') . ' - Urban Running Games')

@section('content')
<div class="page-rules">
    <div class="page-header page-header--rules">
        <div class="container">
            <h1>{{ $title ?? 'Согласие на обработку данных' }}</h1>
        </div>
    </div>

    <section class="rules-section">
        <div class="container">
            <div class="rules-card">
                <div class="rules-card__content content">
                    @if(!empty($content))
                        {!! $content !!}
                    @else
                        <p>Здесь будет размещено согласие на обработку персональных данных.</p>
                        <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
