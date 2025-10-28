
git clone https://github.com/you/laravel-docker-skeleton.git
cd laravel-docker-skeleton
./install.sh

Insert into App container as interactive mode 
    docker exec -it breeze-theme-app /bin/bash

Insert into mysql Container
    docker exec -it admin-with-spatie-mysql mysql -u root -p

if install fresh laravel
    composer create-project --prefer-dist laravel/laravel:^11.0 .

Set up .env for Laravel:

cp .env.example .env

# For clear config and run migrations
    php artisan config:clear
    php artisan cache:clear
    php artisan migrate


# general git commands
    To discard all untracked files and folders
        git clean -fd
    Flags explained:
    -f → force (Git won’t delete anything unless you explicitly confirm)
    -d → include untracked directories ()

# general docker commands

Let's verify the configuration is working:
1. Check the full configuration to see if variables are properly set:
    docker compose --env-file .env.docker config

2. Or check specific services to see if the variables are interpolated:

    # Check container names
    docker compose --env-file .env.docker config | grep "container_name"

    # Check network configuration
    docker compose --env-file .env.docker config | grep "network"

    # Check ports
    docker compose --env-file .env.docker config | grep "ports"

3. Now start your containers:
    docker compose --env-file .env.docker up -d

4. Check if containers are running:
    docker ps

# Create new project from existing template

    Step 1: Create New Project Directory

    # You're already in ~/Downloads/laravel/projects/
    mkdir my-new-project
    cd my-new-project

Step 2: Copy Template Files (Without Git History)
    # Copy all files from breeze-theme except .git folder
    cp -r ../breeze-theme/* .
    cp -r ../breeze-theme/.* . 2>/dev/null || true

    # Remove the Git history from the copied project
    rm -rf .git

Step 3: Update Configuration for New Project
    # Update .env.docker with new project settings
    nano .env.docker

    Change these values in .env.docker:

    PROJECT_NAME=my-new-project
    NGINX_PORT=8081        # Different from breeze-theme (8080)
    MYSQL_PORT=3308        # Different from breeze-theme (3307)
    DB_DATABASE=my_new_project_db
    APP_NAME="My New Project"

    and in docker-compose.override.yml (two places)
     networks:
      - breeze-theme_network
    # Network connection
    networks:
      - breeze-theme_network

    Or use sed to automate:

    sed -i 's/PROJECT_NAME=breeze-theme/PROJECT_NAME=my-new-project/g' .env.docker
    sed -i 's/NGINX_PORT=8080/NGINX_PORT=8081/g' .env.docker
    sed -i 's/MYSQL_PORT=3307/MYSQL_PORT=3308/g' .env.docker
    sed -i 's/DB_DATABASE=quizjeeto/DB_DATABASE=my_new_project_db/g' .env.docker
    sed -i 's/APP_NAME=breeze-theme/APP_NAME="My New Project"/g' .env.docker

Step 4: Create Fresh Laravel Application (optional)
    # Remove the old Laravel code
    rm -rf src/*

    # Create new Laravel project
    docker run --rm -v $(pwd)/src:/app composer create-project laravel/laravel .

Step 5: Initialize New Git Repository

    # Initialize new Git repo
    git init
    git add .
    git commit -m "Initial commit: New Laravel project with Docker"

    # Create new GitHub repository and push
    gh repo create my-new-project --public --push

Let's Do It Now:

    # You're in ~/Downloads/laravel/projects/
    mkdir client-project
    cd client-project
    cp -r ../breeze-theme/* .
    cp -r ../breeze-theme/.* . 2>/dev/null || true
    rm -rf .git

    # Update configuration
    sed -i 's/PROJECT_NAME=breeze-theme/PROJECT_NAME=client-project/g' .env.docker
    sed -i 's/NGINX_PORT=8080/NGINX_PORT=8081/g' .env.docker
    sed -i 's/MYSQL_PORT=3307/MYSQL_PORT=3308/g' .env.docker
    sed -i 's/DB_DATABASE=quizjeeto/DB_DATABASE=client_project_db/g' .env.docker
    sed -i 's/APP_NAME=breeze-theme/APP_NAME="Client Project"/g' .env.docker

    # Fresh Laravel
    rm -rf src/*
    docker run --rm -v $(pwd)/src:/app composer create-project laravel/laravel .

    # Initialize Git
    git init
    git add .
    git commit -m "Initial commit: Client Project"

    # Create GitHub repo
    gh repo create client-project --public --push

# Rebuild
    docker compose --env-file .env.docker down
    docker compose --env-file .env.docker build --no-cache app
    docker compose --env-file .env.docker up -d


# Stop and Remove All Containers
    docker stop $(docker ps -aq)
    docker rm -f $(docker ps -aq)

# Remove All Images
    docker rmi -f $(docker images -q)

# Remove All Volumes
    docker volume rm $(docker volume ls -q)

# Remove All Networks (except default ones)
    docker network rm $(docker network ls -q)
    Note : This will fail for bridge, host, and none networks — that’s normal.
    
# Full Cleanup in One Command
    If you want to wipe Docker completely clean, this one command does it all
    docker system prune -a --volumes -f
    -a → removes all unused images, not just dangling ones
    --volumes → removes all volumes
    -f → skips confirmation prompt    

# Optional — Clean Everything Safely

    If you want to remove everything (containers, images, volumes, networks) only if they exist, use:

# Stop and remove all containers
    docker ps -aq | xargs -r docker stop
    docker ps -aq | xargs -r docker rm -f

# Remove all images
    docker images -q | xargs -r docker rmi -f

# Remove all volumes
    docker volume ls -q | xargs -r docker volume rm

# Remove all unused data (cleanup)
    docker system prune -a --volumes -f

# Start Stop Container
    docker start my_container_name
