#!/bin/bash
# ================================================================
# 🏥 MedClinic - Medical Clinic Management System
# Setup Script — Run this after cloning the repository
# ================================================================

set -e

echo "🏥 Setting up MedClinic..."

# 1. Install Composer dependencies
echo "📦 Installing PHP dependencies..."
composer install

# 2. Copy .env file
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Configure your .env (edit manually or use sed below)
echo "⚙️  Configure your .env file (DB, Mail, etc.)"

# 5. Run migrations + seed
echo "🗃️  Running migrations and seeders..."
php artisan migrate --seed

# 6. Create storage symlink
php artisan storage:link

# 7. Install npm dependencies
npm install && npm run build

echo "✅ Setup complete! Run: php artisan serve"
