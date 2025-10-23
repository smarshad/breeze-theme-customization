#!/bin/bash
set -e

# first check if port is already uses or not
ENV_FILE=".env.docker"

# Function: find next available port (only increments if port is actually in use)
find_next_free_port() {
    local port=$1
    # Check both docker and system-wide port usage
    while docker ps --format '{{.Ports}}' | grep -q ":$port->" || sudo lsof -i :$port >/dev/null 2>&1; do
        port=$((port + 1))
    done
    echo $port
}

# Function: update key=value in .env file
update_env_var() {
    local key=$1
    local value=$2
    if grep -q "^${key}=" "$ENV_FILE"; then
        sed -i "s/^${key}=.*/${key}=${value}/" "$ENV_FILE"
    else
        echo "${key}=${value}" >> "$ENV_FILE"
    fi
}

check_and_fix_port() {
    local key=$1
    local port
    port=$(grep "^${key}=" "$ENV_FILE" | cut -d '=' -f2)
    local new_port
    new_port=$(find_next_free_port "$port")

    if [ "$new_port" != "$port" ]; then
        echo "⚠️ Port $port is in use. Updating $key → $new_port"
        update_env_var "$key" "$new_port"
    else
        echo "✅ Port $port is free for $key"
    fi
}

echo "🔍 Checking ports in $ENV_FILE..."

# Check key ports
check_and_fix_port "NGINX_PORT"
check_and_fix_port "PHPMYADMIN_PORT"
check_and_fix_port "MYSQL_PORT"

echo "✅ Port check complete. Using available ports."


# Function: check if project name already exists in docker
check_project_name() {
    local project_name=$1
    if docker ps -a --format '{{.Names}}' | grep -qw "$project_name-app"; then
        echo "⚠️ Project '$project_name' already exists as a container."
        echo "   Please remove existing containers or use a different PROJECT_NAME."
        exit 1
    else
        echo "✅ Project name '$project_name' is available."
    fi
}

# Check project name
PROJECT_NAME=$(grep "^PROJECT_NAME=" "$ENV_FILE" | cut -d '=' -f2)
check_project_name "$PROJECT_NAME"

echo "✅ Port and project name check complete. Using available ports and project name."
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
