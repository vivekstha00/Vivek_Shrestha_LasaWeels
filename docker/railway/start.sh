#!/bin/sh
set -eu

# Railway uses command overrides for pre-deploy migrations and cron services.
if [ "$#" -eq 1 ]; then
    exec /bin/sh -c "$1"
elif [ "$#" -gt 1 ]; then
    exec "$@"
fi

runtime_port="${PORT:-8080}"

sed -i "s/^Listen .*/Listen ${runtime_port}/" /etc/apache2/ports.conf
sed -i "s/__PORT__/${runtime_port}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p \
    /var/www/bootstrap/cache \
    /var/www/storage/app/public \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# PHP's Apache module requires prefork. Remove any conflicting MPM links that
# may have been enabled by the runtime environment before Apache starts.
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork >/dev/null

php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
