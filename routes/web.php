<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', [HomeController::class, 'index'])->name('home');

// О команде организатора
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Правила забега
Route::get('/rules', [RulesController::class, 'index'])->name('rules');

// События
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/archive', [EventController::class, 'archive'])->name('events.archive');

// Фотогалерея (альбомы по событиям)
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/album/{album}', [GalleryController::class, 'show'])->name('gallery.show');

// Контакты
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Партнёры и спонсоры
Route::get('/partners', [PartnerController::class, 'index'])->name('partners');

// Сводный рейтинг
Route::get('/rating', [RatingController::class, 'index'])->name('rating');

// Юридические страницы
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/consent', [LegalController::class, 'consent'])->name('legal.consent');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/returns', [LegalController::class, 'returns'])->name('legal.returns');

// Раздача файлов из storage (fallback: ловит /storage/... когда симлинк не работает)
Route::fallback(function (\Illuminate\Http\Request $request) {
    $uriPath = parse_url($request->getRequestUri(), PHP_URL_PATH);
    if ($request->isMethod('GET') && $uriPath && str_starts_with($uriPath, '/storage/')) {
        return app(StorageController::class)->show($request);
    }
    abort(404);
});
