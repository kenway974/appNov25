FROM php:8.2-fpm

# Variables Symfony (évite crash build)
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV DATABASE_URL="sqlite:///:memory:"
ENV PORT=8080

# Installer nginx + extensions PHP
RUN apt-get update && apt-get install -y \
    nginx \
    git unzip libicu-dev libzip-dev \
 && docker-php-ext-install intl opcache pdo pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copier config nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Permissions Symfony
RUN mkdir -p var/cache var/log \
 && chown -R www-data:www-data var

# Exposer port Railway
EXPOSE 8080

# Lancer PHP-FPM + Nginx
CMD sh -c "php-fpm & nginx -g 'daemon off;'"