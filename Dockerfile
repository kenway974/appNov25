FROM php:8.2-fpm

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV DATABASE_URL="sqlite:///:memory:"

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev \
 && docker-php-ext-install intl opcache pdo pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

CMD ["php-fpm"]