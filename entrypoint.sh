#!/bin/sh

set -e

echo "Fix permissions..."
chown -R www-data:www-data var || true
chmod -R 775 var || true

echo "Clear cache..."
rm -rf var/cache/*
rm -rf var/log/*

echo "Warmup cache..."
php bin/console cache:warmup --env=prod || true

echo "Run migrations..."
php bin/console doctrine:migrations:migrate --no-interaction || true

echo "Start PHP-FPM..."
php-fpm -D

echo "Start Nginx..."
nginx -g "daemon off;"