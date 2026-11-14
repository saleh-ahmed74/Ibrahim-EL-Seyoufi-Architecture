# Production deployment

## Before release

1. Confirm the `production` branch and review its changes.
2. Back up the live MySQL database and uploaded images. Verify the backup can be restored before changing the schema.
3. Build a release with supported PHP, Composer, and Node versions:
   ```sh
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```
4. Keep the Laravel application directory outside the web root and point the domain document root at `backend/public` (or the deployed Laravel app's `public` directory). Never expose `.env`, `.git`, `vendor`, `storage`, `composer.json`, or `artisan`.
5. Create a dedicated MySQL database user with access only to this application database. Keep production credentials in the private environment file and never in Git.
6. Set `APP_ENV=production`, `APP_DEBUG=false`, a unique `APP_KEY`, the exact HTTPS `APP_URL`, secure session cookies, `DB_CONNECTION=mysql`, and `FILESYSTEM_DISK=public`.
7. Set `CACHE_STORE=file` or an independent Redis cache if the public site should serve cached portfolio content during a MySQL interruption. Ensure `storage/` and `bootstrap/cache/` are writable only by the PHP process.
8. Run these commands from the deployed application directory after confirming the target database and backup:
   ```sh
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   php artisan storage:link
   ```
   The migrations are additive and retain current content. Do not run `migrate:fresh` or `db:wipe` in production.
9. Create an admin account using `php artisan admin:create` from the server's trusted command runner. Do not add a public account-creation route.
10. Visit the home page, project listing, a project with internal projects, About, and all admin screens. Check one logo upload, one gallery upload, HTTPS, and offline fallback in the production domain.
11. Verify that `/.env`, `/.git/config`, `/vendor/`, `/storage/`, `/composer.json`, and `/artisan` return 403 or 404.

## Image and network handling

All admin images are validated and converted to WebP before they are stored on the public disk. Only resized image files are exposed to the website. The service worker only caches public GET pages and static assets; it bypasses admin routes and all non-GET requests.

The portfolio is read-only while offline. Admin edits, image uploads, and inquiry actions require a live connection. The offline page explains this and offers a manual refresh after reconnecting; it does not pretend to save changes that never reached the server.

## Recovery

Keep encrypted, off-server backups for the database and uploaded image directory. Practice restoring both to a separate environment before a major release. Keep logs and environment files private, disable directory listing, deny dotfiles, and do not use permissive `777` file permissions.
