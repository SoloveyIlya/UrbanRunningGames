<?php

/**
 * Роутер для встроенного PHP-сервера.
 * Запросы /storage/* всегда идут в Laravel (раздача файлов). Остальное — статика по возможности.
 * Запуск: php -S localhost:8000 -t public public/router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// /storage/* всегда в Laravel (симлинк может не работать на WSL/встроенном сервере)
if (str_starts_with($uri, '/storage/')) {
    require_once __DIR__ . '/index.php';
    return true;
}

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // отдать статический файл (css, js, favicon)
}

require_once __DIR__ . '/index.php';
