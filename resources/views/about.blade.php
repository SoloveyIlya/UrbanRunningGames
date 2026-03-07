@extends('layouts.app')

@section('title', ($title ?? 'О команде организатора') . ' - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $title ?? 'О команде организатора' }}</h1>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="content">
            @if(!empty($content))
                {!! $content !!}
            @else
                <p>Здесь будет размещена информация о команде организатора Urban Running Games.</p>
                <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
            @endif
        </div>
    </div>
</section>
@endsection
