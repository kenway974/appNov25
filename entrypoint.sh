#!/bin/sh
set -e

echo "=============================="
echo " Symfony Railway Entrypoint"
echo "=============================="

# ======================
# Safety check vendor
# ======================
echo "Checking Symfony installation..."

if [ ! -f vendor/autoload.php ]; then
  echo "❌ FATAL: vendor/autoload.php missing"
  exit 1
fi

if [ ! -d vendor/symfony/runtime ]; then
  echo "❌ FATAL: symfony/runtime missing"
  exit 1
fi

# ======================
# Setup writable dirs
# ======================
echo "Creating writable directories in /tmp..."

mkdir -p "$APP_CACHE_DIR" "$APP_LOG_DIR" "$APP_SESSION_DIR"
chmod -R 777 /tmp/symfony

# ======================
# Cache clean + warmup
# ======================
echo "Clearing cache..."
rm -rf "$APP_CACHE_DIR"/* || true

echo "Warming up cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# ======================
# Debug info
# ======================
echo "=============================="
echo " SYMFONY DEBUG CHECK"
echo "=============================="

php -v

echo "Vendor check:"
ls -la vendor

echo "Runtime check:"
ls -la vendor/symfony/runtime

echo "Autoload check:"
ls -la vendor/autoload.php

php -r "echo is_dir('vendor/symfony/runtime') ? 'OK runtime' : 'MISSING runtime'; echo PHP_EOL;"

# ======================
# Migrations (IMPORTANT: fail fast)
# ======================
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# ======================
# Start services
# ======================
echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"