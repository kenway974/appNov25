#!/bin/sh
set -e

echo "=============================="
echo " Symfony Railway Entrypoint"
echo "=============================="

echo "Checking Symfony installation..."

if [ ! -f vendor/autoload.php ]; then
  echo "❌ FATAL: vendor/autoload.php missing"
  exit 1
fi

if [ ! -d vendor/symfony/runtime ]; then
  echo "❌ FATAL: symfony/runtime missing"
  exit 1
fi

mkdir -p "$APP_CACHE_DIR" "$APP_LOG_DIR" "$APP_SESSION_DIR"
chmod -R 777 /tmp/symfony

echo "Warming Symfony cache..."
php bin/console cache:warmup --env=prod

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

php -v
php -r "echo is_dir('vendor/symfony/runtime') ? 'OK runtime' : 'MISSING runtime'; echo PHP_EOL;"

php-fpm -D
nginx -g "daemon off;"