# Architect Portfolio

This application is a Laravel 12, Livewire 4, and MySQL portfolio site. The public website and the administrator area are rendered by the same Laravel application. The previous Next.js frontend and Filament panel have been removed.

## Requirements

- PHP 8.2 or newer with PDO MySQL and GD WebP support
- Composer
- MySQL 8.0+ or a compatible MySQL service
- Node.js 20+ and npm to compile the Vite assets

## Local setup

1. Install PHP dependencies with `composer install`.
2. Install frontend build tools with `npm ci`.
3. Copy `.env.example` to `.env`, create a dedicated MySQL database and user, and update the database values.
4. Generate an application key with `php artisan key:generate`.
5. Run `php artisan migrate --seed`. This creates or updates starter portfolio content. It does not reset the database.
6. Create an administrator from the command line with `php artisan admin:create`.
7. Link public image storage with `php artisan storage:link`.
8. Build assets with `npm run build`.
9. Start Laravel at `http://127.0.0.1:8585` with `php artisan serve --port=8585`.

The database seeder adds starter categories, settings, and projects only when those records do not already exist. It preserves existing site settings, projects, and categories. It does not insert a default administrator password. Do not seed a live database without a verified backup.

## Administrator

- Sign in at `/admin/login`.
- Manage projects and their internal projects at `/admin/projects`.
- Manage project categories at `/admin/categories`.
- Upload the site logo, home image, and profile photo at `/admin/settings`.
- Review inquiries and existing site statistics from the admin menu.

Create or reset an administrator only from a trusted terminal with `php artisan admin:create`. Public sign-up creates standard accounts and cannot grant administrator access.

## Data and migrations

The current MySQL schema remains the source of truth. New migrations add an optional `parent_id` to the existing `projects` table, a nullable logo path to `site_settings`, and an admin flag that defaults to false. Existing project, image, settings, user, inquiry, and analytics rows are retained. Grant admin access only to selected accounts through the trusted CLI command.

Back up the database and uploaded images before a production migration. Never use `migrate:fresh`, `db:wipe`, or destructive rollbacks on production.

## Production resilience

The public pages are rendered directly by Laravel, so browser page loads no longer depend on a separate Next.js or API server. Public content is cached for ten minutes with a previous-value fallback, and the default file cache can continue serving that saved content during a database interruption.

A service worker caches versioned assets and a bounded set of public pages already opened by the visitor. It never caches admin pages, Livewire requests, uploads, or form submissions. If a request takes longer than five seconds, the visitor receives a saved page when available or a clear offline page. On reconnect, the page can be refreshed explicitly.

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md). Serve only Laravel's `public/` directory. Keep `.env`, uploaded files, logs, `vendor/`, and `storage/` outside the public document root.

The implemented controls and host-owned launch decisions are recorded in [the production decisions log](reports/production-audit/production-decisions.md).
