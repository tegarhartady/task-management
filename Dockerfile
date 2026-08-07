FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Remove default server definition
RUN rm -rf /var/www/html

# We don't copy the application code here yet to take advantage of docker cache for dependencies
# In a real production Dockerfile, you would COPY . /var/www and run composer install --no-dev
# But for local dev/hybrid, we map it via volumes in docker-compose.yml.

# Create system user to run Composer and Artisan Commands (optional, to avoid root permission issues)
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel
RUN mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www

USER root
CMD ["php-fpm"]
EXPOSE 9000
