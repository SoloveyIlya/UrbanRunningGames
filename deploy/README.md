# Деплой Urban Running Games

Конфигурации и инструкции для развёртывания проекта на production-сервере.

## Содержание

| Файл | Описание |
|------|----------|
| [nginx/urban-running.conf](nginx/urban-running.conf) | Пример vhost Nginx (HTTP + SSL) |
| [ssl-letsencrypt.md](ssl-letsencrypt.md) | HTTPS: Let's Encrypt + автообновление |
| [logrotate/laravel.conf](logrotate/laravel.conf) | Ротация логов Laravel |
| [ENV_CHECKLIST.md](ENV_CHECKLIST.md) | Чек-лист переменных окружения для production |

## Полный регламент

Подробный регламент деплоя (подготовка, шаги, откат, чек-лист): **[`doсs/Регламент деплоя.md`](../doсs/Регламент%20деплоя.md)**

---

## Быстрый старт (первый деплой)

### 1. Требования на сервере

- **PHP 8.4** (расширения: mbstring, pdo_mysql, xml, ctype, json, fileinfo, openssl, tokenizer, **gd** или **imagick**)
- **Composer**
- **Node.js** и **npm**
- **MySQL 8**
- **Nginx** (или Apache)

### 2. Размещение кода

```bash
# Клонировать репозиторий
git clone https://github.com/YOUR_ORG/urban-running-games.git /var/www/urban-running-games
cd /var/www/urban-running-games

# Установка зависимостей
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

### 3. Конфигурация

```bash
cp .env.example .env
php artisan key:generate
```

Заполните `.env` по чек-листу: [ENV_CHECKLIST.md](ENV_CHECKLIST.md)

### 4. База данных и медиа

```bash
php artisan migrate --force
php artisan storage:link
```

### 5. Права и веб-сервер

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

### 6. Nginx

Скопировать конфиг и активировать:

```bash
sudo cp /var/www/urban-running-games/deploy/nginx/urban-running.conf /etc/nginx/sites-available/urban-running
# Отредактировать server_name и путь к проекту при необходимости
sudo ln -s /etc/nginx/sites-available/urban-running /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 7. HTTPS (Let's Encrypt)

После того как сайт доступен по HTTP:

```bash
sudo certbot --nginx -d ваш-домен.ru
```

Подробнее: [ssl-letsencrypt.md](ssl-letsencrypt.md)

### 8. Logrotate (опционально)

```bash
sudo cp /var/www/urban-running-games/deploy/logrotate/laravel.conf /etc/logrotate.d/urban-running
# Проверить путь в конфиге
sudo logrotate -d /etc/logrotate.d/urban-running
```

---

## CI/CD (GitHub Actions)

При push в ветку `master` деплой выполняется автоматически. Требуется настройка GitHub Secrets:

| Secret | Описание |
|--------|----------|
| `PROD_HOST` | IP или hostname сервера |
| `PROD_USER` | SSH-пользователь |
| `PROD_SSH_KEY` | Приватный SSH-ключ (полное содержимое) |

**Настройка:** Settings → Secrets and variables → Actions → New repository secret

Путь к проекту на сервере: `/var/www/urban-running-games` (при другом пути — отредактировать `.github/workflows/deploy.yml`).

---

## Проверка готовности к production

Перед сдачей проекта убедитесь:

- [ ] Все переменные из [ENV_CHECKLIST.md](ENV_CHECKLIST.md) заполнены
- [ ] HTTPS включён (Let's Encrypt), `APP_URL` с `https://`
- [ ] Turnstile-ключи заданы (формы обратной связи и заявок)
- [ ] Почта настроена (уведомления о заявках)
- [ ] Симлинк `public/storage` создан
- [ ] Права `storage/` и `bootstrap/cache/` — `www-data:www-data`
