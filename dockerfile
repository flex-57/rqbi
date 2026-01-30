# Build
FROM php:8.2-fpm-alpine AS builder
RUN apk add --no-cache git unzip
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Production
FROM php:8.2-fpm-alpine AS prod
RUN docker-php-ext-install pdo_mysql opcache
COPY --from=builder /app/vendor /app/vendor
COPY . /app
