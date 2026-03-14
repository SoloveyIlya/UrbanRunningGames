@extends('layouts.app')

@section('title', 'Сводный рейтинг - Urban Running Games')

@section('content')
<div class="page-rating">
<section class="page-hero relative min-h-[38vh] flex items-center justify-center text-center bg-gradient-to-br from-[#1a1525] via-[#0c0c10] to-[#1a1525] text-white">
    <div class="hero__overlay absolute inset-0 pointer-events-none"></div>
    <div class="container relative z-10 max-w-[1200px] mx-auto px-4 sm:px-5 py-12">
        <h1 class="text-3xl md:text-4xl font-bold italic uppercase tracking-wide text-white mb-2">{{ $ratingPageTitle ?? 'Сводный рейтинг' }}</h1>
        <p class="page-hero__sub text-lg text-white/85 m-0">{{ $ratingPageSubtitle ?? 'Рейтинг команд по результатам всех проведённых забегов-игр' }}</p>
    </div>
</section>

<section class="rating-section py-12 md:py-16 text-gray-100">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <div class="rating-info"> 
        </div>

        @if($entries->isNotEmpty())
            @php
                $topThree = $entries->take(3);
                $rest = $entries->slice(3);
            @endphp

            @if($topThree->isNotEmpty())
                <div class="rating-podium" aria-label="Топ-3 команд">
                    <h2 class="rating-podium__title">Топ-3</h2>
                    <div class="rating-podium__list">
                        @foreach([1 => 2, 0 => 1, 2 => 3] as $idx => $place)
                            @if(isset($topThree[$idx]))
                                @php $entry = $topThree[$idx]; @endphp
                                <div class="rating-podium__card rating-podium__card--{{ $place }}">
                                    <div class="rating-podium__place" aria-hidden="true">
                                        @if($place === 1)
                                            <span class="rating-podium__medal rating-podium__medal--gold">1</span>
                                        @elseif($place === 2)
                                            <span class="rating-podium__medal rating-podium__medal--silver">2</span>
                                        @else
                                            <span class="rating-podium__medal rating-podium__medal--bronze">3</span>
                                        @endif
                                    </div>
                                    <h3 class="rating-podium__team">{{ $entry->team_name }}</h3>
                                    @if($entry->team_type)
                                        <span class="rating-podium__type">{{ $entry->team_type_label }}</span>
                                    @endif
                                    <dl class="rating-podium__stats">
                                        <div><dt>Очки</dt><dd>{{ $entry->points }}</dd></div>
                                        <div><dt>Событий</dt><dd>{{ $entry->events_count }}</dd></div>
                                    </dl>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rating-table-wrapper">
                @if($rest->isNotEmpty())
                    <h2 class="rating-table-title">Остальные места</h2>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>Место</th>
                                <th>Название команды</th>
                                <th>Участники</th>
                                <th>Очки</th>
                                <th>Событий</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rest as $entry)
                                <tr>
                                    <td>{{ 3 + $loop->iteration }}</td>
                                    <td>{{ $entry->team_name }}</td>
                                    <td>{{ $entry->team_type_label }}</td>
                                    <td>{{ $entry->points }}</td>
                                    <td>{{ $entry->events_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h2 class="rating-table-title">Полная таблица</h2>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>Место</th>
                                <th>Название команды</th>
                                <th>Участники</th>
                                <th>Очки</th>
                                <th>Событий</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topThree as $index => $entry)
                                <tr class="rating-table__row--top-3">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $entry->team_name }}</td>
                                    <td>{{ $entry->team_type_label }}</td>
                                    <td>{{ $entry->points }}</td>
                                    <td>{{ $entry->events_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <p class="rating-table-btn-info">Таблица загружается вручную администратором. Доступен файл Excel для скачивания.</p>
            <div class="rating-actions">
                <a href="{{ route('rating.export') }}" class="btn btn--primary" download>Скачать Excel файл</a>
            </div>
        @else
            <div class="rating-table-wrapper">
                <table class="rating-table">
                    <thead>
                        <tr>
                            <th>Место</th>
                            <th>Название команды</th>
                            <th>Участники</th>
                            <th>Очки</th>
                            <th>Событий</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="empty-state">Рейтинг будет загружен позже. Редактирование в <a href="{{ url('/admin') }}">админ-панели</a> → Рейтинг.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
</div>
@endsection
