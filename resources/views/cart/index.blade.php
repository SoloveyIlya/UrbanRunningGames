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
            <div class="cart-total">
                <strong>Итого: {{ number_format($total, 0, ',', ' ') }} ₽</strong>
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
