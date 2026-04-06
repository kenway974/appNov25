#!/bin/sh

set -e

echo "Fix permissions (critical)..."

mkdir -p var/cache var/log
chown -R www-data:www-data var || true
chmod -R 777 var/cache var/log || true

echo "Clear cache..."
rm -rf var/cache/* || true

echo "Warmup cache..."
php bin/console cache:warmup --env=prod || true

echo "Run migrations..."
php bin/console doctrine:migrations:migrate --no-interaction || true

echo "Start PHP-FPM..."
php-fpm -D

echo "Start Nginx..."
nginx -g "daemon off;"