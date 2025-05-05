#!/bin/bash

# Wait for MySQL
until nc -z -v -w30 db 3306
do
  echo "Waiting for database connection..."
  sleep 5
done

echo "Running migrations..."
php artisan migrate --seed --force

exec php-fpm
