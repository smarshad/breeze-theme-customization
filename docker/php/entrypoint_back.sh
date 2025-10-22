#!/bin/bash
set -e  # Exit on any error

echo "Starting Laravel application setup..."

# Change to the Laravel directory
cd /var/www/html

# Fix public directory permissions
if [ -d "public" ]; then
    echo "Setting public directory ownership and permissions..."
    chown -R www-data:www-data public
    chmod -R 775 public
else
    echo "Public directory does not exist! Please check your Laravel installation."
    exit 1
fi

# Fix storage directory permissions
if [ -d "storage" ]; then
    echo "Setting storage directory permissions..."
    chown -R www-data:www-data storage
    chmod -R 775 storage
    mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache
    chown -R www-data:www-data storage/framework
    chmod -R 775 storage/framework
fi

# Fix bootstrap/cache directory permissions
if [ -d "bootstrap/cache" ]; then
    chown -R www-data:www-data bootstrap
    chmod -R 775 bootstrap
    echo "Setting bootstrap/cache permissions..."
    chown -R www-data:www-data bootstrap/cache
    chmod -R 775 bootstrap/cache
fi

echo "Setup complete!"

# Execute the main command
exec "$@"
