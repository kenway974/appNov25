FROM php:8.2-apache

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

# Activer mod_rewrite (important pour Symfony)
RUN a2enmod rewrite

# Configurer le dossier public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

RUN mkdir -p var \
 && chown -R www-data:www-data var vendor public
 
# Permissions Symfony
RUN chown -R www-data:www-data /var/www/html/var

CMD ["apache2-foreground"]