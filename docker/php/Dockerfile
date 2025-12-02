# Base image
FROM php:8.3-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl build-essential pkg-config \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    libssl-dev zlib1g-dev libcurl4-openssl-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl xml dom \
    && pecl install redis swoole \
    && docker-php-ext-enable redis swoole \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy all files first (for dev, volumes will overwrite anyway)
COPY . .

# Install PHP dependencies (inside container)
RUN composer install --no-interaction --optimize-autoloader --ignore-platform-reqs

# Set permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Install chokidar globally
RUN npm install -g chokidar-cli

# Expose Octane port
EXPOSE 8000

# Start Laravel Octane with Swoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--watch"]
