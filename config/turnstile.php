<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    | Site key (public) — для виджета на фронте.
    | Secret key — только на бэкенде для верификации через Siteverify API.
    | Если ключи не заданы, проверка Turnstile не выполняется (удобно для локальной разработки).
    |
    | https://developers.cloudflare.com/turnstile/
    */

    'site_key' => env('TURNSTILE_SITE_KEY', ''),
    'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    'siteverify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',

];
