FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libssl-dev \
    pkg-config \
    libicu-dev \
 && pecl install -n mongodb \
 && docker-php-ext-enable mongodb \
 && docker-php-ext-install intl opcache \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

CMD ["php-fpm"]
