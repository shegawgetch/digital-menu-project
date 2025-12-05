# Stop on error
$ErrorActionPreference = "Stop"

Write-Host "🔹 Starting Laravel deployment setup for Render..."

# 1️⃣ Ensure .env exists
if (!(Test-Path ".env")) {
    Write-Host "⚠️  .env not found. Copying .env.example..."
    Copy-Item ".env.example" ".env"
}

# 2️⃣ Install PHP dependencies
Write-Host "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3️⃣ Generate app key if not exists
Write-Host "🗝 Generating app key..."
php artisan key:generate --ansi

# 4️⃣ Run migrations
Write-Host "🛠 Running migrations..."
php artisan migrate --force

# 5️⃣ Cache configs for performance
Write-Host "🧹 Clearing and caching config, route, view..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6️⃣ Build frontend assets if package.json exists
if (Test-Path "package.json") {
    Write-Host "💻 Installing and building frontend assets..."
    npm install
    npm run build
}

Write-Host "✅ Laravel deployment setup complete!"
