@extends('layouts.app')

@section('title', 'Магазин мерча - Urban Running Games')

@section('content')
{{-- Слайдер сверху — позиционирование как .hero__overlay на главной --}}
@php
    $url1 = $shopHeroSlide1 ? (str_starts_with($shopHeroSlide1, 'http') ? $shopHeroSlide1 : asset($shopHeroSlide1)) : '';
    $url2 = $shopHeroSlide2 ? (str_starts_with($shopHeroSlide2, 'http') ? $shopHeroSlide2 : asset($shopHeroSlide2)) : '';
    $url3 = $shopHeroSlide3 ? (str_starts_with($shopHeroSlide3, 'http') ? $shopHeroSlide3 : asset($shopHeroSlide3)) : '';
    $slide1Style = $url1 ? "linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.3) 100%), url('" . e($url1) . "')" : "linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%), url('" . asset('images/image.svg') . "')";
    $slide2Style = $url2 ? "linear-gradient(135deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.4) 100%), url('" . e($url2) . "')" : "linear-gradient(135deg, rgba(38, 32, 58, 0.92) 0%, rgba(105, 88, 160, 0.85) 100%)";
    $slide3Style = $url3 ? "linear-gradient(135deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.4) 100%), url('" . e($url3) . "')" : "linear-gradient(135deg, rgba(45, 45, 45, 0.95) 0%, rgba(102, 126, 234, 0.7) 100%)";
    $overlayOpacity = is_numeric($shopHeroOverlayOpacity ?? '') ? (float) $shopHeroOverlayOpacity : 0.5;
@endphp
<div class="hero shop-hero hero--with-video">
    <div class="shop-hero__slider">
        <div class="shop-hero__track">
            <div class="shop-hero__slide shop-hero__slide--1" style="background-image: {{ $slide1Style }};"></div>
            <div class="shop-hero__slide shop-hero__slide--2" style="background-image: {{ $slide2Style }};"></div>
            <div class="shop-hero__slide shop-hero__slide--3" style="background-image: {{ $slide3Style }};"></div>
        </div>
    </div>
    <div class="hero__overlay" style="opacity: {{ $overlayOpacity }};"></div>
    <div class="shop-hero__content">
        <div class="container">
            <h1 class="shop-hero__title">Магазин мерча</h1>
            <p class="shop-hero__sub">Футболки, брендированная экипировка и сувениры</p>
        </div>
    </div>
</div>

<section class="shop-section">
    <div class="container">
        {{-- Фильтры: кнопка-иконка, по нажатию панель расширяется вправо --}}
        <form method="get" action="{{ route('shop.index') }}" class="shop-filters">
            <div class="shop-filters__panel" id="shopFiltersPanel">
                <button type="button" class="shop-filters__toggle" id="shopFiltersToggle" aria-expanded="false" aria-controls="shopFiltersRow" aria-label="Открыть фильтры">
                    <svg class="shop-filters__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                </button>
                <div class="shop-filters__row" id="shopFiltersRow">
                <label class="shop-filters__label">
                    <span class="shop-filters__text">Название</span>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Поиск по названию" class="shop-filters__input">
                </label>
                <label class="shop-filters__label">
                    <span class="shop-filters__text">Цена от, ₽</span>
                    <input type="number" name="price_min" value="{{ request('price_min') }}" min="0" step="1" placeholder="0" class="shop-filters__input">
                </label>
                <label class="shop-filters__label">
                    <span class="shop-filters__text">Цена до, ₽</span>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" min="0" step="1" placeholder="—" class="shop-filters__input">
                </label>
                <label class="shop-filters__label">
                    <span class="shop-filters__text">Сортировка</span>
                    <select name="sort" class="shop-filters__select">
                        <option value="">По умолчанию</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена: по возрастанию</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена: по убыванию</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Название: А–Я</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Название: Я–А</option>
                    </select>
                </label>
                <button type="submit" class="btn shop-filters__btn">Показать</button>
                </div>
            </div>
        </form>

        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    <a href="{{ route('shop.show', $product) }}" class="product-card">
                        <div class="product-card-image">
                            @if($product->cover_url)
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="400" height="400">
                            @else
                                <div class="product-card-placeholder">
                                    <span>Нет фото</span>
                                </div>
                            @endif
                        </div>
                        <div class="product-card-info">
                            <h3 class="product-card-title">{{ $product->name }}</h3>
                            <p class="product-card-price">{{ $product->display_price }}</p>
                            @if($product->hasAttributes())
                                <p class="product-card-attributes">Размеры / цвета в наличии</p>
                            @endif
                            <span class="btn btn-sm product-card__btn">Подробнее</span>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($products->hasPages())
                <div class="pagination-wrap">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <p>В каталоге пока нет товаров. Следите за обновлениями!</p>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
(function() {
    var slides = document.querySelectorAll('.shop-hero__slide');
    if (slides.length > 1) {
        var current = 0;
        function show(i) {
            current = (i + slides.length) % slides.length;
            slides.forEach(function(s, j) { s.classList.toggle('active', j === current); });
        }
        slides.forEach(function(s, i) { s.classList.toggle('active', i === 0); });
        setInterval(function() { show(current + 1); }, 5000);
    }
    var toggle = document.getElementById('shopFiltersToggle');
    var row = document.getElementById('shopFiltersRow');
    if (toggle && row) {
        var panel = document.getElementById('shopFiltersPanel');
        toggle.addEventListener('click', function() {
            var open = panel ? !panel.classList.contains('is-open') : !row.classList.contains('is-open');
            if (panel) panel.classList.toggle('is-open', open);
            row.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Закрыть фильтры' : 'Открыть фильтры');
        });
    }
})();
</script>
@endpush
@endsection
