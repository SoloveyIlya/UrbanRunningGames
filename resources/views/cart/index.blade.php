@extends('layouts.app')

@section('title', 'Корзина - Магазин - Urban Running Games')

@section('content')
<div class="page-cart">
<div class="page-header page-header--cart bg-[#111] text-gray-200 py-8 px-0">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        <nav class="breadcrumb-nav flex items-center gap-1 text-sm text-white/80 mb-2" aria-label="Хлебные крошки">
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="breadcrumb-sep">/</span>
            <span>Корзина</span>
        </nav>
        <h1 class="text-3xl font-bold italic uppercase tracking-wide text-white m-0">Корзина</h1>
    </div>
</div>

<section class="cart-section py-12 md:py-16">
    <div class="container max-w-[1200px] mx-auto px-4 sm:px-5">
        @if(count($items) > 0)
            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="cart-th-product">Товар</th>
                            <th class="cart-th-variant">Вариант</th>
                            <th class="cart-th-price">Цена</th>
                            <th class="cart-th-qty">Кол-во</th>
                            <th class="cart-th-subtotal">Сумма</th>
                            <th class="cart-th-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $row)
                            <tr class="cart-row">
                                <td data-label="Товар" class="cart-td-product">
                                    <a href="{{ route('shop.show', $row['product']) }}" class="cart-item-product">
                                        @if($row['product']->cover_url)
                                            <img src="{{ $row['product']->cover_url }}" alt="" class="cart-item-thumb" width="80" height="80">
                                        @else
                                            <span class="cart-item-no-photo">—</span>
                                        @endif
                                        <span class="cart-item-name">{{ $row['product']->name }}</span>
                                    </a>
                                </td>
                                <td data-label="Вариант" class="cart-td-variant">{{ $row['variant'] ? $row['variant']->attribute_label : '—' }}</td>
                                <td data-label="Цена" class="cart-td-price">{{ number_format($row['price'], 0, ',', ' ') }} ₽</td>
                                <td data-label="Кол-во" class="cart-td-qty">
                                    <form action="{{ route('cart.update') }}" method="POST" class="cart-qty-form">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $row['key'] }}">
                                        <input type="number" name="quantity" value="{{ $row['quantity'] }}" min="1" max="99" class="cart-qty-input" aria-label="Количество">
                                        <button type="submit" class="btn btn-sm cart-qty-btn">OK</button>
                                    </form>
                                </td>
                                <td data-label="Сумма" class="cart-td-subtotal">{{ number_format($row['subtotal'], 0, ',', ' ') }} ₽</td>
                                <td class="cart-td-actions" data-label="">
                                    <form action="{{ route('cart.remove', $row['key']) }}" method="POST" onsubmit="return confirm('Удалить из корзины?');" class="cart-remove-form">
                                        @csrf
                                        <button type="submit" class="cart-remove-btn" aria-label="Удалить из корзины">✕</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="cart-summary">
                <div class="cart-promo">
                    @if(session('promo_error'))
                        <p class="cart-promo-error" role="alert">{{ session('promo_error') }}</p>
                    @endif
                    @if($promo ?? null)
                        <div class="cart-promo-applied">
                            <span class="cart-promo-text">Промокод <strong>{{ $promo->code }}</strong> применён. Скидка: −{{ number_format($discount ?? 0, 0, ',', ' ') }} ₽</span>
                            <form action="{{ route('cart.promo.remove') }}" method="POST" class="cart-promo-remove-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">Отменить</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.promo.apply') }}" method="POST" class="cart-promo-form">
                            @csrf
                            <label for="promo_code" class="cart-promo-label">Промокод</label>
                            <input type="text" id="promo_code" name="code" placeholder="Введите код" maxlength="64" class="cart-promo-input" aria-label="Промокод">
                            <button type="submit" class="btn btn-secondary">Применить</button>
                        </form>
                    @endif
                </div>
                <div class="cart-total">
                    @if(($discount ?? 0) > 0)
                        <p class="cart-total-line cart-total-sub">Сумма: {{ number_format($total, 0, ',', ' ') }} ₽</p>
                        <p class="cart-total-line cart-total-discount">Скидка: −{{ number_format($discount, 0, ',', ' ') }} ₽</p>
                    @endif
                    <p class="cart-total-line cart-total-final"><strong>Итого: {{ number_format($total_final ?? $total, 0, ',', ' ') }} ₽</strong></p>
                </div>
                <div class="cart-actions">
                    <a href="{{ route('shop.index') }}" class="btn btn-secondary">← Продолжить покупки</a>
                    <a href="{{ route('checkout.show') }}" class="btn btn-primary">Оформить заявку</a>
                </div>
            </div>
        @else
            <div class="cart-empty">
                <p class="cart-empty-text">Корзина пуста</p>
                <p class="cart-empty-hint">Добавьте товары из каталога, чтобы оформить заказ.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Перейти в каталог</a>
            </div>
        @endif
    </div>
</section>
</div>
@endsection
