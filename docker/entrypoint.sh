#!/bin/sh
set -e

# Railway injects PORT; Apache defaults to 80.
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# The container caches parameters (DB credentials come from env), so rebuild it on each start.
rm -rf app/cache/prod
su -s /bin/sh www-data -c "php app/console cache:warmup --env=prod --no-debug" || echo "cache:warmup failed - continuing, Symfony will build the cache on first request"
chown -R www-data:www-data app/cache app/logs web/uploads

exec apache2-foreground
