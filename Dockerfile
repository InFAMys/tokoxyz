# ---- build assets ----
FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# ---- runtime: php-fpm + nginx + supervisor ----
FROM php:8.4-fpm
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev \
        libicu-dev libcurl4-openssl-dev default-mysql-client unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip intl bcmath exif \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY --from=assets /app/public/build ./public/build
COPY . .

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && composer dump-autoload --optimize

EXPOSE 80
CMD ["/usr/local/bin/entrypoint.sh"]
