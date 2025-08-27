#!/bin/bash

# Set proper permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear and cache configuration
php artisan config:clear
php artisan config:cache

# Run database migrations (only if DB is available)
if [ "$DB_CONNECTION" != "" ]; then
    php artisan migrate --force || echo "Migration failed or no database configured"
fi

# Cache routes and views (optional, can fail safely)
php artisan route:cache || echo "Route caching failed"
php artisan view:cache || echo "View caching failed"

# Start Apache
exec apache2-foreground