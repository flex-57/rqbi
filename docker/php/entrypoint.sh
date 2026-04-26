#!/bin/sh
set -e

# En dev : installer les dépendances Composer (pas en prod, elles sont dans l'image)
if [ "${APP_ENV:-dev}" = "dev" ]; then
    composer install --no-interaction --prefer-dist
fi

# Générer les clés JWT si absentes
if [ ! -f config/jwt/private.pem ]; then
    mkdir -p config/jwt
    php bin/console lexik:jwt:generate-keypair --overwrite --quiet
fi

# Warmup cache (prod uniquement)
if [ "${APP_ENV}" = "prod" ]; then
    php bin/console cache:warmup
fi

# Migrations
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"
