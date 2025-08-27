#!/bin/bash

echo "🚀 Building Padellers API for production..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Create SQLite database if it doesn't exist
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Set proper permissions
chmod 664 database/database.sqlite
chmod 775 database/

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Clear and cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

echo "✅ Build completed successfully!"