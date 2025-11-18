########################################
# BASE VENDOR (Production)
########################################
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

########################################
# PROD
########################################
FROM php:8.2-apache AS prod

# Apache modules
RUN a2enmod rewrite

# System deps
RUN apt-get update && apt-get install -y \
        libzip-dev libicu-dev libpng-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copie proprio et fichiers
COPY --chown=www-data:www-data . .

# Copie les vendor depuis l’étape Composer
COPY --from=vendor /app/vendor ./vendor

# Permissions du dossier var
RUN mkdir -p var && chown -R www-data:www-data var

ARG APP_ENV=prod
ARG APP_DEBUG=0

ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_SECRET=9ff60f60664c5721f2f8e1559a8cec72db1cfef1dc0094cdda48b66be1b3b410
ENV DATABASE_URL="mysql://symfony:rapiderapide419420@db:3306/symfony?charset=utf8mb4"
ENV REDIS_URL="redis://redis:6379"

# PHP config
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Copier l'entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

