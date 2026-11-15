# Ibrahim El-Seyoufi — Architecture Portfolio

A responsive architecture portfolio and content management system built with Laravel 12, Livewire 4, Blade, MySQL, and Vite. The public website and admin area run in one Laravel application; there is no Next.js frontend, Node.js application server, or Filament dependency. Node.js is used only to compile the CSS and JavaScript assets.

## Contents

- [What the application does](#what-the-application-does)
- [Technology and requirements](#technology-and-requirements)
- [Application structure](#application-structure)
- [Public pages and routes](#public-pages-and-routes)
- [Admin area](#admin-area)
- [Data model](#data-model)
- [Images and uploads](#images-and-uploads)
- [Caching, offline behavior, and traffic](#caching-offline-behavior-and-traffic)
- [Security](#security)
- [Local development](#local-development)
- [Tests and build](#tests-and-build)
- [Production deployment](#production-deployment)
- [Operations and troubleshooting](#operations-and-troubleshooting)
- [Project documents](#project-documents)

## What the application does

- Presents a bilingual (English/Arabic) architecture portfolio, project details, practice information, and contact entry points.
- Supports project categories and parent projects with nested projects, cover images, galleries, and featured project highlights.
- Shows featured projects on the home page, with an all-projects link to the complete listing.
- Provides project and image pagination, category filtering, and a category menu that loads its category list when opened.
- Includes an administrator area for projects, categories, site and home-page copy, inquiries, and analytics.
- Lets administrators update site text and upload a logo, hero/profile image, category images, project covers, and gallery images.
- Provides responsive gallery browsing with touch/pointer interactions, image zoom, and a public offline fallback.

The public site uses a dark appearance; the admin interface defaults to a light appearance and offers a theme switch. The home-page copy is editable in Store Settings and supports English and Arabic values.

## Technology and requirements

| Component | Technology |
| --- | --- |
| Server application | PHP 8.2+, Laravel 12 |
| Interactive pages | Livewire 4, Blade |
| Database | MySQL (MySQL 8 recommended) |
| Styles and browser scripts | CSS and JavaScript bundled with Vite |
| Image conversion | PHP GD with WebP support |
| PHP dependencies | Composer |
| Asset build tools | Node.js and npm (Node 20+ recommended) |

Install PHP extensions required by Laravel, including PDO MySQL, fileinfo, mbstring, OpenSSL, tokenizer, XML, ctype, JSON, and GD/WebP. The host must allow writes to `storage/` and `bootstrap/cache/`.

## Application structure

```text
app/
  Http/                 Middleware, controllers, and request handling
  Livewire/             Public, authentication, and admin page components
  Models/               Eloquent models
  Support/               Public content caching, copy defaults, image processing
bootstrap/               Application and middleware configuration
config/                  Laravel configuration
 database/
  factories/             Model factories
  migrations/            Additive schema changes
  seeders/                Safe starter data
public/                   Web document root, offline page, service worker
resources/
  css/                    Application styles
  js/                     Browser interactions
  views/                  Blade and Livewire templates
routes/web.php            Named public, account, and admin routes
tests/                    PHPUnit/Pest feature and unit tests
```

## Public pages and routes

| URL | Purpose |
| --- | --- |
| `/` | Home page and featured projects |
| `/projects` | Paginated project listing; optional category filter |
| `/projects/{slug}` | Project details, nested projects, and paginated gallery |
| `/about` | Practice/about page |
| `/login`, `/register` | Standard account access |
| `/forgot-password`, `/reset-password/{token}` | Password recovery |
| `/verify-email`, `/email/verify/{id}/{hash}` | Email verification |
| `/account/password` | Authenticated password change |

Project/category data is queried through Laravel/Eloquent. The category menu initially renders without requesting the category list; opening it fetches the compact list, and selecting a category loads the corresponding paginated projects.

## Admin area

Sign in at `/admin/login`. Authenticated administrators can use:

| URL | Purpose |
| --- | --- |
| `/admin` | Dashboard |
| `/admin/projects` | Create, edit, feature, reorder, and remove projects; manage galleries and nested projects |
| `/admin/categories` | Create and edit categories |
| `/admin/settings` | Site details, images, social links, SEO, and editable home-page copy |
| `/admin/inquiries` | Review contact inquiries |
| `/admin/analytics` | View recorded site statistics |

Public registration creates non-admin accounts. Administrator access is granted only through the trusted CLI command `php artisan admin:create`; do not expose administrative credentials in source control. The admin UI reports database action outcomes with dismissible notices.

## Data model

The database is the source of truth. Migrations are additive and preserve existing content.

- `users`: standard accounts plus a boolean admin authorization flag.
- `categories`: active/sortable project categories and optional image paths.
- `projects`: title, slug, description, category, location, year, sort order, optional parent project, and `is_featured` (false by default). Soft deletes are used.
- `project_images`: ordered project gallery entries and cover-image designation.
- `site_settings`: site/practice copy, contact and social links, SEO values, image paths, and bilingual `homepage_copy` JSON.
- Inquiry and analytics records: stored for the corresponding admin pages.

Run migrations after deploying new code. Back up the database and uploaded images first; never use `migrate:fresh` or `db:wipe` against production.

## Images and uploads

Image files are validated and converted to WebP by `App\Support\ImageCompressor` before being saved on the configured public disk. The helper caps the default longest edge at 1920px and uses configurable quality at the call site. It rejects unsupported formats and images above its pixel limit. PNG/WebP alpha is retained when the source has transparency; opaque backgrounds are preserved. This processing does not remove a photo's background.

The normal public storage setup is:

```sh
php artisan storage:link
```

Keep the original uploads in the configured storage disk and ensure that disk is backed up with the database. Prefer appropriately sized assets and verify the PHP upload limits (`upload_max_filesize`, `post_max_size`, and memory limit) on the host.

## Caching, offline behavior, and traffic

- `App\Support\PublicContent` caches public settings, menus, projects, and paginated project/gallery data for ten minutes. Admin content changes invalidate the public-content version so subsequent requests see updates.
- When a content lookup fails, the helper returns a previously cached value when available, otherwise a safe empty/default value. Configure a persistent cache store; the deployment guide recommends the file store for one server and shared Redis for multiple servers.
- The service worker caches versioned static assets and a bounded set of visited public pages. It bypasses admin pages, Livewire requests, uploads, and non-GET/form submissions. A slow or unavailable request can show a cached page or the offline page; edits and forms still require a live connection.
- Project listings, nested-project listings, and galleries use pagination so the browser does not need every project or image at once.
- Login and authenticated admin requests have rate limits. These mechanisms improve resilience but are not a substitute for measuring actual peak traffic or provisioning the database, PHP workers, cache, and storage for expected load.

When releasing a new service-worker build, the cache version is updated in `public/service-worker.js` to retire older cached assets.

## Security

- Keep `.env`, credentials, logs, source control metadata, and application internals outside the public document root.
- Set production `APP_DEBUG=false`, use HTTPS and secure session cookies, and keep `APP_KEY` private and stable.
- Use Laravel session authentication, CSRF protection, authorization middleware for admin routes, input validation, and rate limiting.
- Create or reset admin accounts from a trusted command line, not public registration.
- Do not cache admin pages, Livewire updates, uploads, or form submissions in the service worker.
- Restrict database credentials to the application database and use least-privilege file permissions.

## Local development

1. Install dependencies:

   ```sh
   composer install
   npm ci
   ```

2. Create `.env` from `.env.example`, create a local MySQL database and user, and set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. The provided sample targets database `architecture_portfolio`.
3. Generate the app key and initialize the database/storage:

   ```sh
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

   Seeders add starter records without resetting existing projects, categories, or settings. They do not create an administrator.

4. Create an admin account from a trusted terminal:

   ```sh
   php artisan admin:create
   ```

5. Build assets and run the app:

   ```sh
   npm run build
   php artisan serve --port=8585
   ```

   Browse to `http://127.0.0.1:8585`. During active UI work, `npm run dev` serves Vite assets with hot reload. `composer run dev` starts the Laravel server, logs, and Vite together when the required dependencies are installed.

Important `.env` values include `APP_URL`, `APP_ENV`, `APP_DEBUG`, `DB_*`, `SESSION_*`, `FILESYSTEM_DISK`, `CACHE_STORE`, and the mail settings. Never commit a real `.env` file.

## Tests and build

Build the production browser assets with:

```sh
npm run build
```

Run the application test suite with:

```sh
php artisan test
```

Tests are forced to use MySQL database `architecture_portfolio_test` by `phpunit.xml`. Create that dedicated database and grant the test user access before running tests. Do not point test configuration to the live portfolio database. Tests may create and drop tables in the test database.

Useful checks:

```sh
php artisan route:list
php artisan migrate:status
php artisan about
```

## Production deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for the release checklist, security configuration, migration guidance, offline behavior, and operational decisions. Key points:

1. Back up the production database and uploaded images and confirm the backup is restorable.
2. Install production Composer dependencies and build Vite assets (`composer install --no-dev --optimize-autoloader`, `npm ci`, `npm run build`).
3. Set the web document root to this project's `public/` directory only.
4. Configure private production environment values, HTTPS, database access, storage, and a persistent/shared cache appropriate to the number of application servers.
5. Run `php artisan migrate --force` only after confirming the target database and backup.
6. Run Laravel config/route/view/event caches, create the storage link, and smoke-check public, account, and admin routes.
7. Verify sensitive paths are not publicly served and test the offline fallback on the real HTTPS domain.

The application exposes Laravel's `/up` health endpoint. Availability targets, monitoring recipients, backup retention, recovery objectives, and capacity limits need to be set by the hosting owner; see `DEPLOYMENT.md`.

## Operations and troubleshooting

- **Database connection errors:** confirm the selected database exists, MySQL is reachable from PHP, and `.env` credentials are correct; clear cached configuration after environment changes with `php artisan config:clear`.
- **Missing uploaded images:** verify the `public` disk configuration, `php artisan storage:link`, and read permissions on `storage/app/public`.
- **Changes not appearing immediately:** admin saves invalidate the public cache; check the save notice, database connection, and cache store. Clear Laravel's cache only when diagnosing stale state.
- **Assets look outdated:** run `npm run build` and deploy the generated `public/build` directory, then refresh the page/service worker.
- **Tests cannot connect:** create `architecture_portfolio_test`; verify the test user's MySQL privileges. This name is deliberately separate from the default app database.
- **Offline page appears:** reconnect and refresh; offline mode is read-only and does not queue forms or admin changes.
- **Image upload fails:** check allowed image formats, PHP upload/memory limits, GD WebP support, storage writability, and available disk space.

## Project documents

- [Production deployment guide](DEPLOYMENT.md)
- [Product requirements](prd.md)
- [Production audit decisions](reports/production-audit/production-decisions.md)
