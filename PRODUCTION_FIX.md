# Исправление ошибки "Method Not Allowed" на продакшене

## Проблема
На продакшене (qaror.sjco.uz) при попытке войти в админ панель появляется ошибка:
```
Method Not Allowed
The POST method is not supported for route admin/login. Supported methods: GET, HEAD.
```

## Причина
1. Был создан файл `app/Filament/Auth/CustomLogin.php` с пустым методом `getForms()`, который ломал форму логина
2. На сервере закеширован старый роутинг

## Решение

### Шаг 1: Загрузить изменения на сервер
Файл `app/Filament/Auth/CustomLogin.php` был удален из проекта. Загрузите изменения на сервер:

```bash
# На локальной машине
git add .
git commit -m "Fix: Remove broken CustomLogin that caused Method Not Allowed error"
git push origin main
```

### Шаг 2: На продакшен сервере выполнить команды

```bash
# Перейти в директорию проекта
cd /path/to/your/project

# Получить последние изменения
git pull origin main

# Очистить ВСЕ кеши Laravel
php artisan optimize:clear

# Или по отдельности:
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Обновить автозагрузку Composer
composer dump-autoload

# Обновить Filament ассеты
php artisan filament:upgrade

# Закешировать заново для продакшена (ОПЦИОНАЛЬНО - только если нужна производительность)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Шаг 3: Проверить права на файлы

```bash
# Убедиться что Laravel имеет права на запись в storage и cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # замените www-data на вашего пользователя web-сервера
```

### Шаг 4: Проверить .htaccess файлы

Убедитесь что в корне проекта есть `.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

И в `public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
```

### Шаг 5: Перезапустить сервисы

```bash
# Для Apache
sudo systemctl restart apache2

# Для Nginx + PHP-FPM
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm  # замените на вашу версию PHP

# Для очереди (если используется)
sudo systemctl restart queue-worker  # или supervisorctl restart all
```

## Проверка работы

После выполнения команд попробуйте снова войти в админ панель:
- URL: https://qaror.sjco.uz/admin
- Email: admin@test.com
- Password: password

## Дополнительная диагностика

Если проблема остается, проверьте:

### 1. Проверить роуты на сервере
```bash
php artisan route:list --path=admin/login
php artisan route:list --path=livewire
```

Должны быть:
- `GET|HEAD admin/login`
- `POST livewire/update` ✅ ВАЖНО!

### 2. Проверить логи
```bash
tail -f storage/logs/laravel.log
```

### 3. Проверить логи веб-сервера
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

### 4. Проверить SESSION конфигурацию в .env
```env
SESSION_DRIVER=database  # или file
SESSION_SECURE_COOKIE=true  # для HTTPS
SESSION_DOMAIN=.sjco.uz  # или null
```

Если используется `SESSION_DRIVER=database`, убедитесь что таблица `sessions` существует:
```bash
php artisan migrate
```

## Важные замечания

1. **ВСЕГДА очищайте кеш** после изменений в роутах или конфигурации
2. На продакшене НЕ используйте `APP_DEBUG=true` - установите `APP_DEBUG=false`
3. Убедитесь что `APP_ENV=production`
4. После очистки кеша можно снова закешировать для производительности
5. Если изменили .env - выполните `php artisan config:clear`

## Создание нового пользователя на продакшене

Если нужно создать нового пользователя:

```bash
php artisan tinker

# В консоли Tinker:
$user = new App\Models\User();
$user->name = 'Your Name';
$user->email = 'your@email.com';
$user->password = bcrypt('your-secure-password');
$user->save();
exit
```
