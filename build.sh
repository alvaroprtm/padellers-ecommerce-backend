#!/bin/bash

echo "🚀 Building Padellers API for production..."

# Install SQLite if not available
if ! command -v sqlite3 &> /dev/null; then
    echo "📦 Installing SQLite..."
    apt-get update && apt-get install -y sqlite3 libsqlite3-dev
fi

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

<<<<<<< HEAD
=======
# Create SQLite database if it doesn't exist
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    echo "🗄️ Creating SQLite database..."
    touch database/database.sqlite
fi

# Set proper permissions
chmod 664 database/database.sqlite
chmod 775 database/

>>>>>>> 74bf878 (Fix SQLite setup for DigitalOcean App Platform)
# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration for production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Wait for database to be ready (PostgreSQL)
echo "⏳ Waiting for database..."
sleep 10

# Run database migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Run seeders
echo "🌱 Running seeders..."
php artisan db:seed --force

echo "✅ Build completed successfully!"
echo "📍 Database: $(pwd)/database/database.sqlite"