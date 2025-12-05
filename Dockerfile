# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev && \
    docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer inside container
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set write permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose Render port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
