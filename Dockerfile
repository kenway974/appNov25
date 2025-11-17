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

ENV APP_ENV=prod
ENV APP_DEBUG=0

# Préparation du cache Symfony (user=app pour ignorer lecture .env par symfony)
USER www-data
RUN php bin/console cache:clear --no-warmup && php bin/console cache:warmup
USER root


# PHP config
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

EXPOSE 80

CMD ["apache2-foreground"]
