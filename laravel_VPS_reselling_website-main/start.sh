#!/bin/sh
set -e

# Dynamically bind Apache to Render's $PORT (defaults to 80 if not set)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/*.conf

# Discover packages & upgrade filament components
php artisan package:discover --ansi
php artisan filament:upgrade

# Run database migrations automatically on startup (force for production)
php artisan migrate --force

# Run database seeders if enabled (seeders are idempotent, default true)
if [ "${RUN_DB_SEED:-true}" = "true" ]; then
    php artisan db:seed --force || true
fi

# Cache configurations for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions for storage and bootstrap cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start queue worker in background for free tier setups (can be disabled via RUN_QUEUE_WORKER=false)
if [ "${RUN_QUEUE_WORKER:-true}" = "true" ]; then
    echo "Starting background queue worker on database connection..."
    php artisan queue:work --sleep=3 --tries=3 --timeout=90 > /var/log/queue-worker.log 2>&1 &
fi

# Start the Apache server in the foreground
exec apache2-foreground

