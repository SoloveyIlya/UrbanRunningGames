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

        <!-- TODO: Реализовать загрузку рейтинга из файла или БД -->
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
                    <tr>
                        <td colspan="4" class="empty-state">Рейтинг будет загружен позже</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rating-actions">
            <a href="#" class="btn" download>Скачать Excel файл</a>
        </div>
    </div>
</section>
@endsection
