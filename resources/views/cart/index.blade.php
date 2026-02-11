@extends('layouts.app')

@section('title', 'Корзина - Магазин - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="breadcrumb-sep">/</span>
            <span>Корзина</span>
        </nav>
        <h1>Корзина</h1>
    </div>
</div>

<section class="cart-section">
    <div class="container">
        @if(count($items) > 0)
            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Вариант</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Сумма</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('shop.show', $row['product']) }}" class="cart-item-product">
                                        @if($row['product']->cover_url)
                                            <img src="{{ $row['product']->cover_url }}" alt="" class="cart-item-thumb" width="60" height="60">
                                        @else
                                            <span class="cart-item-no-photo">—</span>
                                        @endif
                                        <span>{{ $row['product']->name }}</span>
                                    </a>
                                </td>
                                <td>{{ $row['variant'] ? $row['variant']->attribute_label : '—' }}</td>
                                <td>{{ number_format($row['price'], 0, ',', ' ') }} ₽</td>
                                <td>
                                    <form action="{{ route('cart.update') }}" method="POST" class="cart-qty-form">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $row['key'] }}">
                                        <input type="number" name="quantity" value="{{ $row['quantity'] }}" min="1" max="99" class="cart-qty-input">
                                        <button type="submit" class="btn btn-sm">OK</button>
                                    </form>
                                </td>
                                <td>{{ number_format($row['subtotal'], 0, ',', ' ') }} ₽</td>
                                <td>
                                    <form action="{{ route('cart.remove', $row['key']) }}" method="POST" onsubmit="return confirm('Удалить из корзины?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" aria-label="Удалить">✕</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="cart-promo">
                @if($promo ?? null)
                    <p class="cart-promo-applied">
                        Промокод <strong>{{ $promo->code }}</strong> применён.
                        Скидка: −{{ number_format($discount ?? 0, 0, ',', ' ') }} ₽
                        <form action="{{ route('cart.promo.remove') }}" method="POST" class="cart-promo-remove-form">
                            @csrf
                            <button type="submit" class="btn btn-sm">Отменить</button>
                        </form>
                    </p>
                @else
                    <form action="{{ route('cart.promo.apply') }}" method="POST" class="cart-promo-form">
                        @csrf
                        <label for="promo_code">Промокод:</label>
                        <input type="text" id="promo_code" name="code" placeholder="Введите код" maxlength="64" class="cart-promo-input">
                        <button type="submit" class="btn btn-secondary">Применить</button>
                    </form>
                @endif
            </div>
            <div class="cart-total">
                @if(($discount ?? 0) > 0)
                    <p>Сумма: {{ number_format($total, 0, ',', ' ') }} ₽</p>
                    <p>Скидка: −{{ number_format($discount, 0, ',', ' ') }} ₽</p>
                @endif
                <p><strong>Итого: {{ number_format($total_final ?? $total, 0, ',', ' ') }} ₽</strong></p>
            </div>
            <div class="cart-actions">
                <a href="{{ route('shop.index') }}" class="btn btn-secondary">← Продолжить покупки</a>
                <a href="{{ route('checkout.show') }}" class="btn btn-primary">Оформить заявку</a>
            </div>
        @else
            <div class="empty-state">
                <p>Корзина пуста.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Перейти в каталог</a>
            </div>
        @endif
    </div>
</section>
@endsection
