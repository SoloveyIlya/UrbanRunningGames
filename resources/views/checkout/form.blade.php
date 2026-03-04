@extends('layouts.app')

@section('title', 'Оформление заявки - Магазин - Urban Running Games')

@section('content')
<div class="page-header page-header--checkout">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="{{ route('shop.index') }}">Магазин</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('cart.index') }}">Корзина</a>
            <span class="breadcrumb-sep">/</span>
            <span>Оформление заявки</span>
        </nav>
        <h1>Оформление заявки</h1>
    </div>
</div>

<section class="checkout-section">
    <div class="container">
        <div class="checkout-grid">
            <div class="checkout-form-wrap">
                <h2>Контактные данные</h2>
                <form action="{{ route('checkout.store') }}" method="POST" class="contact-form">
                    @csrf
                    <div class="form-group">
                        <label for="name">Имя *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон *</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required maxlength="50">
                        @error('phone')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий к заявке</label>
                        <textarea id="comment" name="comment" rows="4" maxlength="2000">{{ old('comment') }}</textarea>
                        @error('comment')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <x-turnstile-widget />
                    <button type="submit" class="btn btn-primary">Отправить заявку</button>
                </form>
            </div>
            <div class="checkout-summary">
                <h2>Состав заказа</h2>
                <ul class="checkout-summary-list">
                    @foreach($items as $row)
                        <li>
                            <span class="checkout-item-name">{{ $row['product']->name }}</span>
                            @if($row['variant'])
                                <span class="checkout-item-variant">{{ $row['variant']->attribute_label }}</span>
                            @endif
                            <span class="checkout-item-qty">{{ $row['quantity'] }} × {{ number_format($row['price'], 0, ',', ' ') }} ₽</span>
                        </li>
                    @endforeach
                </ul>
                @if(($discount ?? 0) > 0)
                    <p class="checkout-discount">Промокод {{ $promo->code ?? '' }}: −{{ number_format($discount, 0, ',', ' ') }} ₽</p>
                @endif
                <p class="checkout-total"><strong>Итого: {{ number_format($total_final ?? $total, 0, ',', ' ') }} ₽</strong></p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    @if(config('turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endpush
