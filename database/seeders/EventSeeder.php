<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём города
        $moscow = City::firstOrCreate(
            ['name' => 'Москва'],
            ['country_code' => 'RU', 'is_active' => true]
        );

        $spb = City::firstOrCreate(
            ['name' => 'Санкт-Петербург'],
            ['country_code' => 'RU', 'is_active' => true]
        );

        $kazan = City::firstOrCreate(
            ['name' => 'Казань'],
            ['country_code' => 'RU', 'is_active' => true]
        );

        // Создаём партнёров
        $partner1 = Partner::firstOrCreate(
            ['name' => 'Спортивный магазин "Бег"'],
            [
                'website_url' => 'https://example.com',
                'description' => 'Официальный партнёр по спортивной экипировке',
                'level' => 'sponsor',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $partner2 = Partner::firstOrCreate(
            ['name' => 'Фитнес-клуб "Энергия"'],
            [
                'website_url' => 'https://example.com',
                'description' => 'Партнёр по подготовке участников',
                'level' => 'partner',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // Предстоящие события
        $event1 = Event::firstOrCreate(
            ['slug' => 'moscow-spring-2026'],
            [
                'title' => 'Весенний забег в Москве 2026',
                'city_id' => $moscow->id,
                'location_text' => 'Парк Сокольники, главный вход',
                'starts_at' => now()->addMonths(2)->setTime(10, 0),
                'description' => 'Приглашаем всех любителей бега на весенний забег-игру в Москве! Командный формат, интересные задания и отличная атмосфера.',
                'rules' => 'Участие командное (4 человека). Обязательна спортивная форма. Старт в 10:00. Регистрация за 30 минут до старта.',
                'status' => 'published',
            ]
        );

        $event2 = Event::firstOrCreate(
            ['slug' => 'spb-summer-2026'],
            [
                'title' => 'Летний забег в Санкт-Петербурге 2026',
                'city_id' => $spb->id,
                'location_text' => 'Центральный парк культуры и отдыха',
                'starts_at' => now()->addMonths(4)->setTime(9, 30),
                'description' => 'Летний забег-игра в культурной столице России. Уникальные локации, интересные задания и море позитивных эмоций!',
                'rules' => 'Команды из 4 человек. Рекомендуется взять с собой воду. Старт в 9:30.',
                'status' => 'published',
            ]
        );

        $event3 = Event::firstOrCreate(
            ['slug' => 'kazan-autumn-2026'],
            [
                'title' => 'Осенний забег в Казани 2026',
                'city_id' => $kazan->id,
                'location_text' => 'Стадион "Казань Арена"',
                'starts_at' => now()->addMonths(7)->setTime(11, 0),
                'description' => 'Осенний забег-игра в столице Татарстана. Исторические места, современные маршруты и незабываемые впечатления.',
                'rules' => 'Командный формат (4 участника). Старт в 11:00. Регистрация обязательна.',
                'status' => 'published',
            ]
        );

        // Прошедшие события (для архива)
        $event4 = Event::firstOrCreate(
            ['slug' => 'moscow-winter-2025'],
            [
                'title' => 'Зимний забег в Москве 2025',
                'city_id' => $moscow->id,
                'location_text' => 'Парк Горького',
                'starts_at' => now()->subMonths(3)->setTime(10, 0),
                'description' => 'Зимний забег-игра, который прошёл в декабре 2025 года. Отличная погода, замечательные участники и незабываемая атмосфера!',
                'rules' => 'Команды из 4 человек. Зимняя спортивная форма обязательна.',
                'status' => 'published',
            ]
        );

        $event5 = Event::firstOrCreate(
            ['slug' => 'spb-fall-2025'],
            [
                'title' => 'Осенний забег в Санкт-Петербурге 2025',
                'city_id' => $spb->id,
                'location_text' => 'Летний сад',
                'starts_at' => now()->subMonths(5)->setTime(10, 30),
                'description' => 'Осенний забег-игра в Санкт-Петербурге. Красивые осенние пейзажи и интересные задания по всему городу.',
                'rules' => 'Командный формат. Старт в 10:30.',
                'status' => 'published',
            ]
        );

        $event6 = Event::firstOrCreate(
            ['slug' => 'moscow-summer-2025'],
            [
                'title' => 'Летний забег в Москве 2025',
                'city_id' => $moscow->id,
                'location_text' => 'ВДНХ',
                'starts_at' => now()->subMonths(8)->setTime(9, 0),
                'description' => 'Летний забег-игра на ВДНХ. Много участников, отличная погода и незабываемые впечатления!',
                'rules' => 'Команды из 4 человек. Старт в 9:00.',
                'status' => 'published',
            ]
        );

        // Привязываем партнёров к событиям
        $event1->partners()->syncWithoutDetaching([$partner1->id, $partner2->id]);
        $event2->partners()->syncWithoutDetaching([$partner1->id]);
        $event4->partners()->syncWithoutDetaching([$partner2->id]);
        $event5->partners()->syncWithoutDetaching([$partner1->id, $partner2->id]);
    }
}
