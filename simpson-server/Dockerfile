## Dockerfile for a PHP install with the PGSQL extension
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www

COPY . .
COPY .env.example .env

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN composer install

# This is needed when using nginx
#CMD ["php-fpm"]
#CMD ["bash", "-c", "php artisan migrate && php artisan serve --host=0.0.0.0 --port=8080"]
