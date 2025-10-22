#!/bin/bash
set -e

# ======================================================
# Laravel Docker Skeleton Installer
# ======================================================
# This script builds and runs containers,
# installs a fresh Laravel app in ./src,
# and fixes directory permissions.
# ======================================================

echo "🚀 Building Docker images..."
docker compose --env-file .env.docker down
docker compose --env-file .env.docker build --no-cache app

echo "🧩 Starting containers..."
docker compose --env-file .env.docker up -d

echo "📦 Installing fresh Laravel inside container..."
docker compose exec app bash -c "
    cd /var/www/html &&
    if [ ! -f artisan ]; then
        composer create-project --prefer-dist laravel/laravel:^11.0 . &&
        echo '✅ Laravel installation complete.'
    else
        echo '⚠️ Laravel already installed, skipping installation.'
    fi
"

echo "🔧 Setting directory permissions..."
docker compose exec app bash -c "
    cd /var/www/html

    # Fix public directory
    if [ -d 'public' ]; then
        echo 'Setting public directory ownership and permissions...'
        chown -R www-data:www-data public
        chmod -R 775 public
    else
        echo 'Public directory does not exist! Please check your Laravel installation.'
    fi

    # Fix storage directory
    if [ -d 'storage' ]; then
        echo 'Setting storage directory permissions...'
        chown -R www-data:www-data storage
        chmod -R 775 storage

        mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache
        chown -R www-data:www-data storage/framework
        chmod -R 775 storage/framework
    else
        echo 'Storage directory not found. Skipping...'
    fi

    # Fix bootstrap/cache directory
    if [ -d 'bootstrap/cache' ]; then
        echo 'Setting bootstrap/cache permissions...'
        chown -R www-data:www-data bootstrap
        chmod -R 775 bootstrap
        chown -R www-data:www-data bootstrap/cache
        chmod -R 775 bootstrap/cache
    else
        echo 'Bootstrap/cache directory not found. Skipping...'
    fi
"

echo "🎉 Laravel Docker skeleton setup completed successfully!"
