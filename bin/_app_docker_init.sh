#!/bin/bash
set -e

echo "Starting Dev Environment...";

composer install --ansi --no-interaction

#php artisan config:cache
#php artisan route:cache
#php artisan view:cache

if [ ! -d ./public/storage ]; then
    php artisan storage:link
fi;
php artisan migrate

exec "php-fpm"

