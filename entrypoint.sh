#!/bin/sh
set -e

echo "=============================="
echo " Symfony Railway Entrypoint"
echo "=============================="

# ======================
# Setup dossiers writable
# ======================
echo "Creating writable directories in /tmp..."

mkdir -p $APP_CACHE_DIR
mkdir -p $APP_LOG_DIR
mkdir -p $APP_SESSION_DIR

# Permissions safe (pas besoin de 777 global)
chmod -R 777 /tmp/symfony

# ======================
# Nettoyage cache
# ======================
echo "Clearing cache..."
rm -rf $APP_CACHE_DIR/* || true

# ======================
# Warmup Symfony
# ======================
echo "Warming up cache..."
php bin/console cache:warmup --env=prod || true

echo "=============================="
echo " SYMFONY DEBUG CHECK"
echo "=============================="

echo "PHP version:"
php -v

echo "Composer vendor check:"
ls -la vendor || echo "❌ vendor missing"

echo "Symfony runtime check:"
ls -la vendor/symfony/runtime || echo "❌ runtime missing"

echo "Autoload check:"
ls -la vendor/autoload.php || echo "❌ autoload missing"

php -r "echo is_dir('vendor/symfony/runtime') ? 'OK runtime' : 'MISSING runtime'; echo PHP_EOL;"

# ======================
# Migrations (optionnel mais pratique)
# ======================
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction || true

# ======================
# Start services
# ======================
echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"