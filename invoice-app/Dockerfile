FROM php:8.2-fpm-alpine

COPY docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    build-base \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    mariadb-client \
    bash \
    tzdata \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd

RUN apk del build-base

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

EXPOSE 9000
