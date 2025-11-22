# CHANGE: Update to PHP 8.3 to satisfy Filament/Laravel requirements
FROM php:8.3-fpm

# Install system dependencies
# CHANGE: Added libicu-dev (required for the intl php extension)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    zip unzip nodejs npm default-mysql-client \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Fix git ownership issue (removes the "dubious ownership" error in logs)
RUN git config --global --add safe.directory /var/www

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]