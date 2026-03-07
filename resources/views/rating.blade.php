@extends('layouts.app')

@section('title', 'Сводный рейтинг - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Сводный рейтинг</h1>
    </div>
</div>

<section class="rating-section">
    <div class="container">
        <div class="rating-info">
            <p>Рейтинг команд по результатам всех проведённых забегов-игр.</p>
            <p>Таблица загружается вручную администратором. Доступен файл Excel для скачивания.</p>
        </div>

        <div class="rating-table-wrapper">
            <table class="rating-table">
                <thead>
                    <tr>
                        <th>Место</th>
                        <th>Название команды</th>
                        <th>Очки</th>
                        <th>Событий</th>
                    </tr>
                </thead>
                <tbody>
                    @if($entries->isNotEmpty())
                        @foreach($entries as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $entry->team_name }}</td>
                                <td>{{ $entry->points }}</td>
                                <td>{{ $entry->events_count }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="empty-state">Рейтинг будет загружен позже. Редактирование в <a href="{{ url('/admin') }}">админ-панели</a> → Рейтинг.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($entries->isNotEmpty())
            <div class="rating-actions">
                <a href="{{ route('rating.export') }}" class="btn" download>Скачать Excel файл</a>
            </div>
        @endif
    </div>
</section>
@endsection
