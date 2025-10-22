FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libfreetype6-dev \
    zip unzip git curl

# Configure GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath opcache

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Get current user/group ID from build args
ARG USER_ID=1000
ARG GROUP_ID=1000

# Modify www-data user to match host user ID and set home directory
RUN groupmod -g ${GROUP_ID} www-data && \
    usermod -u ${USER_ID} -g ${GROUP_ID} -d /home/www-data www-data && \
    mkdir -p /home/www-data && \
    chown www-data:www-data /home/www-data

WORKDIR /var/www/html

# Create bash aliases
RUN echo "alias ll='ls -alF'" >> /root/.bashrc && \
    echo "alias ll='ls -alF'" >> /home/www-data/.bashrc && \
    chown www-data:www-data /home/www-data/.bashrc

# Ensure bash is the default shell for www-data
RUN usermod -s /bin/bash www-data

# Copy entrypoint script
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy custom PHP-FPM pool configuration
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Set entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Default command
CMD ["php-fpm"]