FROM php:8.2-fpm AS builder

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install --omit=dev \
    && npm run build \
    && rm -rf node_modules

FROM php:8.2-fpm-alpine

RUN apk --no-cache add libpng libjpeg libzip libfreetype

RUN docker-php-ext-install gd pdo pdo_mysql zip

WORKDIR /var/www

COPY --from=builder /var/www /var/www

RUN addgroup -g 1000 appgroup && adduser -u 1000 -G appgroup -s /bin/sh -D appuser
USER appuser

EXPOSE 9000

CMD ["php-fpm"]
