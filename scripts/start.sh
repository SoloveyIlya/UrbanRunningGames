#!/usr/bin/env bash
# Запуск сайта в режиме разработки (Linux / macOS)
# Использование: из корня проекта — ./scripts/start.sh

set -e
cd "$(dirname "$0")/.."

if ! command -v php &> /dev/null; then
  echo "Ошибка: PHP не найден. Установите PHP 8.4 и добавьте в PATH."
  exit 1
fi
if ! command -v composer &> /dev/null; then
  echo "Ошибка: Composer не найден. Установите Composer и добавьте в PATH."
  exit 1
fi
if ! command -v node &> /dev/null; then
  echo "Ошибка: Node.js не найден. Установите Node.js и добавьте в PATH."
  exit 1
fi

echo "Запуск: PHP-сервер, очередь, Vite..."
npm run start
