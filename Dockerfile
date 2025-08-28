FROM php:8.3-cli

# Install system dependencies and PHP extensions including SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    zip \
    unzip \
    git \
    && docker-php-ext-install \
        pdo_sqlite \
        sqlite3 \
        pdo_mysql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to match DigitalOcean App Platform
WORKDIR /workspace

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create required directories and database file
RUN mkdir -p database storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && touch database/database.sqlite \
    && chmod 664 database/database.sqlite \
    && chmod 775 database/ \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Generate application key
RUN php artisan key:generate --force

# Run migrations and seeders
RUN php artisan migrate --force \
    && php artisan db:seed --force

# Cache configuration
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 8080

# Start the application
CMD php artisan serve --host=0.0.0.0 --port=8080