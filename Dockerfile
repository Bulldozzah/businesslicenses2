# Legacy Symfony 2.3 app - needs PHP 7.4 (fatals on PHP 8).
FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev libgmp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip gd gmp opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# Dev/debug front controllers must not be reachable in production.
RUN rm -f web/app_dev.php web/config.php web/app_latest.php \
    && mkdir -p app/cache app/logs web/uploads/documents \
    && chown -R www-data:www-data app/cache app/logs web/uploads \
    && chmod +x docker/entrypoint.sh

ENV SYMFONY_ENV=prod
CMD ["docker/entrypoint.sh"]
