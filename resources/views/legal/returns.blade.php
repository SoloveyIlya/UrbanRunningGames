@extends('layouts.app')

@section('title', ($title ?? 'Возврат и обмен') . ' - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ $title ?? 'Возврат и обмен' }}</h1>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="legal-content">
            @if(!empty($content))
                {!! $content !!}
            @else
                <p>Здесь будут размещены правила возвратов товаров.</p>
                <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
            @endif
        </div>
    </div>
</section>
@endsection
