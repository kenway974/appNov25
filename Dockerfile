########################################
# VENDOR
########################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction

########################################
# DEV
########################################
FROM php:8.2-fpm AS dev

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev \
 && docker-php-ext-install pdo_mysql intl zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

# DEV settings
RUN echo "opcache.enable=0" > /usr/local/etc/php/conf.d/opcache.ini

CMD ["php-fpm"]

########################################
# PROD
########################################
FROM php:8.2-fpm AS prod

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev \
 && docker-php-ext-install pdo_mysql intl zip opcache

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

# PROD settings
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
 && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup \
 && APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup

CMD ["php-fpm"]
