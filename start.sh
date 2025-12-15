#!/bin/bash
set -e  # Stop on error

echo "🔹 Starting Laravel deployment setup for Render..."

# 1️⃣ Ensure .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env not found. Copying .env.example..."
    cp .env.example .env
fi

# 2️⃣ Wait for DB
echo "⏳ Waiting for database..."
until php -r "new PDO('pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "Database not ready yet. Sleeping 2 seconds..."
    sleep 2
done
echo "✅ Database is ready!"

# 3️⃣ Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 4️⃣ Generate app key (only if not set)
if ! php artisan key:generate --show | grep -q 'base64:'; then
    echo "🗝 Generating app key..."
    php artisan key:generate --ansi
fi

# 5️⃣ Run migrations
echo "🛠 Running migrations..."
php artisan migrate --force

# 6️⃣ Clear and cache configs
echo "🧹 Clearing and caching config, route, view..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7️⃣ Start Apache
echo "🚀 Starting Apache..."
apache2-foreground
