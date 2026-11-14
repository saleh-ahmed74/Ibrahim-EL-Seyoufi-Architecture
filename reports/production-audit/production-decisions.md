# Production decisions for the architecture portfolio

Reviewed against the transferable production decision template on 2026-11-14. The template is guidance; these are the decisions that fit this portfolio and its current hosting information.

## Applied in the application

| Area | Project decision | Implementation |
|---|---|---|
| Database | MySQL only | MySQL is the application and test connection. SQLite connection configuration and local SQLite databases are removed. Tests target a separate `architecture_portfolio_test` database. |
| Browser authentication | Laravel web sessions, cookies, and CSRF | Admin accounts are created or reset only by `php artisan admin:create`; there is no public registration or API token flow. Session IDs rotate at login, logout invalidates the session and CSRF token, and session payloads are encrypted. |
| Login persistence | No persistent remember-me cookie | Admin sessions use the configured idle lifetime instead of extending via a long-lived recaller cookie. |
| Authorization | Server-side administrator flag | Admin routes and every Livewire admin component check `is_admin`. Login errors do not reveal whether an account exists. |
| Rate limits | Separate login and admin limits | Five failed sign-in attempts per normalized email and IP per minute; login route requests are limited to 30 per IP per minute; admin requests are limited to 120 per user and IP per minute. Laravel returns 429 responses and `Retry-After`. |
| Session and cache stores | MySQL sessions; file cache for one persistent app server | Sessions are shared through MySQL. File cache keeps the last public portfolio snapshot available during a database outage. The default file cache and rate limits are local to one app server. |
| Background jobs | Synchronous | No slow external jobs currently exist. Add a managed worker before switching to an asynchronous queue. |
| Retries and offline use | Public GET pages may fall back to saved pages; writes are never retried automatically | The service worker does not cache admin pages, Livewire requests, or non-GET requests. The browser explains 401/403, 419, 429, network, and server failures; uncertain writes require checking saved state before resubmitting. |
| Development URL | Port `8585` | Composer's development command and local instructions use `http://127.0.0.1:8585`. |

The site has no checkout, payment, public account creation, or external HTTP integration, so commerce idempotency and outbound HTTP retry rules do not currently apply. The existing personal-access-token migration is retained as historical schema compatibility; token authentication is not installed or used.

## Hosting decisions still required

These cannot be verified or safely configured without the production host and an accountable service owner:

| Area | Current status | Owner action |
|---|---|---|
| MFA for administrators | Deferred; not implemented | Enable MFA for named admin accounts before public launch, or formally accept the exception and record an owner and review date. |
| Trusted proxies | Not configured | If a load balancer is used, trust only its actual proxy addresses and verify client IP and HTTPS detection. |
| Multi-server operation | Not approved | If multiple app instances are used, configure shared Redis for cache and rate limiting, shared file/object storage, and verify session behavior. |
| Backups and recovery | No off-server backup or restore drill is evidenced | Back up MySQL and uploaded images off-server, encrypt them, choose retention, then approve RPO/RTO after a restore drill. |
| Monitoring and alerts | No external monitor or recipient is configured | Monitor `/up`, errors, latency, disk, and MySQL; name and test the alert recipient. |
| Performance targets | No representative staging measurements are available | Record a baseline and run gradual load and recovery tests on staging before setting p95, availability, or capacity goals. |

Until those host-owned items are completed or explicitly accepted, this record describes application controls and deployment prerequisites; it does not claim a fully verified production launch.
