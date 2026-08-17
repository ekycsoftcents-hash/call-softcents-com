#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting Update Process..."

# 1. Pull latest changes (optional, uncomment if using Git)
# git pull origin main

# 2. Build and Restart Containers
echo "📦 Building and restarting containers..."
docker compose up -d --build

# 3. Install PHP Dependencies
echo "🐘 Installing Composer dependencies..."
docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# 4. Run Database Migrations
echo "🗄️ Running migrations..."
docker compose exec app php artisan migrate --force

# 5. Clear and Cache Config/Routes/Views
echo "⚡ Optimizing Laravel..."
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize

# 6. Restart Background Services (Supervisor)
echo "🔄 Restarting workers..."
docker compose exec worker supervisorctl restart all

# 7. Frontend Assets (Optional - uncomment if you want to build inside container)
# echo "🎨 Building frontend assets..."
# docker compose exec app npm install
# docker compose exec app npm run build

echo "✅ Update Complete!"
