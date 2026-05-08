FROM php:8.2-fpm

# ======================
# ENV
# ======================
ENV APP_ENV=prod \
    APP_DEBUG=0 \
    PORT=8080 \
    APP_CACHE_DIR=/tmp/symfony/cache \
    APP_LOG_DIR=/tmp/symfony/log \
    APP_SESSION_DIR=/tmp/symfony/sessions

# ======================
# System deps
# ======================
RUN apt-get update && apt-get install -y \
    nginx \
    git unzip curl \
    libicu-dev libzip-dev \
    nodejs npm \
 && docker-php-ext-install intl opcache pdo pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# ======================
# Composer
# ======================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ======================
# App
# ======================
WORKDIR /app
COPY . .

# ======================
# Front build
# ======================
RUN npm install && npm run build

# ======================
# PHP deps
# ======================
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

# ======================
# Nginx config
# ======================
COPY nginx.conf /etc/nginx/nginx.conf

# ======================
# Entrypoint
# ======================
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ======================
# Port Railway
# ======================
EXPOSE 8080

CMD ["/entrypoint.sh"]