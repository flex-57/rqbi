# ── Stage 1 : extensions PHP communes ────────────────────────────────────────
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
      bash git unzip \
      icu-dev libzip-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl zip gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# ── Stage 2 : dev (+ Xdebug) ─────────────────────────────────────────────────
FROM base AS dev

RUN apk add --no-cache linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/conf.d/app.dev.ini /usr/local/etc/php/conf.d/app.dev.ini
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

# ── Stage 3 : build assets (Node) ────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci

COPY vite.config.ts tailwind.config.js postcss.config.js ./
COPY assets/ assets/
COPY templates/ templates/

RUN npm run build

# ── Stage 4 : production PHP-FPM ─────────────────────────────────────────────
FROM base AS prod

ENV APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .
COPY --from=node-builder /app/public/build public/build/

COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN mkdir -p var/cache var/log public/uploads \
    && chown -R www-data:www-data var public/uploads

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

# ── Stage 5 : nginx prod ──────────────────────────────────────────────────────
FROM nginx:1.27-alpine AS nginx-prod

COPY docker/nginx/prod.conf /etc/nginx/conf.d/default.conf
COPY --from=prod /var/www/html/public /var/www/html/public
