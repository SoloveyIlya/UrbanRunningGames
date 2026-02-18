# HTTPS: Let's Encrypt + автообновление

## Требования

- Домен указывает на IP сервера (A-запись)
- Nginx или Apache установлен и запущен
- Порты 80 и 443 открыты

## 1. Установка Certbot

### Ubuntu / Debian

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx   # для Nginx
# или
sudo apt install certbot python3-certbot-apache  # для Apache
```

## 2. Получение сертификата

### Nginx

```bash
# Убедитесь, что сайт доступен по HTTP (порт 80)
sudo certbot --nginx -d urban-running-games.ru -d www.urban-running-games.ru
```

### Apache

```bash
sudo certbot --apache -d urban-running-games.ru -d www.urban-running-games.ru
```

Следуйте инструкциям: введите email, примите условия, выберите редирект HTTP→HTTPS (рекомендуется).

## 3. Автообновление (auto-renew)

Certbot добавляет cron/systemd timer. Проверка:

```bash
sudo certbot renew --dry-run
```

### Ручная настройка cron (если нужно)

```bash
sudo crontab -e
```

Добавить строку (запуск каждый день в 3:00):

```
0 3 * * * certbot renew --quiet --post-hook "systemctl reload nginx"
```

Для Apache:

```
0 3 * * * certbot renew --quiet --post-hook "systemctl reload apache2"
```

## 4. Laravel

В `.env` на production:

```
APP_URL=https://urban-running-games.ru
```

Laravel 12 по умолчанию доверяет всем прокси. Если используется Cloudflare или другой reverse proxy, проверьте `bootstrap/app.php` (TrustProxies) или middleware.

## 5. Проверка

- https://urban-running-games.ru — открывается по HTTPS
- `sudo certbot certificates` — список сертификатов и даты истечения
