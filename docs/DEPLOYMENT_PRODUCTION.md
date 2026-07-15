# Production Deployment Guide — Barcode Management System

> **Version:** 1.0.0  
> **Last Updated:** 2026-07-15  
> **Target OS:** Ubuntu 24.04 LTS

---

## Table of Contents

1. [VPS Setup](#1-vps-setup)
2. [Domain & DNS](#2-domain--dns)
3. [SSL Certificate](#3-ssl-certificate)
4. [Nginx Configuration](#4-nginx-configuration)
5. [Supervisor (Queue Worker)](#5-supervisor-queue-worker)
6. [Cron (Scheduler)](#6-cron-scheduler)
7. [Queue Configuration](#7-queue-configuration)
8. [Cache Configuration](#8-cache-configuration)
9. [Storage Setup](#9-storage-setup)
10. [Backup Strategy](#10-backup-strategy)
11. [Monitoring](#11-monitoring)
12. [Rollback Procedure](#12-rollback-procedure)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. VPS Setup

### Minimum Requirements

| Resource | Requirement |
|----------|-------------|
| CPU | 2 vCPU |
| RAM | 4 GB |
| Disk | 20 GB SSD |
| OS | Ubuntu 24.04 LTS |
| Network | Static public IP |

### Initial Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Set hostname
sudo hostnamectl set-hostname barcode-prod

# Create deploy user
sudo adduser deploy
sudo usermod -aG sudo deploy

# Configure SSH key
sudo mkdir -p /home/deploy/.ssh
sudo cp ~/.ssh/authorized_keys /home/deploy/.ssh/
sudo chown -R deploy:deploy /home/deploy/.ssh
sudo chmod 700 /home/deploy/.ssh
sudo chmod 600 /home/deploy/.ssh/authorized_keys

# Configure firewall
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Disable root login
sudo sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sudo systemctl restart sshd
```

### Install Required Software

```bash
# Run the deployment script (bare-metal)
sudo ./scripts/deploy.sh

# Or with Docker
sudo ./scripts/deploy.sh --docker
```

---

## 2. Domain & DNS

### DNS Records

| Type | Name | Value |
|------|------|-------|
| A | `api.your-domain.com` | `<VPS_IP_ADDRESS>` |
| A | `app.your-domain.com` | `<VPS_IP_ADDRESS>` |

### Update .env

```bash
# Backend
APP_URL=https://api.your-domain.com
SANCTUM_STATEFUL_DOMAINS=app.your-domain.com,localhost

# Frontend
VITE_API_BASE_URL=https://api.your-domain.com/api
```

---

## 3. SSL Certificate

### Using Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificates
sudo certbot --nginx -d api.your-domain.com -d app.your-domain.com

# Verify auto-renewal
sudo certbot renew --dry-run
```

### Update Nginx for HTTPS

After obtaining certificates, update the Nginx configs to include SSL:

```nginx
server {
    listen 443 ssl http2;
    server_name api.your-domain.com;

    ssl_certificate /etc/letsencrypt/live/api.your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.your-domain.com/privkey.pem;

    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # ... rest of the config
}

server {
    listen 80;
    server_name api.your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

---

## 4. Nginx Configuration

### Backend API

Location: `/etc/nginx/sites-available/barcode-backend`

```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/barcode/backend/public;

    index index.php;
    charset utf-8;

    # Security headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
    add_header X-XSS-Protection "0" always;

    client_max_body_size 20M;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 180;
    }

    location ~ /\.(?!well-known).* { deny all; }
    location ~ ^/(storage/app|storage/framework|storage/logs|bootstrap/cache|\.env) { deny all; }
}
```

### Frontend SPA

Location: `/etc/nginx/sites-available/barcode-frontend`

```nginx
server {
    listen 80;
    server_name app.your-domain.com;
    root /var/www/barcode/frontend/dist;

    index index.html;
    charset utf-8;

    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
    add_header X-XSS-Protection "0" always;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires max;
        add_header Cache-Control "public, immutable";
        log_not_found off;
    }

    location = /health {
        access_log off;
        return 200 '{"status":"ok"}';
        add_header Content-Type application/json;
    }

    location ~ /\. { deny all; }
}
```

### Enable Sites

```bash
sudo ln -sf /etc/nginx/sites-available/barcode-backend /etc/nginx/sites-enabled/
sudo ln -sf /etc/nginx/sites-available/barcode-frontend /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

---

## 5. Supervisor (Queue Worker)

### Installation

```bash
sudo apt install supervisor -y
```

### Configuration

File: `/etc/supervisor/conf.d/barcode-queue.conf`

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

### Start

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start barcode-queue:*
sudo supervisorctl status
```

---

## 6. Cron (Scheduler)

Add to crontab (`sudo crontab -e`):

```bash
* * * * * cd /var/www/barcode/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Queue Configuration

### Database Driver (Recommended)

```bash
# Create jobs table migration
php artisan queue:table
php artisan migrate

# Update .env
QUEUE_CONNECTION=database
```

### Redis Driver (High Performance)

```bash
# Install Redis
sudo apt install redis-server -y

# Update .env
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 8. Cache Configuration

### OPcache

Ensure OPcache is enabled in `/etc/php/8.2/cli/conf.d/99-opcache.ini`:

```ini
[opcache]
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### Laravel Cache Commands

Run after every deployment:

```bash
cd /var/www/barcode/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 9. Storage Setup

### Permissions

```bash
sudo chown -R www-data:www-data /var/www/barcode/backend/storage
sudo chown -R www-data:www-data /var/www/barcode/backend/bootstrap/cache
sudo chmod -R 775 /var/www/barcode/backend/storage
sudo chmod -R 775 /var/www/barcode/backend/bootstrap/cache
```

### Storage Link

```bash
cd /var/www/barcode/backend
php artisan storage:link
```

---

## 10. Backup Strategy

### Automated Daily Backup

Add to crontab (`sudo crontab -e`):

```bash
# Daily database backup at 2 AM
0 2 * * * /var/www/barcode/scripts/backup.sh --retention=30

# Weekly S3 upload (Sunday at 3 AM)
0 3 * * 0 /var/www/barcode/scripts/backup.sh --s3 --retention=30
```

### Manual Backup

```bash
sudo ./scripts/backup.sh
```

### Manual Restore

```bash
sudo ./scripts/restore.sh /var/backups/barcode/barcode_20260715_020000.sql.gz
```

---

## 11. Monitoring

### Health Check Endpoint

```
GET https://api.your-domain.com/api/health
```

Expected response:
```json
{
    "status": "ok",
    "database": "ok",
    "cache": "ok",
    "queue": "ok",
    "storage": "ok",
    "timestamp": "2026-07-15T12:00:00.000000Z"
}
```

### Uptime Monitoring

Recommended services:
- **UptimeRobot** (free tier: 5 monitors)
- **Pingdom** (paid, more features)
- **Better Uptime** (includes status page)

### Error Tracking

Recommended services:
- **Sentry** (free tier: 5k events/month)
- **Flare** (Laravel-specific)
- **Papertrail** (log aggregation)

### Server Monitoring

```bash
# Install Netdata (real-time monitoring)
bash <(curl -Ss https://my-netdata.io/kickstart.sh)

# Or install Prometheus + Grafana stack
```

### Log Files

| Log | Path |
|-----|------|
| Laravel | `/var/www/barcode/backend/storage/logs/laravel.log` |
| Nginx Backend | `/var/log/nginx/backend-access.log` |
| Nginx Backend Error | `/var/log/nginx/backend-error.log` |
| Nginx Frontend | `/var/log/nginx/frontend-access.log` |
| Nginx Frontend Error | `/var/log/nginx/frontend-error.log` |
| PHP-FPM | `/var/log/php8.2-fpm.log` |
| Queue Worker | `/var/www/barcode/backend/storage/logs/queue-worker.log` |

---

## 12. Rollback Procedure

### Using Rollback Script

```bash
# Rollback 1 commit
sudo ./scripts/rollback.sh

# Rollback 3 commits
sudo ./scripts/rollback.sh --steps=3

# Rollback to specific tag
sudo ./scripts/rollback.sh --tag=v1.0.0
```

### Manual Rollback

```bash
# 1. Enable maintenance mode
cd /var/www/barcode/backend
php artisan down --retry=60

# 2. Revert code
cd /var/www/barcode
git reset --hard HEAD~1

# 3. Rollback database
cd backend
php artisan migrate:rollback --force

# 4. Rebuild backend
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Rebuild frontend
cd /var/www/barcode/frontend
npm ci
npm run build

# 6. Restart services
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm
sudo supervisorctl restart barcode-queue:*

# 7. Disable maintenance mode
cd /var/www/barcode/backend
php artisan up
```

---

## 13. Troubleshooting

### Common Issues

#### 1. 502 Bad Gateway

**Cause:** PHP-FPM not running or socket issue.

**Fix:**
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl status php8.2-fpm
# Check socket: /var/run/php/php8.2-fpm.sock
```

#### 2. 500 Internal Server Error

**Cause:** Storage permissions or cached config.

**Fix:**
```bash
sudo chmod -R 775 /var/www/barcode/backend/storage
sudo chmod -R 775 /var/www/barcode/backend/bootstrap/cache
cd /var/www/barcode/backend && php artisan optimize:clear
```

#### 3. Database Connection Error

**Cause:** PostgreSQL not running or credentials wrong.

**Fix:**
```bash
sudo systemctl status postgresql
sudo -u postgres psql -c "\l"
# Check .env credentials
```

#### 4. Queue Worker Not Processing

**Cause:** Supervisor not running or config error.

**Fix:**
```bash
sudo supervisorctl status
sudo supervisorctl restart barcode-queue:*
sudo tail -f /var/www/barcode/backend/storage/logs/queue-worker.log
```

#### 5. Frontend Blank Page

**Cause:** Build error or API URL misconfigured.

**Fix:**
```bash
# Check browser console for errors
# Verify VITE_API_BASE_URL in frontend/.env
# Rebuild frontend
cd /var/www/barcode/frontend
npm ci && npm run build
```

#### 6. SSL Certificate Expired

**Fix:**
```bash
sudo certbot renew
sudo systemctl reload nginx
```

### Quick Health Check

```bash
# Check all services
echo "=== Nginx ===" && sudo systemctl status nginx --no-pager | grep "Active:"
echo "=== PHP-FPM ===" && sudo systemctl status php8.2-fpm --no-pager | grep "Active:"
echo "=== PostgreSQL ===" && sudo systemctl status postgresql --no-pager | grep "Active:"
echo "=== Supervisor ===" && sudo supervisorctl status
echo "=== Disk ===" && df -h / | tail -1
echo "=== Memory ===" && free -h | grep Mem
echo "=== API Health ===" && curl -s https://api.your-domain.com/api/health | jq .
```

### Useful Commands

```bash
# View Laravel logs in real-time
tail -f /var/www/barcode/backend/storage/logs/laravel.log

# View Nginx error logs
tail -f /var/log/nginx/backend-error.log

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check PostgreSQL connections
sudo -u postgres psql -c "SELECT * FROM pg_stat_activity;"

# List all Supervisor processes
sudo supervisorctl status

# Check disk usage
df -h

# Check memory usage
free -h

# Check running processes
htop
```
