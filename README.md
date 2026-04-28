# Spam Destroyer

Block invite link spam from Discord, Telegram and other platforms. Built with [Laravel](https://laravel.com) — a redirect engine that intercepts spam links and protects communities.

**Production site:** [spam-destroyer.com](https://spam-destroyer.com/)

---

## Overview

Spam Destroyer is a high-performance URL redirection and analytics service. Users submit suspicious invite links; the app redirects through a tracking layer that records hits, blocks bots via rate-limiting, and provides admins with a clean dashboard to manage redirects. A companion [WordPress plugin](https://wordpress.org/plugins/spam-destroyer/) extends the same protection to comment spam.

The system was rebuilt from a legacy WordPress + Facebook Login prototype into a lean Laravel 12 application that uses ~10% of the original server resources.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Language** | PHP 8.4 |
| **Framework** | Laravel 12 |
| **Database** | MariaDB (admin only); Redis (URL lookups, analytics) |
| **Cache/Queue** | Redis via Predis (explicit, non-swappable for analytics) |
| **Frontend** | Blade + CSS + Vite |
| **CI / Quality** | Laravel Pint, Larastan (PHPStan), PHPUnit |
| **Infrastructure** | Nginx, Laravel scheduler (cron: every minute) |

---

## Architecture & Design Decisions

### Redis-First Analytics

URL redirects are cached via Laravel's generic cache layer for fast lookups. Analytics (hit counts, path counters) use **Redis directly** — not the cache abstraction — because:

- Redis's `INCREMENT` command provides atomic counters without race conditions.
- Making the dependency explicit signals that this code cannot silently degrade if the cache driver is swapped.
- Data loss on Redis restart is acceptable (analytics are synced to MySQL every 15 minutes by `SyncAnalyticsCommand`).

The `Redis::keys()` pattern is used instead of `SCAN` because the key space is small (synced every 15 minutes) and the simpler code is easier to maintain.

### Synchronous Analytics Sync

An Artisan command (`sync:analytics`) flushes Redis counters into SQL on a schedule. A dedicated `AnalyticsSyncService` handles the batch upsert. Separate tables for daily stats were considered but omitted — the current volume doesn't justify the complexity.

### Performance

| Metric | Score |
|--------|-------|
| Google PageSpeed Performance | 100/100 |
| Accessibility | 100/100 |
| SEO | 100/100 |
| Best Practices | 100/100 |

(scores are from 2026-04-29)

### Security Hardening

- **Rate limiting:** Form submissions are throttled via Laravel's throttle middleware.
- **XSS prevention:** Redirect URLs are escaped in JavaScript output.
- **Race conditions:** The redirect management controller uses database transactions to prevent duplicate slug creation.
- **Strict types:** `declare(strict_types=1)` enforced across all PHP classes.
- **Input validation:** `from` fields are validated for URL-safe characters and enforced uniqueness at the database level.

---

## Key Source Files

| File | Purpose |
|------|---------|
| `app/Http/Controllers/RedirectController.php` | Handles incoming redirects and records hits |
| `app/Http/Controllers/RedirectManagementController.php` | Admin CRUD for URL mappings |
| `app/Http/Middleware/TrackPathHits.php` | Middleware that increments Redis counters per path |
| `app/Services/UrlLookupService.php` | Cache-aware URL resolution |
| `app/Services/AnalyticsService.php` | Redis-backed analytics read/write |
| `app/Services/AnalyticsSyncService.php` | Batch sync from Redis to MySQL |
| `app/Console/Commands/SyncAnalyticsCommand.php` | Artisan command for manual/frequent sync |
| `app/Models/UrlMapping.php` | Eloquent model for redirect mappings |
| `app/Models/PathCounter.php` | Eloquent model for aggregated path stats |

---

## Local Development

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
```

Use the `dev` script for concurrent serving, queue listening, and Vite HMR:

```bash
composer dev
```

---

## Code Quality

```bash
# Laravel Pint (PSR-12 coding style — 100% compliant)
./vendor/bin/pint

# Larastan (static analysis — PHPStan for Laravel)
composer phpstan

# PHPUnit tests
composer test
```

---

## Deployment

A cron entry calling `php artisan schedule:run` every minute drives the Laravel scheduler, which triggers the analytics sync and other maintenance tasks.
