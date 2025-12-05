# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev pkg-config build-essential && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_pgsql zip gd mbstring && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer inside container
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set write permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose the default Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
