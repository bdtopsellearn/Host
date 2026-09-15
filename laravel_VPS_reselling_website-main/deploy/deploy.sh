#!/usr/bin/env bash
# ==============================================================================
# VortexCloud 1-Click Zero-Downtime Code Update & Deploy Script
# ==============================================================================

set -e

echo "[*] Deploying latest updates for VortexCloud..."

# 1. Put application into maintenance mode with 15s retry-after header
php artisan down --render="errors::503" --retry=15 || true

# 2. Pull latest code (if using git on server)
# git pull origin main

# 3. Install production composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Run database migrations
php artisan migrate --force

# 5. Clear and cache Laravel config, routes, and views
php artisan optimize

# 6. Signal Supervisor queue workers to gracefully reload new code
php artisan queue:restart

# 7. Bring application out of maintenance mode
php artisan up

echo "[✓] Deployment completed successfully!"
