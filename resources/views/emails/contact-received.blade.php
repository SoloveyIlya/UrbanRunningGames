<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое обращение</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 1rem; }
        h1 { font-size: 1.25rem; }
        .field { margin-bottom: 1rem; }
        .label { font-weight: bold; }
        .value { margin-top: 0.25rem; }
        hr { border: none; border-top: 1px solid #ddd; margin: 1.5rem 0; }
    </style>
</head>
<body>
    <h1>Новое обращение с сайта</h1>
    <p>Тема: <strong>{{ $message->topic_label }}</strong></p>

    <div class="field">
        <span class="label">Имя:</span>
        <div class="value">{{ $message->full_name }}</div>
    </div>
    @if($message->phone)
    <div class="field">
        <span class="label">Телефон:</span>
        <div class="value">{{ $message->phone }}</div>
    </div>
    @endif
    @if($message->email)
    <div class="field">
        <span class="label">Email:</span>
        <div class="value"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></div>
    </div>
    @endif
    <div class="field">
        <span class="label">Сообщение:</span>
        <div class="value">{{ $message->message }}</div>
    </div>

    <hr>
    <p style="font-size: 0.875rem; color: #666;">IP: {{ $message->ip ?? '—' }} · {{ $message->created_at?->format('d.m.Y H:i') }}</p>
</body>
</html>
