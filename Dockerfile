FROM php:8.2-fpm

# =========================
# PHP + extensions
# =========================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libssl-dev \
    pkg-config \
    libicu-dev \
 && pecl install mongodb \
 && docker-php-ext-enable mongodb \
 && docker-php-ext-install intl opcache \
 && rm -rf /var/lib/apt/lists/*

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# =========================
# Install deps
# =========================
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# =========================
# Permissions Symfony
# =========================
RUN mkdir -p var/cache var/log \
 && chown -R www-data:www-data var vendor public