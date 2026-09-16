# Legacy Symfony 2.3 app - needs PHP 7.4 (fatals on PHP 8).
# Alpine base: the Debian 11 (bullseye) php:7.4 images can no longer apt-get their -dev packages.
FROM php:7.4-fpm-alpine3.16

RUN apk add --no-cache nginx icu-libs libzip libpng libjpeg-turbo freetype gmp gnu-libiconv \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev gmp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip gd gmp opcache \
    && apk del .build-deps

# musl's iconv cannot transliterate; GNU libiconv keeps slug/translit code working.
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html
COPY . .

# Dev/debug front controllers must not be reachable in production.
RUN rm -f web/app_dev.php web/config.php web/app_latest.php \
    && mkdir -p app/cache app/logs web/uploads/documents \
    && chown -R www-data:www-data app/cache app/logs web/uploads \
    && chmod +x docker/entrypoint.sh

ENV SYMFONY_ENV=prod
CMD ["docker/entrypoint.sh"]
