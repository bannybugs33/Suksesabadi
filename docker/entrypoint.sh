#!/bin/sh
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
  echo "ERROR: APP_KEY belum diisi. Buat dengan: php artisan key:generate --show" >&2
  exit 1
fi

DB_FILE="${DB_DATABASE:-/data/database.sqlite}"

# Siapkan folder & izin (volume Docker baru biasanya dimiliki root)
mkdir -p /data storage/framework/cache storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public bootstrap/cache
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  touch "$DB_FILE"
fi
chown -R www-data:www-data /data storage bootstrap/cache

artisan() { su -s /bin/sh www-data -c "php artisan $*"; }

artisan package:discover --ansi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  artisan migrate --force
fi

# Jangan aktifkan di produksi nyata: seeder membuat akun dengan password "password"
if [ "${RUN_SEED:-false}" = "true" ]; then
  artisan db:seed --force
fi

artisan config:cache
artisan route:cache
artisan view:cache

exec "$@"
