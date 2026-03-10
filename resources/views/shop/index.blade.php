@extends('layouts.app')

@section('title', 'Магазин - Urban Running Games')

@section('content')
<div class="shop-page-wrap min-h-screen text-gray-100 relative z-10">
    <div class="max-w-[1200px] mx-auto">
        {{-- Breadcrumb — по макету: 16px/20px, #8D49EE --}}
        <nav class="shop-breadcrumb mb-0" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Главная</a>
            <span class="shop-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="shop-breadcrumb__current" aria-current="page">Магазин</span>
        </nav>

        {{-- Title — по макету --}}
        <h1 class="shop-title">SPRUT STYLE STORE</h1>

        {{-- Подзаголовок под заголовком — по макету --}}
        <p class="shop-subtitle">Твой стиль – твоя жизнь. Наш мерч – твоя мотивация. Бег – это твой ритм, твое движение, твоя свобода. Теперь он может быть и твоим стилем.</p>

        {{-- Фильтры по типам (Frame 102/101): под заголовком — стекло + активная как btn--race --}}
        <div class="shop-toolbar-row">
            <nav class="shop-filters-row" aria-label="Типы товаров">
                <a href="{{ route('shop.index') }}"
                   class="shop-filter-btn {{ ($activeType ?? '') === '' ? 'shop-filter-btn--active' : '' }}">Все товары</a>
                @foreach($productTypes ?? [] as $type)
                    <a href="{{ route('shop.index', ['type' => $type->slug]) }}"
                       class="shop-filter-btn {{ ($activeType ?? '') === $type->slug ? 'shop-filter-btn--active' : '' }}">
                        {{ $type->label }}
                    </a>
                @endforeach
            </nav>

            <a href="{{ route('cart.index') }}"
               class="shop-cart-link"
               aria-label="Перейти в корзину"
               title="Корзина">
                <svg class="shop-cart-link__icon" width="32" height="26" viewBox="0 0 32 26" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M14.1395 13C14.1395 12.3846 13.6398 11.8857 13.0233 11.8857C12.4068 11.8857 11.907 12.3846 11.907 13V18.9428C11.907 19.5582 12.4068 20.0571 13.0233 20.0571C13.6398 20.0571 14.1395 19.5582 14.1395 18.9428V13Z" fill="white" fill-opacity="0.6"/>
                    <path d="M20.093 13C20.093 12.3846 19.5932 11.8857 18.9767 11.8857C18.3602 11.8857 17.8605 12.3846 17.8605 13V18.9428C17.8605 19.5582 18.3602 20.0571 18.9767 20.0571C19.5932 20.0571 20.093 19.5582 20.093 18.9428V13Z" fill="white" fill-opacity="0.6"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.2545 0.326366C20.8186 -0.108789 20.1118 -0.108789 19.6758 0.326366C19.2399 0.761521 19.2399 1.46705 19.6758 1.9022L23.7237 5.94286H8.63748L12.6854 1.90222C13.1213 1.46707 13.1213 0.761543 12.6854 0.326389C12.2494 -0.108766 11.5426 -0.108766 11.1067 0.326389L5.48016 5.94286H1.11628C0.499775 5.94286 0 6.44174 0 7.05714C0 7.67255 0.499775 8.17143 1.11628 8.17143H1.92642C2.26341 9.4435 2.69927 10.9101 3.20641 12.6166L4.73184 17.7497C5.33785 19.7899 5.70936 21.0406 6.36054 22.0577C7.48161 23.8086 9.20358 25.0917 11.2041 25.6665C12.366 26.0005 13.6729 26.0003 15.8048 26H16.1952C18.3271 26.0003 19.634 26.0005 20.7959 25.6665C22.7964 25.0917 24.5184 23.8086 25.6395 22.0577C26.2906 21.0406 26.6622 19.7899 27.2682 17.7496L28.7935 12.617C29.3006 10.9104 29.7366 9.44352 30.0736 8.17143H30.8837C31.5002 8.17143 32 7.67255 32 7.05714C32 6.44174 31.5002 5.94286 30.8837 5.94286H26.8811L21.2545 0.326366ZM5.35432 12.0082C4.91615 10.5338 4.54141 9.27232 4.23868 8.17143H27.7613C27.4586 9.27232 27.0839 10.5338 26.6457 12.0082L25.1833 16.929C24.5037 19.2158 24.2183 20.1391 23.7583 20.8575C22.9297 22.1517 21.6569 23.1 20.1783 23.525C19.3575 23.7608 18.3895 23.7714 16 23.7714C13.6105 23.7714 12.6425 23.7608 11.8217 23.525C10.3431 23.1 9.07032 22.1517 8.24171 20.8575C7.78174 20.1391 7.49628 19.2158 6.8167 16.929L5.35432 12.0082Z" fill="white" fill-opacity="0.6"/>
                </svg>
                @php $cartCount = \App\Http\Controllers\CartController::getCount(); @endphp
                @if($cartCount > 0)
                    <span class="shop-cart-link__count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        {{-- Product grid — карточки без фона и границ, по макету --}}
        @if($products->count() > 0)
            <div class="shop-products-grid">
                @foreach($products as $product)
                    <a href="{{ route('shop.show', $product) }}" class="shop-product-card">
                        <div class="shop-product-card__img">
                            @if($product->cover_url)
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                            @else
                                <span class="shop-product-card__no-photo">Нет фото</span>
                            @endif
                        </div>
                        <h3 class="shop-product-card__title">{{ $product->name }}</h3>
                        <p class="shop-product-card__price">{{ $product->display_price }}</p>
                    </a>
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="mt-10 flex justify-center pagination-wrap">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="shop-empty-state">
                <p class="shop-empty-state__text">В этой категории пока нет товаров. Попробуйте другую или загляните позже.</p>
                <a href="{{ route('shop.index') }}" class="shop-empty-state__btn">Все товары</a>
            </div>
        @endif
    </div>
</div>
@endsection
