# GARDA 5 — Production Deployment Guide

## Prerequisites

- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.6+
- Redis (recommended for session/cache/queue)
- Nginx or Apache
- Supervisor (for queue workers if using `database`/`redis` queue driver)

---

## 1. Initial Server Setup

```bash
# Clone / upload the project
git clone https://your-repo/garda5-app /var/www/garda5
cd /var/www/garda5

# Install PHP dependencies (no dev)
composer install --no-dev --optimize-autoloader

# Set up environment
cp env.production .env
# Edit .env — fill in DB credentials, APP_URL, MAIL settings
nano .env

# Generate application key
php artisan key:generate

# Set correct permissions
chown -R www-data:www-data /var/www/garda5
chmod -R 755 /var/www/garda5
chmod -R 775 /var/www/garda5/storage
chmod -R 775 /var/www/garda5/bootstrap/cache

# Install frontend assets
npm ci
npm run build
```

---

## 2. Database Setup

```bash
# Run all migrations
php artisan migrate --force

# Seed master data only (Wilayah, Videos)
# Do NOT run db:seed without --class on production — it will try DevFixtureSeeder
php artisan db:seed --class=WilayahSeeder
php artisan db:seed --class=VideoSeeder

# Create the first admin account interactively
php artisan app:create-admin
```

---

## 3. Laravel Optimization (run after every deployment)

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
php artisan icons:cache   # If using Blade icons
```

---

## 4. APP_KEY Rotation

If you need to rotate the APP_KEY (e.g., after a suspected compromise):

```bash
# Step 1: Save old key (sessions/cookies encrypted with old key will be invalidated)
OLD_KEY=$(grep APP_KEY .env | cut -d= -f2)

# Step 2: Generate new key
php artisan key:generate --force

# Step 3: Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan session:flush   # Forces all users to re-login

# Step 4: Restart queue workers
php artisan queue:restart
```

---

## 5. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate     /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;

    root /var/www/garda5/public;
    index index.php;

    # Security headers (also set by Laravel middleware, belt-and-suspenders)
    add_header X-Frame-Options           "DENY"                          always;
    add_header X-Content-Type-Options    "nosniff"                       always;
    add_header Referrer-Policy           "strict-origin-when-cross-origin" always;

    # Prevent access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny direct access to storage
    location /storage {
        deny all;
    }
}
```

---

## 6. Supervisor (Queue Workers)

If using `QUEUE_CONNECTION=database` or `redis`:

```ini
; /etc/supervisor/conf.d/garda5-worker.conf

[program:garda5-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/garda5/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/garda5/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
supervisorctl reread
supervisorctl update
supervisorctl start garda5-worker:*
```

---

## 7. Logging

Production log level is set to `error` in `env.production`.

Logs are written to `storage/logs/laravel-YYYY-MM-DD.log` (daily rotation).

To integrate with Sentry, add to `.env`:
```
SENTRY_LARAVEL_DSN=https://your-key@sentry.io/your-project
```

And install: `composer require sentry/sentry-laravel`

---

## 8. Automated Backup (crontab)

```bash
# /etc/cron.d/garda5-backup
# Daily DB backup at 02:00
0 2 * * * www-data /usr/bin/mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > /backups/garda5-$(date +\%F).sql.gz

# Keep only last 30 days
0 3 * * * www-data find /backups/ -name "garda5-*.sql.gz" -mtime +30 -delete
```

Or add the Laravel scheduler to crontab:
```
* * * * * cd /var/www/garda5 && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Post-Deployment Checklist

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_KEY` generated
- [ ] `SESSION_SECURE=true`
- [ ] `SESSION_SAME_SITE=strict`
- [ ] SSL certificate installed and auto-renewing
- [ ] Database backed up
- [ ] `php artisan config:cache` run
- [ ] `php artisan route:cache` run
- [ ] Queue workers running (if applicable)
- [ ] Storage symlink created: `php artisan storage:link`
- [ ] First admin created: `php artisan app:create-admin`
- [ ] `.env` NOT in git (confirm with `git status`)
- [ ] `.ddev/` NOT deployed
