#!/bin/sh
set -e

# Railway injects PORT.
sed -i "s/listen [0-9]*;/listen ${PORT:-8080};/" /etc/nginx/nginx.conf

# The container caches parameters (DB credentials come from env), so rebuild it on each start.
rm -rf app/cache/prod
su -s /bin/sh www-data -c "php app/console cache:warmup --env=prod --no-debug" \
    || echo "cache:warmup failed - continuing, Symfony will build the cache on first request"
chown -R www-data:www-data app/cache app/logs web/uploads

php-fpm -D
exec nginx -g 'daemon off;'
