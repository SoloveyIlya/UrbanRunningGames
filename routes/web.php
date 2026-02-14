<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
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

// Магазин мерча (каталог)
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/product/{product}', [ProductController::class, 'show'])->name('shop.show');

// Корзина и оформление заявки
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/promo', [CartController::class, 'applyPromo'])->name('cart.promo.apply');
Route::post('/cart/promo/remove', [CartController::class, 'removePromo'])->name('cart.promo.remove');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');

// Партнёры и спонсоры
Route::get('/partners', [PartnerController::class, 'index'])->name('partners');

// Сводный рейтинг
Route::get('/rating', [RatingController::class, 'index'])->name('rating');

// Юридические страницы
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/consent', [LegalController::class, 'consent'])->name('legal.consent');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/returns', [LegalController::class, 'returns'])->name('legal.returns');

// Админка: экспорт заявок в CSV (только для авторизованных)
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('orders-export-csv', [\App\Http\Controllers\Admin\OrderExportController::class, 'csv'])->name('orders.export.csv');
});

// Раздача файлов из storage (fallback: ловит /storage/... когда симлинк не работает). Явный маршрут для route:cache.
Route::get('/storage/{path}', [StorageController::class, 'show'])->where('path', '.*');

// Раздача медиафайлов через Laravel (нет 404: при отсутствии файла отдаётся плейсхолдер). Используйте этот URL в приложении.
Route::get('/media/{path}', [StorageController::class, 'showMedia'])->where('path', '.*')->name('media.show');
