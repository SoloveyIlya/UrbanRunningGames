@extends('layouts.app')

@section('title', ($title ?? 'Правила забега') . ' - Urban Running Games')

@section('content')
<div class="page-rules">
    <div class="page-header page-header--rules bg-[#111] text-gray-200 py-10 px-0">
        <div class="container max-w-[1200px] mx-auto px-5">
            <h1 class="text-3xl font-bold italic uppercase tracking-wide text-white m-0">{{ $title ?? 'Правила забега' }}</h1>
        </div>
    </div>

    <section class="rules-section py-12 md:py-16">
        <div class="container max-w-[1200px] mx-auto px-5">
            <div class="rules-card rounded-lg overflow-hidden bg-white/5 backdrop-blur-sm p-6 md:p-8">
                <div class="rules-card__content content">
                    @if(!empty($content))
                        {!! $content !!}
                    @else
                        <p>Здесь будут размещены правила и условия участия в забегах-играх.</p>
                        <p>Текст можно отредактировать в <a href="{{ url('/admin') }}">админ-панели</a> → Контент сайта.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
