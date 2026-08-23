# Deployment checklist

Run these steps on the server every time you deploy.

## 1. Install dependencies (production mode)
composer install --no-dev --optimize-autoloader

## 2. Environment
Copy .env.example to .env and set:
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql

## 3. Cache everything (main performance fix for Filament admin)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:optimize

## 4. Storage & assets
php artisan storage:link
npm run build

## 5. After any future code change
Re-run step 3 — cached config does not auto-update.
