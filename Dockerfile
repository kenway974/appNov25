FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libssl-dev \
    pkg-config \
 && pecl install -n mongodb \
 && docker-php-ext-enable mongodb \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . .

RUN php -m | grep mongodb

CMD ["php-fpm"]
