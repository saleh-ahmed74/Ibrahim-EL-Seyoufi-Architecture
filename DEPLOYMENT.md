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
4. Keep the Laravel application directory outside the web root and point the domain document root at the repository's `public/` directory. Never expose `.env`, `.git`, `vendor`, `storage`, `composer.json`, or `artisan`.
5. Create a dedicated MySQL database user with access only to this application database. Keep production credentials in the private environment file and never in Git.
6. Set `APP_ENV=production`, `APP_DEBUG=false`, a unique `APP_KEY`, the exact HTTPS `APP_URL`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `DB_CONNECTION=mysql`, and `FILESYSTEM_DISK=public`.
7. Use `CACHE_STORE=file` on one persistent application server to serve saved public content during a MySQL interruption. When running multiple app servers, use shared Redis for cache and rate limits, and shared object storage for uploaded files. Ensure `storage/` and `bootstrap/cache/` are writable only by the PHP process.
8. Keep `SESSION_DRIVER=database` for sessions shared through MySQL. `CACHE_STORE=file` is the single-server default and keeps cached public pages readable during a database interruption. Queue jobs run synchronously until a slow job is introduced; configure a managed worker before changing `QUEUE_CONNECTION` to an asynchronous driver.
9. Run these commands from the deployed application directory after confirming the target MySQL database and backup:
   ```sh
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   php artisan storage:link
   ```
   The migrations are additive and retain current content. Do not run `migrate:fresh` or `db:wipe` in production.
10. Create or promote an admin account using `php artisan admin:create` from the server's trusted command runner. Public sign-up creates non-admin accounts only. The web login is limited to five failed attempts per email and IP; authenticated admin pages are limited to 120 requests per user and IP per minute.
11. Visit the home page, project listing, a project with internal projects, About, and all admin screens. Check one logo upload, one gallery upload, HTTPS, and offline fallback in the production domain.
12. Verify that `/.env`, `/.git/config`, `/vendor/`, `/storage/`, `/composer.json`, and `/artisan` return 403 or 404.

## Image and network handling

All admin images are validated and converted to WebP before they are stored on the public disk. Only resized image files are exposed to the website. The service worker only caches public GET pages and static assets; it bypasses admin routes and all non-GET requests.

The portfolio is read-only while offline. Admin edits, image uploads, and inquiry actions require a live connection. The offline page explains this and offers a manual refresh after reconnecting; it does not pretend to save changes that never reached the server.

Livewire requests are not automatically repeated after a timeout or server error. For 401/403, 419, 429, network failures, and 5xx responses, the interface shows a recovery message; for uncertain writes, check the page state before submitting again. Login uses session authentication with CSRF, generic failure messages, session ID regeneration, and logout invalidation. Persistent “remember me” login is disabled.

For local development, the Laravel server uses port `8585`: `php artisan serve --port=8585`.

## Operational items requiring hosting-owner decisions

- Configure encrypted database and upload backups in off-server storage, then record the approved retention, RPO, and RTO after a restore drill.
- Configure an external error and uptime monitor and name its alert recipient; the application `/up` route is available as a health check.
- Measure representative traffic on staging before setting p95, availability, or capacity targets. No concurrency or performance guarantee is assumed here.
- Configure trusted proxy addresses at the hosting layer when the app is behind a load balancer. Do not trust arbitrary forwarded IP headers.
- Enable MFA for named administrator accounts before public launch, or have the site owner formally accept the temporary exception and set a review date. Public accounts cannot grant themselves administrator access; use the trusted CLI command to create or reset an admin.

Tests are configured for a separate MySQL database named `architecture_portfolio_test`; create it before running tests so no command can target the portfolio database by mistake.

## Recovery

Keep encrypted, off-server backups for the database and uploaded image directory. Practice restoring both to a separate environment before a major release. Keep logs and environment files private, disable directory listing, deny dotfiles, and do not use permissive `777` file permissions.
