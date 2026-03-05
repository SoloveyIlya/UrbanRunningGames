<?php

return [
    'test_mode' => (bool) env('PAYMENT_TEST_MODE', true),

    'tbank' => [
        'terminal_key' => env('TBANK_TERMINAL_KEY', ''),
        'password' => env('TBANK_PASSWORD', ''),
        'api_url' => env('TBANK_API_URL', 'https://securepay.tinkoff.ru/v2/Init'),
        'notification_url' => env('TBANK_NOTIFICATION_URL'), // полный URL вебхука, например https://site.ru/payment/webhook
        'success_url' => env('TBANK_SUCCESS_URL'), // куда вернуть после успешной оплаты
        'fail_url' => env('TBANK_FAIL_URL'),       // куда вернуть при ошибке
    ],
];
