# Local Development Setup - Qaror Application

## Database Configuration

**Database Type:** MySQL 8.0
**Database Name:** qaror_db
**Database Host:** 127.0.0.1
**Database Port:** 3306
**Database User:** root
**Database Password:** (empty)

## Access Information

### Public Website
- URL: http://localhost:8000
- Status: Running

### Admin Panel (Filament)
- URL: http://localhost:8000/admin
- Email: admin@test.com
- Password: password

## Running the Application

### Start Development Server
```bash
php artisan serve
```
The server will run at: http://127.0.0.1:8000

### Start with All Services (recommended)
```bash
composer dev
```
This runs concurrently:
- Laravel server (port 8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server (hot reload)

### Stop the Current Server
Press `Ctrl+C` in the terminal where the server is running, or:
```bash
# Find and kill the process
lsof -ti:8000 | xargs kill -9
```

## Common Tasks

### Database Operations
```bash
# Run migrations
php artisan migrate

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Run seeders
php artisan db:seed

# Access MySQL database directly
/opt/homebrew/Cellar/mysql@8.0/8.0.44_5/bin/mysql -u root qaror_db
```

### Asset Development
```bash
# Watch for changes and rebuild
npm run dev

# Build for production
npm run build
```

### Testing
```bash
# Run all tests
composer test

# Or directly
php artisan test
```

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Create New Admin User
```bash
php artisan tinker
```
Then in the Tinker console:
```php
$user = new App\Models\User();
$user->name = 'Your Name';
$user->email = 'your@email.com';
$user->password = bcrypt('your-password');
$user->save();
```

## MySQL Service Management

### Check MySQL Status
```bash
brew services list | grep mysql
```

### Start MySQL
```bash
brew services start mysql@8.0
```

### Stop MySQL
```bash
brew services stop mysql@8.0
```

### Restart MySQL
```bash
brew services restart mysql@8.0
```

## Project Structure

### Key Directories
- `app/Filament/Admin/Resources/` - Filament admin resources
- `app/Livewire/` - Livewire components
- `app/Models/` - Eloquent models
- `database/migrations/` - Database migrations
- `resources/views/` - Blade templates
- `routes/web.php` - Web routes
- `public/` - Public assets
- `storage/app/public/qarorlar/` - PDF files storage

### Main Features
- **Qaror Management**: CRUD operations for decisions/resolutions
- **PDF Upload**: Upload and view PDF documents
- **Search & Filter**: Search by title, number, year
- **Excel Import**: Bulk import from Excel files
- **Public Interface**: View decisions publicly
- **Admin Panel**: Filament-based admin interface

## Troubleshooting

### Port 8000 Already in Use
```bash
# Find what's using port 8000
lsof -ti:8000

# Kill the process
lsof -ti:8000 | xargs kill -9

# Or use a different port
php artisan serve --port=8001
```

### MySQL Connection Issues
1. Ensure MySQL is running: `brew services list | grep mysql`
2. Check .env database credentials
3. Clear config cache: `php artisan config:clear`
4. Test connection: `/opt/homebrew/Cellar/mysql@8.0/8.0.44_5/bin/mysql -u root -e "USE qaror_db; SHOW TABLES;"`

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### Composer/NPM Issues
```bash
# Remove and reinstall
rm -rf vendor node_modules
composer install --ignore-platform-reqs
npm install
```

## Notes

- PHP 8.5.0 is being used with `--ignore-platform-reqs` flag for some packages
- The application uses SQLite by default but has been configured for MySQL
- Queue driver is set to 'sync' for development (no background worker needed)
- Session driver is 'file' for local development
- Debug mode is enabled (APP_DEBUG=true)
