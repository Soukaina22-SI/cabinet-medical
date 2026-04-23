# Dockerfile — Pour déploiement VPS ou Docker
FROM php:8.2-fpm-alpine

# Extensions PHP nécessaires
RUN apk add --no-cache \
        nginx \
        nodejs \
        npm \
        git \
        zip \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        gd \
        zip \
        opcache \
        bcmath

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force && php artisan storage:link && php-fpm -D && nginx -g 'daemon off;'"]
