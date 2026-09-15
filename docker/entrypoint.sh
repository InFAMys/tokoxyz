#!/usr/bin/env bash
set -e

php artisan storage:link > /dev/null 2>&1 || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
