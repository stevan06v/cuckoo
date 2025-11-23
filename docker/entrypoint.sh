#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "Creating .env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

echo "Waiting for database connection..."
while ! nc -z db 3306; do   
    sleep 1 
done 
echo "Database is ready. Starting application setup..."

chmod -R 777 storage bootstrap/cache

echo "Running key generation, migrations, and seeding..."

php artisan key:generate
php artisan config:clear
php artisan route:clear
php artisan cache:clear

php artisan migrate:fresh --force --seed

exec docker-php-entrypoint "$@"