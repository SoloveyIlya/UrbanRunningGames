# Чек-лист переменных окружения для production

Обязательные и рекомендуемые настройки `.env` на production-сервере.

## Обязательные

| Переменная | Пример | Описание |
|------------|--------|----------|
| `APP_ENV` | `production` | Режим production |
| `APP_DEBUG` | `false` | Отключить отладку |
| `APP_KEY` | *(сгенерировать)* | `php artisan key:generate` |
| `APP_URL` | `https://urban-running-games.ru` | Полный URL сайта с https |
| `DB_DATABASE` | `urban_running` | Имя БД |
| `DB_USERNAME` | `urban_user` | Пользователь MySQL |
| `DB_PASSWORD` | `***` | Пароль MySQL |

## Логирование

| Переменная | Рекомендуемое значение |
|------------|------------------------|
| `LOG_CHANNEL` | `stack` |
| `LOG_STACK` | `daily` |
| `LOG_DAILY_DAYS` | `14` |
| `LOG_LEVEL` | `error` (или `warning`) |

## Почта

Для уведомлений о заявках и обращениях:

| Переменная | Описание |
|------------|----------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | Хост SMTP |
| `MAIL_PORT` | 587 (TLS) или 465 (SSL) |
| `MAIL_USERNAME` | Логин SMTP |
| `MAIL_PASSWORD` | Пароль SMTP |
| `MAIL_ENCRYPTION` | `tls` или `ssl` |
| `MAIL_FROM_ADDRESS` | Адрес отправителя |
| `MAIL_FROM_NAME` | Имя отправителя |
| `MAIL_ADMIN_EMAIL` | Куда слать уведомления (если пусто — MAIL_FROM_ADDRESS) |

## Cloudflare Turnstile (антиспам)

Для production формы проверки Turnstile **обязательны**:

| Переменная | Описание |
|------------|----------|
| `TURNSTILE_SITE_KEY` | Публичный ключ из Cloudflare Turnstile |
| `TURNSTILE_SECRET_KEY` | Секретный ключ |

Получить ключи: [Cloudflare Turnstile](https://dash.cloudflare.com/?to=/:account/turnstile)

## Прочее

| Переменная | Production |
|------------|------------|
| `SESSION_DRIVER` | `database` или `file` |
| `CACHE_STORE` | `database` или `file` |
| `QUEUE_CONNECTION` | `database` (или `sync` если очереди не используются) |
