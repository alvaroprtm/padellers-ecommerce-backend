#!/bin/bash

echo "🚀 Building Padellers API for production..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

# Clear and cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Wait for database to be ready (PostgreSQL)
echo "⏳ Waiting for database..."
sleep 10

# Run database migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

echo "✅ Build completed successfully!"