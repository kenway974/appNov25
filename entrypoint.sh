#!/bin/sh

set -e

echo "Running migrations..."

php bin/console doctrine:migrations:migrate --no-interaction || true

echo "Starting PHP-FPM..."

php-fpm -D

echo "Starting Nginx..."

nginx -g "daemon off;"