<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 1rem; }
        h1 { font-size: 1.25rem; }
        .field { margin-bottom: 1rem; }
        .label { font-weight: bold; }
        .value { margin-top: 0.25rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #ddd; padding: 0.5rem; text-align: left; }
        th { background: #f5f5f5; }
        hr { border: none; border-top: 1px solid #ddd; margin: 1.5rem 0; }
    </style>
</head>
<body>
    <h1>Новая заявка #{{ $order->id }}</h1>

    <div class="field">
        <span class="label">Имя:</span>
        <div class="value">{{ $order->name }}</div>
    </div>
    <div class="field">
        <span class="label">Телефон:</span>
        <div class="value">{{ $order->phone }}</div>
    </div>
    <div class="field">
        <span class="label">Email:</span>
        <div class="value"><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></div>
    </div>
    @if($order->comment)
    <div class="field">
        <span class="label">Комментарий:</span>
        <div class="value">{{ $order->comment }}</div>
    </div>
    @endif

    <h2 style="font-size: 1rem;">Состав заказа</h2>
    <table>
        <thead>
            <tr>
                <th>Товар</th>
                <th>Кол-во</th>
                <th>Цена</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}{{ $item->productVariant ? ' — ' . $item->productVariant->attribute_label : '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format((float) $item->price_amount, 0, ',', ' ') }} ₽</td>
                <td>{{ number_format((float) $item->price_amount * $item->quantity, 0, ',', ' ') }} ₽</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->promoCode)
    <p>Промокод: <strong>{{ $order->promoCode->code }}</strong> · Скидка: {{ number_format((float) $order->discount_amount, 0, ',', ' ') }} ₽</p>
    @endif
    <p><strong>Итого: {{ $order->total_amount }}</strong></p>

    <hr>
    <p style="font-size: 0.875rem; color: #666;">{{ $order->created_at?->format('d.m.Y H:i') }}</p>
</body>
</html>
