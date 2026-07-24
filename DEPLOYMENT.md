# EVA Beauty Hub API - Deployment Documentation

This document outlines the requirements and steps for deploying the EVA Beauty Hub API to a production environment (specifically AlwaysData).

> [!IMPORTANT]
> **USER TODO**: Fill in the placeholders (e.g. `{ALWAYSDATA_PLAN}`, `{PHP_VERSION}`, `{DB_ENGINE_VERSION}`) with the exact values from your hosting provider once gathered.

## 1. Environment Variables Required

The following environment variables are required in the production environment. Make sure to set these securely.

| Variable | Description | Example / Required |
|----------|-------------|--------------------|
| `APP_ENV` | Must be `production` | `production` |
| `APP_KEY` | Application encryption key | (Generated via `php artisan key:generate`) |
| `APP_DEBUG` | Must be `false` | `false` |
| `APP_URL` | The public URL of the API | `https://api.beauty.alwaysdata.net` |
| `FRONTEND_URL` | Allowed origin for CORS | `https://evabeauty.com` |
| `DB_CONNECTION` | Database connection type | `mysql` |
| `DB_HOST` | Database host | `mysql-{ACCOUNT}.alwaysdata.net` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `{ACCOUNT}_evabeauty` |
| `DB_USERNAME` | Database user | `{ACCOUNT}` |
| `DB_PASSWORD` | Database password | `XXXXX` |
| `CLOUDINARY_URL` | Full cloudinary URL | `cloudinary://API_KEY:API_SECRET@CLOUD_NAME` |
| `CLOUDINARY_CLOUD_NAME` | Cloudinary cloud name | `XXXXX` |
| `CLOUDINARY_API_KEY` | Cloudinary API key | `XXXXX` |
| `CLOUDINARY_API_SECRET`| Cloudinary API secret| `XXXXX` |
| `CACHE_STORE` | Cache driver | `{REDIS_AVAILABLE ? 'redis' : 'file'}` |
| `QUEUE_CONNECTION` | Queue driver | `{REDIS_AVAILABLE ? 'redis' : 'database'}` |
| `SESSION_DRIVER` | Session driver | `cookie` |

## 2. Production .env Template

Copy this into your `.env` on the production server and replace the `XXXXX` values.

```ini
APP_NAME="EVA Beauty Hub API"
APP_ENV=production
APP_KEY=XXXXX
APP_DEBUG=false
APP_URL=https://api.beauty.alwaysdata.net
FRONTEND_URL=https://evabeauty.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=mysql-{ACCOUNT}.alwaysdata.net
DB_PORT=3306
DB_DATABASE={ACCOUNT}_evabeauty
DB_USERNAME={ACCOUNT}
DB_PASSWORD=XXXXX

SESSION_DRIVER=cookie
SESSION_LIFETIME=120

CACHE_STORE={REDIS_AVAILABLE ? 'redis' : 'file'}
QUEUE_CONNECTION={REDIS_AVAILABLE ? 'redis' : 'database'}

CLOUDINARY_URL=cloudinary://XXXXX:XXXXX@XXXXX
CLOUDINARY_CLOUD_NAME=XXXXX
CLOUDINARY_API_KEY=XXXXX
CLOUDINARY_API_SECRET=XXXXX
```

## 3. Database Setup Steps

Once the files are uploaded and the `.env` is configured:

1. Create the database in the AlwaysData control panel if you haven't already.
2. Run the migrations to build the schema:
   ```bash
   php artisan migrate --force
   ```
3. Run the seeders to populate initial data and settings:
   ```bash
   php artisan db:seed --force
   ```
4. The `AdminSeeder` (called within `DatabaseSeeder`) will create the initial `Super Admin` account.

## 4. Server Requirements Checklist

Ensure your AlwaysData instance meets the following requirements:

- [ ] **PHP Version**: 8.2+ (`{PHP_VERSION}`)
- [ ] **Database Engine**: MySQL 5.7+ or MariaDB 10.4+ (`{DB_ENGINE_VERSION}`)
- [ ] **Composer**: 2+
- [ ] **PHP Extensions**: `pdo_mysql`, `mbstring`, `openssl`, `xml`, `curl`, `fileinfo`, `gd`

## 5. Deployment Steps for AlwaysData

**Plan details:** `{ALWAYSDATA_PLAN}`
**SSH Access:** `{SSH_ENABLED}`

1. **Upload Files:** Upload the project files to the root directory for your site (e.g. `www/api`).
2. **Install Dependencies:** Connect via SSH and run:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
3. **Configure Environment:** Create and configure the `.env` file per the template above.
4. **Generate Key:**
   ```bash
   php artisan key:generate --force
   ```
5. **Set up Database:** Run the migrations and seeders (see section 3).
6. **Cache Configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```
7. **Configure Public Directory:** Ensure the AlwaysData site's root directory is pointed to the `/public` folder of your Laravel installation.
8. **Configure Cron Job:** Add the following to the AlwaysData cron (`{CRON_ENABLED}`):
   ```bash
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```
9. **Configure Queue Worker:** Run the queue worker as a daemon service (if possible) or via a process manager like Supervisor. If using `database` queue, ensure `php artisan queue:work` is running continually.

## 6. Post-Deployment Verification Checklist

- [ ] Can access `https://api.beauty.alwaysdata.net/api/v1/settings` and receive a `200 OK` JSON response.
- [ ] Can successfully log in as the admin via `POST /api/v1/admin/login`.
- [ ] Rate limiting is properly working (returns `429` status on repeated requests).
- [ ] Cloudinary uploads are working (test by creating a new brand with a logo or product image).
- [ ] No stack traces are exposed on intentional errors (e.g. visiting a 404 route returns a clean JSON error).
