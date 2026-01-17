# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application for managing "Qarorlar" (Decisions/Resolutions). The application provides both an admin panel (Filament) for CRUD operations and a public-facing interface for viewing decisions with PDF support.

**Key Technologies:**
- Laravel 12 (PHP 8.2+)
- Filament 3.x (admin panel)
- Livewire 3.x (reactive components)
- Tailwind CSS 4.x with Flowbite
- Vite (asset bundling)
- SQLite (default database)
- Maatwebsite/Excel (Excel imports)

## Common Commands

### Initial Setup
```bash
composer setup
# This runs: composer install, copies .env, generates key, runs migrations, npm install, npm run build
```

### Development
```bash
composer dev
# Runs concurrently: php artisan serve, queue:listen, pail (logs), npm run dev
```

### Testing
```bash
composer test
# Clears config and runs PHPUnit tests

# Run specific test
php artisan test --filter=TestName
```

### Code Quality
```bash
./vendor/bin/pint
# Laravel Pint for code formatting (PSR-12)
```

### Database
```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
```

### Asset Building
```bash
npm run dev     # Development with hot reload
npm run build   # Production build
```

### Queue Management
```bash
php artisan queue:work
php artisan queue:listen --tries=1
```

### Filament Commands
```bash
php artisan make:filament-resource ModelName
php artisan make:filament-page PageName
php artisan filament:upgrade
```

## Architecture

### Core Domain Model

**Qaror (Decision/Resolution)**
- Primary model representing governmental decisions
- Fields: `title`, `number`, `created_date`, `pdf_path`, `published_id`, `text`
- Table name: `qarors`
- Unique constraint on `published_id` (5-digit random ID)

### Application Layers

**1. Admin Panel (Filament)**
- Located in `app/Filament/Admin/`
- Route prefix: `/admin`
- Resources are auto-discovered from `app/Filament/Admin/Resources/`
- Main resource: `QarorlarResource` handles CRUD for decisions
- Custom login at `app/Filament/Auth/CustomLogin.php`

**2. Public Interface**
- Routes defined in `routes/web.php`
- Main controller: `BasicController` (index page)
- PDF viewer: `PdfController` at route `/pdfs/{number}`
- AJAX search endpoint: `/qarorlar/ajax-search`

**3. Livewire Components**
- `QarorlarTable`: Paginated table with search/filter (title, number, year)
- Uses query strings for filters to maintain state in URL
- Custom pagination theme: Tailwind
- Location: `app/Livewire/`, views in `resources/views/livewire/`

**4. Data Import System**
- `QarorlarImport`: Excel import using Maatwebsite/Excel
- Uses `updateOrCreate` with `number` as unique key
- Job: `ImportQarorExcelJob` (queued)
- Implements `ToModel` and `WithHeadingRow` interfaces

### Important Patterns

**Number Ordering:**
The application uses custom ordering for decision numbers:
```php
->orderByRaw('CAST(number AS UNSIGNED) DESC')
```
This ensures numeric sorting (e.g., 100 > 20 > 3) rather than lexicographic.

**Published ID Generation:**
5-digit unique random IDs (10000-99999) are generated for public-facing references. The Filament form shows this but it's disabled/dehydrated (display-only).

**PDF Handling:**
- PDFs stored in `storage/app/public/qarorlar/`
- Accessed via `/pdfs/{number}` route (by decision number, not ID)
- FileUpload in Filament preserves original filenames

### Database

**Default Connection:** SQLite (`database/database.sqlite`)

To switch to MySQL, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Queue Driver:** Database (uses `jobs` table from migration `0001_01_01_000002_create_jobs_table.php`)

### Frontend

**Views Structure:**
- `resources/views/welcome.blade.php` - Landing page
- `resources/views/pdf-viewer.blade.php` - PDF display page
- `resources/views/livewire/` - Livewire component views
- `resources/views/pdf/` - PDF-related partials

**Asset Pipeline:**
- Entry: `resources/js/app.js` and `resources/css/app.css`
- Tailwind config: `tailwind.config.js` (v4 with @tailwindcss/vite)
- PostCSS config: `postcss.config.js`
- Vite config: `vite.config.js` (Laravel plugin)

**UI Libraries:**
- Flowbite (component library)
- Simple Datatables (table enhancements)
- Blade Heroicons (icon set)

### Configuration Notes

**Livewire:**
- Config files: `config/livewire.php` (note: backup copies exist as `.save` files)
- Components auto-discovered in `app/Livewire/`

**Filament:**
- Panel provider: `app/Providers/Filament/AdminPanelProvider.php`
- Primary color: Amber
- Resources, pages, and widgets auto-discovered

**Storage:**
- Public disk configured for file uploads
- Don't forget to run `php artisan storage:link` after setup

## Development Workflow

1. **Before making changes**: Read the relevant Filament resource or Livewire component to understand current implementation
2. **Model changes**: Always create a migration, don't modify existing migrations
3. **Filament resources**: Follow the existing pattern in `QarorlarResource` for form/table definitions
4. **Livewire**: Use query strings for filter state when pagination is involved
5. **Routes**: Public routes in `web.php`, admin routes auto-registered by Filament
6. **Tests**: Write feature tests in `tests/Feature/`, unit tests in `tests/Unit/`

## Gotchas

- Decision numbers are stored as integers but ordered with `CAST` for proper numeric sorting
- The `published_id` field uses random generation - ensure uniqueness check in production
- Queue jobs must be processed - run `php artisan queue:work` in production
- Livewire pagination uses custom `pageName = 'p'` to avoid conflicts
- Excel imports use `number` field as unique key for upsert operations

## Known Issues and Solutions

### "Method Not Allowed" on Admin Login (Production)
If you encounter "Method Not Allowed" error when trying to login to `/admin/login` on production:

**Cause:** Route cache or broken CustomLogin file

**Solution:**
```bash
# Clear all caches
php artisan optimize:clear

# Remove any custom login files in app/Filament/Auth/ if they override getForms() incorrectly

# Re-cache for production (optional)
php artisan config:cache
php artisan route:cache
```

**Important:** Filament 3 login works through Livewire. The actual authentication happens via `POST /livewire/update`, not `POST /admin/login`. The `/admin/login` route only needs GET method to display the page.

**Verify routes:**
```bash
php artisan route:list --path=livewire
```
You must see: `POST livewire/update` and `POST livewire/upload-file`

See `PRODUCTION_FIX.md` for detailed troubleshooting steps.
