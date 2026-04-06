FROM php:8.2-fpm

# ======================
# ENV (IMPORTANT)
# ======================
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ⚠️ NE PAS mettre sqlite ici !
# Railway injectera DATABASE_URL
ENV PORT=8080

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

WORKDIR /app
COPY . .

# ======================
# Frontend build
# ======================
RUN npm install && npm run build

# ======================
# PHP deps
# ======================
RUN composer install --no-dev --optimize-autoloader --no-scripts

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
# Permissions Symfony
# ======================
RUN mkdir -p var/cache var/log \
 && chown -R www-data:www-data var

EXPOSE 8080

CMD ["/entrypoint.sh"]