FROM php:8.2-apache

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
# Apache config
# =========================

# Activer rewrite (Symfony)
RUN a2enmod rewrite

RUN rm -f /etc/apache2/mods-enabled/mpm_* \
 && a2enmod mpm_prefork

# éviter warning Apache (obligatoire sur certains hosts)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Config Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

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

# =========================
# Runtime
# =========================
CMD ["apache2-foreground"]