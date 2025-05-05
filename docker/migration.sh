#!/bin/sh

cd /patient-management

chown -R www-data:www-data /patient-management

# Generate Laravel application key
echo "Generating application key..."
php artisan key:generate

echo "Installing PHP dependencies..."
composer install

echo "Installing JS dependencies..."
npm install

echo "Building frontend assets..."
npm run build

# Wait for MySQL to be ready
until mysql -h "$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e 'show databases'; do
  echo "Waiting for database connection..."
  sleep 2
done

echo "Running database migrations and seeding..."
php artisan migrate:fresh --seed

echo "Starting PHP-FPM..."
exec php-fpm
