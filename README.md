# EVA Beauty Hub — Backend API

Laravel 12 RESTful API for a cosmetics e-commerce platform with 
frictionless WhatsApp checkout.

## Tech Stack
- Laravel 12
- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.4+
- Cloudinary (image storage)
- Laravel Sanctum (admin auth)
- spatie/laravel-sluggable

## Architecture
Domain-Driven Design (DDD) with 4 domains:
- Catalog (brands, categories, products)
- Operations (WhatsApp numbers, site settings)
- OrderLogging (order snapshots)
- Admin (dashboard authentication)

## Key Features
- Headless REST API (no Blade views)
- UUID-based public identifiers
- Slug-based SEO URLs
- Round-robin WhatsApp routing (race-condition safe)
- Automatic image optimization via Cloudinary (WebP/AVIF)
- Cache layer with automatic invalidation
- Rate limiting on all endpoints
- Soft deletes on core tables

## Installation
See DEPLOYMENT.md for full production deployment guide.

Quick start:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
