# Production Readiness Checklist — Barcode Management System

> **Task:** RC-SPRINT-001 (Production Hardening)  
> **Date:** 2026-07-15

---

## 1. Environment Configuration

- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_KEY` is generated (`php artisan key:generate`)
- [ ] `APP_URL` set to production domain
- [ ] Database credentials configured correctly
- [ ] `SANCTUM_STATEFUL_DOMAINS` includes production frontend domain
- [ ] Frontend `.env` has `VITE_API_BASE_URL` pointing to production API

---

## 2. Database

- [ ] PostgreSQL 16 installed and running
- [ ] Database `barcode_management` created
- [ ] User `barcode_user` created with proper password
- [ ] All migrations have run (`php artisan migrate --force`)
- [ ] Seeders have run (`php artisan db:seed --force`)
- [ ] Database backups configured (cron or managed service)
- [ ] Connection pooling configured (if high traffic)

---

## 3. Backend (Laravel)

- [ ] Composer dependencies installed (`composer install --no-dev --optimize-autoloader`)
- [ ] Config cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Events cached (`php artisan event:cache`)
- [ ] Storage link created (`php artisan storage:link`)
- [ ] OPcache enabled in `php.ini`
- [ ] PHP extensions installed (pgsql, mbstring, xml, curl, bcmath, json, tokenizer)

---

## 4. Frontend (React)

- [ ] Node dependencies installed (`npm ci --production`)
- [ ] Production build created (`npm run build`)
- [ ] Build completes with 0 errors
- [ ] All environment variables set in production `.env`
- [ ] Static assets served via CDN or Nginx with `expires max`

---

## 5. Web Server (Nginx / Apache)

- [ ] Nginx installed and running
- [ ] Backend server block configured (API)
- [ ] Frontend server block configured (SPA)
- [ ] SSL certificate installed (Let's Encrypt / Certbot)
- [ ] HTTP → HTTPS redirect configured
- [ ] Security headers set (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection)
- [ ] `.env` file not accessible from web (blocked by Nginx)
- [ ] `storage/` directory not accessible from web

---

## 6. File Permissions

- [ ] `storage/` directory: `chmod -R 775`
- [ ] `bootstrap/cache/` directory: `chmod -R 775`
- [ ] Owner set to `www-data:www-data`
- [ ] No files owned by `root` in web-accessible paths

---

## 7. Queue & Background Jobs

- [ ] Queue driver configured (database or Redis)
- [ ] Supervisor installed and configured for queue worker
- [ ] Queue worker running (`supervisorctl status`)
- [ ] Laravel scheduler cron entry added (`* * * * *`)

---

## 8. Security

- [ ] `APP_DEBUG=false`
- [ ] Application key generated and not exposed
- [ ] Database password is strong (not default)
- [ ] CORS configured to allow only production frontend domain
- [ ] Sanctum stateful domains configured correctly
- [ ] Rate limiting configured on API routes
- [ ] Failed login attempt throttling active
- [ ] SQL injection protection (Laravel Eloquent/Query Builder)
- [ ] XSS protection (React JSX auto-escapes)
- [ ] CSRF protection enabled for web routes
- [ ] All sensitive data in `.env` (not committed to git)

---

## 9. Monitoring & Logging

- [ ] `LOG_CHANNEL` configured (stack / daily / syslog)
- [ ] `LOG_LEVEL=error` in production
- [ ] Log rotation configured
- [ ] Health check endpoint accessible (`GET /api/health`)
- [ ] Server monitoring tool installed (e.g., Netdata, Prometheus)
- [ ] Uptime monitoring configured (e.g., UptimeRobot, Pingdom)
- [ ] Error tracking configured (e.g., Sentry, Flare)

---

## 10. Performance

- [ ] OPcache enabled and configured
- [ ] Laravel config/route/view/event caching done
- [ ] Frontend assets minified and code-split (Vite build)
- [ ] Database indexes created (via migrations)
- [ ] Nginx `gzip` enabled
- [ ] Static assets cached with `expires max`
- [ ] Redis configured for cache (optional but recommended)

---

## 11. Backup & Disaster Recovery

- [ ] Database backup script configured
- [ ] Backup stored off-site (S3, separate server)
- [ ] Backup restoration tested
- [ ] Rollback procedure documented (see DEPLOYMENT.md)
- [ ] `.env` file backed up securely

---

## 12. Pre-Launch Verification

- [ ] `php artisan about` shows correct environment
- [ ] API responds to health check
- [ ] Login flow works end-to-end
- [ ] Barcode CRUD operations work
- [ ] Master data CRUD operations work
- [ ] User management works
- [ ] Role & permission assignment works
- [ ] Frontend SPA loads without errors
- [ ] All API routes return correct HTTP status codes
- [ ] No debug output visible in production
- [ ] SSL certificate valid and not expired
- [ ] Domain DNS resolves correctly

---

## 13. Post-Launch

- [ ] Monitor error logs for first 24 hours
- [ ] Verify queue worker processes jobs
- [ ] Check database connection pool usage
- [ ] Review server resource usage (CPU, RAM, disk)
- [ ] Set up automated deployment pipeline (CI/CD)

---

## Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Developer | | | |
| QA | | | |
| DevOps | | | |
| Product Owner | | | |
