#!/bin/sh
set -e

# Attendre que MySQL soit prêt
until nc -z -v -w30 db 3306
do
  echo "Waiting for database..."
  sleep 1
done

# Clear & warmup cache en prod
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup
APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup

# Lancer PHP-FPM
php-fpm
#!/bin/sh
set -e

# Attendre que MySQL soit prêt
until nc -z -v -w30 db 3306
do
  echo "Waiting for database..."
  sleep 1
done

# Clear & warmup cache en prod
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup
APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup

# Lancer Apache en foreground
exec apache2-foreground
