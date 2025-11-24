# Stage 1
FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    zip unzip default-mysql-client netcat-openbsd \
    nodejs npm \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN git config --global --add safe.directory /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-progress --no-interaction

# Stage 2 Final small Image :))
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git netcat-openbsd \
    default-mysql-client \
    libicu-dev \
    libonig-dev \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \ 
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=builder /var/www /var/www
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]