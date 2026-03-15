#!/bin/sh
set -e

echo "==> Starting SE AI Project..."

# Cache Laravel config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Wait for MySQL to be ready (extra safety)
echo "==> Waiting for database..."
until php artisan db:monitor --databases=mysql --max=5 2>/dev/null; do
    sleep 2
done

# Run migrations automatically
echo "==> Running migrations..."
php artisan migrate --force --no-interaction
db:seed --class=InitialTestDataSeeder --force --no-interaction

# Fix permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "==> Launching services..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf