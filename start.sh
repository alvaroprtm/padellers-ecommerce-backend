#!/bin/bash

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run database migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application
if [ -f "Dockerfile" ]; then
    # If using Docker, it will handle the server
    exec "$@"
else
    # If using Railway's auto-deploy
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
fi