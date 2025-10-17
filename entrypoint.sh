#!/bin/bash

set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --quiet

# Run migrations (optional; comment out if you want to run manually)
php artisan migrate --force

exec php-fpm
