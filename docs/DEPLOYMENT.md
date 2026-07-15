# Deployment Guide — Barcode Management System

> **Version:** 1.0.0  
> **Last Updated:** 2026-07-15  
> **Task:** RC-SPRINT-001 (Production Hardening)

---

## 1. Server Requirements

### Minimum Specifications

| Resource | Requirement |
|----------|-------------|
| CPU | 2 vCPU |
| RAM | 4 GB |
| Disk | 20 GB SSD |
| OS | Ubuntu 22.04 LTS / Debian 12 |

### Software Versions

| Software | Version | Notes |
|----------|---------|-------|
| PHP | ^8.2 | Required by Laravel 12 |
| Composer | ^2.5 | PHP dependency manager |
| Node.js | ^20 | Required for frontend build |
| NPM | ^10 | Node package manager |
| PostgreSQL | ^16 | Primary database |
| Nginx | ^1.24 | Production web server |
| Redis | ^7.0 | Optional — cache/queue |

---

## 2. PHP Extensions

Ensure the following PHP extensions are installed:

```bash
# Required
php8.2-cli
php8.2-fpm
php8.2-pgsql
php8.2-mbstring
php8.2-xml
php8.2-curl
php8.2-bcmath
php8.2-json
php8.2-tokenizer

# Optional (recommended for production)
php8.2-redis
php8.2-opcache
```

---

## 3. Deployment Steps

### 3.1 Clone Repository

```bash
git clone https://github.com/narcen12345-a11y/Barcode-Management-System-2.git
cd Barcode-Management-System-2
```

### 3.2 Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure .env for production
# Edit .env and set:
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=https://your-domain.com
#   DB_* settings for your PostgreSQL database

# Run database migrations
php artisan migrate --force

# Seed initial data (admin user, roles, permissions)
php artisan db:seed --force

# Cache Laravel config, routes, views, events
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage symlink
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3.3 Frontend Setup

```bash
cd frontend

# Install Node dependencies
npm ci --production

# Build for production
npm run build

# The built files will be in dist/
# Serve these via Nginx or deploy to a CDN
```

### 3.4 Database Setup

```bash
# Create PostgreSQL database and user
sudo -u postgres psql

CREATE DATABASE barcode_management;
CREATE USER barcode_user WITH PASSWORD 'barcode_password';
GRANT ALL PRIVILEGES ON DATABASE barcode_management TO barcode_user;
\c barcode_management
GRANT ALL ON SCHEMA public TO barcode_user;
\q
```

---

## 4. Web Server Configuration

### 4.1 Nginx (Backend API)

```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/barcode/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4.2 Nginx (Frontend SPA)

```nginx
server {
    listen 80;
    server_name app.your-domain.com;
    root /var/www/barcode/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires max;
        log_not_found off;
    }
}
```

### 4.3 Apache (.htaccess)

If using Apache, ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

The Laravel `public/.htaccess` file handles URL rewriting automatically.

---

## 5. Queue Worker (Optional)

If using database queue driver:

```bash
# Install supervisor
sudo apt install supervisor

# Create supervisor config: /etc/supervisor/conf.d/barcode-queue.conf
```

```ini
[program:barcode-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/barcode/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/barcode/backend/storage/logs/queue-worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start barcode-queue:*
```

---

## 6. Cron / Scheduler

Add the following cron entry for the Laravel scheduler:

```bash
# Run every minute as www-data
* * * * * cd /var/www/barcode/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Cache Configuration

### 7.1 OPcache (Recommended)

Add to `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 7.2 Laravel Cache Commands

Run these after every deployment:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

To clear (during maintenance):

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan cache:clear
```

---

## 8. Storage

### 8.1 Local Storage

```bash
# Create storage symlink (public disk)
php artisan storage:link

# Permissions
chmod -R 775 storage/app/public
chmod -R 775 storage/framework/cache
chmod -R 775 storage/framework/sessions
chmod -R 775 storage/framework/views
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
```

### 8.2 S3 / Cloud Storage (Optional)

Set in `.env`:

```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_URL=https://your-bucket.s3.amazonaws.com
```

---

## 9. SSL / HTTPS

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d api.your-domain.com -d app.your-domain.com

# Auto-renewal (already configured by certbot)
sudo certbot renew --dry-run
```

---

## 10. Monitoring & Logging

### Log Files

| Log | Path |
|-----|------|
| Laravel log | `backend/storage/logs/laravel.log` |
| Nginx access | `/var/log/nginx/access.log` |
| Nginx error | `/var/log/nginx/error.log` |
| PHP-FPM | `/var/log/php8.2-fpm.log` |
| Queue worker | `backend/storage/logs/queue-worker.log` |

### Health Check Endpoint

```
GET /api/health
```

Expected response:
```json
{
    "status": "ok",
    "timestamp": "2026-07-15T12:00:00Z"
}
```

---

## 11. Rollback Procedure

```bash
# 1. Revert to previous release
git checkout <previous-tag>

# 2. Re-run deployment steps
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback --force  # if DB changes
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Rebuild frontend
npm ci --production
npm run build
```

---

## 12. Useful Commands

```bash
# Check Laravel version
php artisan --version

# List all routes
php artisan route:list

# List all registered commands
php artisan list

# Check maintenance mode
php artisan down     # Enable maintenance mode
php artisan up       # Disable maintenance mode

# Clear all caches
php artisan optimize:clear

# Show current .env values
php artisan env

# Check application health
php artisan about
```
