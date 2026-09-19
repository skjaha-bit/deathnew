# FYRIRHYGGJA — Laravel API + Admin Panel

A bilingual (English/Icelandic) digital legacy-planning platform. Users record their pension funds, insurance, bank accounts, trade union membership, funeral wishes and death notice details, so relatives have a single place to find everything after the user's day.

Built as a **module-based Laravel 12 monolith** (via `nwidart/laravel-modules`) with a versioned REST API for the mobile/web client and a Bootstrap 5 back-office admin panel.

## Tech stack

- **Laravel 12**, PHP 8.2+, MySQL/MariaDB
- **`nwidart/laravel-modules`** — every business domain is a self-contained package under `Modules/{Name}`
- **`laravel/passport`** — OAuth2 bearer tokens for the API (issued directly on register; on login, gated behind a 2nd-factor OTP)
- **Real SMS OTP** via [GatewayAPI](https://gatewayapi.com) (HTTP Basic auth, token as username) — swappable via `Modules\Core\Services\Sms\SmsGatewayInterface`
- **`spatie/laravel-permission`** — roles & permissions for the admin panel
- **`spatie/laravel-activitylog`** — audit trail
- **`darkaonline/l5-swagger`** — OpenAPI 3 docs generated from PHP attributes, served at `/api/documentation`
- **Blade + Bootstrap 5 + Chart.js**, both loaded via CDN — no Node/npm/Vite build step required

## Modules

| Module | What it owns |
|---|---|
| `Core` | Languages, translations (admin-managed UI strings), settings, `BaseRepository`, `ApiResponse` trait, SMS gateway |
| `Auth` | Registration (direct login), Login+OTP, logout, profile |
| `Overview` | Cross-module profile-completion dashboard |
| `Relatives` | Family members who should eventually access the user's data (invite/permission model only, no unlock trigger yet) |
| `TradeUnion` | Trade union lookup + user's membership entries |
| `CommercialBank` | Bank lookup + user's accounts/bonds/funds |
| `Pension` | Pension fund lookup + private/obligatory entries + "email the fund" inquiries |
| `Insurance` | Insurance company lookup + policies + documents + contact requests |
| `AfterMyDay` | Funeral wishes + death notice (both 1:1 per user) |
| `Cms` | Bilingual help panels / FAQs / education articles / banners (public content) |
| `Notification` | In-app notifications (list/unread-count/mark-read) |
| `Subscription` | Plan selection only — **no payment gateway integrated** |
| `Admin` | Session-based back-office: auth, dashboard, users, roles & permissions, activity logs, reports, notification broadcast |

## Requirements

- PHP 8.2+ with the `sodium` extension enabled (required by Passport/JWT — on XAMPP, uncomment `extension=sodium` in `php.ini`)
- Composer 2.x
- MySQL/MariaDB

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fyrirhyggja
DB_USERNAME=root
DB_PASSWORD=

GATEWAYAPI_URL=https://gatewayapi.com/rest/mtsms
GATEWAYAPI_TOKEN=your-gatewayapi-token
GATEWAYAPI_SENDER=FYRIRHYGGJA
```

Create the database, then:

```bash
composer dump-autoload
php artisan migrate --seed
php artisan passport:keys
php artisan passport:client --personal --name="FYRIRHYGGJA Personal Access Client" --no-interaction
php artisan storage:link
```

Serve it:

```bash
php artisan serve
```

- API base URL: `http://127.0.0.1:8000/api/v1`
- Admin panel: `http://127.0.0.1:8000/admin`
- Swagger UI: `http://127.0.0.1:8000/api/documentation`

### Seeded default admin login

| Email | Password |
|---|---|
| `admin@fyrirhyggja.is` | `Password123!` |

**Change this password before deploying anywhere non-local.**

### Seed data

`php artisan migrate --seed` also loads: EN/IS languages, base roles (`admin`, `support-agent`) and permissions, sample reference data (real Icelandic pension funds/insurance companies/banks/trade unions), the four subscription plans matching the original pricing (1.390/12.000/54.000/96.000 kr.), and bilingual CMS help-panel/FAQ content for every module.

## Authentication flow

1. `POST /api/v1/auth/register` — creates the user and returns a bearer token **immediately** (no OTP gate on sign-up).
2. `POST /api/v1/auth/login` — validates credentials, sends a one-time code over SMS via GatewayAPI, returns `otp_reference` (no token yet).
3. `POST /api/v1/auth/verify-otp` — validates the code, returns a bearer token.
4. `POST /api/v1/auth/resend-otp` — re-sends the code.
5. `POST /api/v1/auth/logout` — revokes the current token (requires `Authorization: Bearer <token>`).

Every other API route (except the public `Cms`/`Banner`/`Language`/`Translation`/plan-listing endpoints) requires `Authorization: Bearer <token>`.

## Documentation

- **Swagger / OpenAPI**: `/api/documentation` (generate manually with `php artisan l5-swagger:generate` if you add endpoints)
- **Postman collection**: [`docs/FYRIRHYGGJA.postman_collection.json`](docs/FYRIRHYGGJA.postman_collection.json) — import into Postman, set the `base_url` collection variable (defaults to `http://127.0.0.1:8000`), and the `token` variable auto-fills after running Register or Verify OTP.
- **Full route list**: [`docs/routes.txt`](docs/routes.txt) (regenerate with `php artisan route:list > docs/routes.txt`)

## Code architecture

Every module follows: `routes → Controller → FormRequest → Service → Repository (CRUD/listing modules only) → Model → API Resource`, plus its own migrations, and (for domain modules with an admin screen) an `Http/Controllers/Admin` controller + Blade views under `resources/views/admin`. Simple 1:1 singleton resources (Funeral, Death Notice) skip the repository layer. All API responses use a consistent envelope via `Modules\Core\Traits\ApiResponse`: `{"success": bool, "message": string, "data": ...}`.

## Known deliberate scope limits

- **Relatives**: only the invite/permission data model is built — there's no "mark user deceased" unlock trigger yet.
- **Subscription**: plan selection only, recorded in the database. No payment vendor is integrated (no card capture, no `payments` table).
- **OTP**: real SMS via GatewayAPI on login; registration bypasses OTP entirely by design.
