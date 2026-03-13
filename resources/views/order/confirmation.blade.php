@extends('layouts.app')

@section('title', 'Заявка принята - Urban Running Games')

@section('content')
<div class="page-header page-header--order-confirmation">
    <div class="container">
        <h1>Заявка принята</h1>
        <p class="page-header-sub">Спасибо! Мы свяжемся с вами в ближайшее время.</p>
    </div>
</div>

<section class="order-confirmation-section">
    <div class="container">
        <div class="order-confirmation-card">
            <p class="order-confirmation-id">Номер заявки: <strong>#{{ $order->id }}</strong></p>
            <p>Имя: {{ $order->name }}</p>
            <p>Телефон: {{ $order->phone }}</p>
            <p>Email: {{ $order->email }}</p>
            @if($order->comment)
                <p>Комментарий: {{ $order->comment }}</p>
            @endif

            <h2>Состав заказа</h2>
            <ul class="order-confirmation-items">
                @foreach($order->items as $item)
                    <li>
                        {{ $item->product->name }}
                        @if($item->product_variant_id)
                            ({{ $item->variant_label }})
                        @endif
                        — {{ $item->quantity }} × {{ $item->display_price }} = {{ $item->subtotal }}
                    </li>
                @endforeach
            </ul>
            @if($order->promo_code_id && (float)($order->discount_amount ?? 0) > 0)
                <p class="order-confirmation-discount">Промокод {{ $order->promoCode?->code }}: −{{ number_format((float)$order->discount_amount, 0, ',', ' ') }} ₽</p>
            @endif
            <p class="order-confirmation-total"><strong>Итого: {{ $order->total_amount }}</strong></p>

            @if($order->isPaid())
                <p class="order-confirmation-paid">Оплачено {{ $order->paid_at?->format('d.m.Y H:i') }}</p>
            @elseif($order->payment?->pay_url)
                <div class="order-confirmation-actions" style="margin-top: 1rem;">
                    <p class="order-confirmation-pay-hint" style="margin-bottom: 0.75rem; font-size: 0.9rem; color: #666;">На странице Т-Банка выберите <strong>СБП (QR-код)</strong> для быстрой оплаты или оплату картой (комиссия выше).</p>
                    <a href="{{ $order->payment->pay_url }}" class="btn btn-primary">Перейти к оплате</a>
                </div>
            @endif

            <div class="order-confirmation-actions">
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Вернуться в магазин</a>
                <a href="{{ route('home') }}" class="btn btn-secondary">На главную</a>
            </div>
        </div>
    </div>
</section>
@endsection
