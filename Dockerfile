########################################
# VENDOR
########################################
FROM composer:2 AS vendor

WORKDIR /app

# Copier seulement composer.* pour profiter du cache
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader



########################################
# CRON
########################################
COPY docker/crontab /etc/cron.d/symfony-cron
RUN chmod 0644 /etc/cron.d/symfony-cron \
    && crontab /etc/cron.d/symfony-cron



########################################
# DEV
########################################
FROM php:8.2-apache AS dev

RUN a2enmod rewrite

# Packages système (ajoute ceux dont tu as besoin)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libpng-dev libonig-dev \
    cron \
 && docker-php-ext-install pdo_mysql intl zip opcache \
 && rm -rf /var/lib/apt/lists/*


RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=vendor /app/vendor /var/www/html/vendor

COPY . /var/www/html

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html/var || true

ENV APP_ENV=dev
ENV APP_DEBUG=1

EXPOSE 80

CMD service cron start && apache2-foreground




########################################
# PROD
########################################

FROM php:8.2-apache AS prod

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev \
 && docker-php-ext-install pdo_mysql intl zip opcache \
 && rm -rf /var/lib/apt/lists/*

# Copier vendor depuis l'étape vendor
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copier le reste du projet
COPY . /var/www/html

WORKDIR /var/www/html

# Installer les dépendances en prod (optimise autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress || true

# Permissions
RUN chown -R www-data:www-data /var/www/html/var || true

ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 80

CMD service cron start && apache2-foreground

