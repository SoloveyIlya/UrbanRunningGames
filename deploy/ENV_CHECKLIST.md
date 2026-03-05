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

## Платежи (T-Bank)

Подробная инструкция: **[Платежи T-Bank: демо и боевой режим](PAYMENT_TBANK.md)** — пошагово: сначала демо-терминал, затем переключение на рабочий терминал.

**Режимы:**

1. **Локальная имитация** — `PAYMENT_TEST_MODE=true` (по умолчанию). После оформления заказа редирект на страницу «Оплата прошла успешно (тестовый режим)» без вызова API.
2. **Тестовые платежи через демо-терминал T-Bank** — реальный сценарий: форма T-Bank, тестовые карты, вебхуки. Подключение по документу «Протестируйте платежи» (SPRUT RUN STORE).

| Переменная | Описание |
|------------|----------|
| `PAYMENT_TEST_MODE` | `true` — имитация без API; `false` — вызов API T-Bank (для демо или боевого терминала) |
| `TBANK_USE_DEMO_TERMINAL` | `true` — использовать демо-терминал из документа (терминал `1771878373032DEMO`). Обязательно вместе с `PAYMENT_TEST_MODE=false` |
| `TBANK_DEMO_PASSWORD` | Пароль демо-терминала (по умолчанию из документа: `J9ttMgcoh^Yp9AtX`). Можно задать в `.env`, если пароль другой |
| `TBANK_TERMINAL_KEY` | Идентификатор терминала (для боевой оплаты или свой тестовый) |
| `TBANK_PASSWORD` | Пароль терминала |
| `TBANK_NOTIFICATION_URL` | Полный URL вебхука, напр. `https://site.ru/payment/webhook` |
| `TBANK_SUCCESS_URL` | (Опционально) URL возврата после успешной оплаты |
| `TBANK_FAIL_URL` | (Опционально) URL возврата при ошибке оплаты |

**Как протестировать платежи через демо-терминал**

В `.env` задать:

```env
PAYMENT_TEST_MODE=false
TBANK_USE_DEMO_TERMINAL=true
```

Пароль демо-терминала уже задан в конфиге. Если в вашем документе другой пароль, добавьте:

```env
TBANK_DEMO_PASSWORD="ваш_пароль"
```

Убедитесь, что `APP_URL` совпадает с тем, откуда вы заходите (например, `http://localhost:8000`), чтобы ссылки SuccessURL/FailURL и вебхук работали. После оформления заказа откроется форма оплаты T-Bank; можно использовать тестовые карты из личного кабинета T-Bank и проверить успешную оплату и возврат на сайт.

## Прочее

| Переменная | Production |
|------------|------------|
| `SESSION_DRIVER` | `database` или `file` |
| `CACHE_STORE` | `database` или `file` |
| `QUEUE_CONNECTION` | `database` (или `sync` если очереди не используются) |
