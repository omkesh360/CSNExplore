# CSNExplore Travel Hub - Deployment Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Security Setup](#security-setup)
5. [Database Setup](#database-setup)
6. [Web Server Configuration](#web-server-configuration)
7. [SSL/HTTPS Setup](#sslhttps-setup)
8. [Backup Configuration](#backup-configuration)
9. [Monitoring & Logging](#monitoring--logging)
10. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements
- PHP 7.4 or higher
- SQLite 3.x or MySQL 5.7+
- Apache 2.4+ or Nginx 1.18+
- 512MB RAM
- 2GB disk space

### Recommended Requirements
- PHP 8.0 or higher
- SQLite 3.35+ or MySQL 8.0+
- Apache 2.4+ with mod_rewrite or Nginx 1.20+
- 2GB RAM
- 10GB disk space
- SSL certificate

### PHP Extensions Required
- pdo_sqlite or pdo_mysql
- json
- mbstring
- openssl
- fileinfo
- gd or imagick (for image processing)
- zip (for backups)

---

## Installation

### Step 1: Clone or Upload Files
```bash
# Clone from repository
git clone https://github.com/your-repo/csnexplore.git
cd csnexplore

# Or upload files via FTP/SFTP to your web server
```

### Step 2: Set Permissions
```bash
# Make directories writable
chmod 755 cache/ data/ database/ logs/ backups/ public/images/
chmod 644 .htaccess

# Secure sensitive files
chmod 600 .env admin_credentials.txt
```

### Step 3: Install Dependencies (if using Composer)
```bash
composer install --no-dev --optimize-autoloader
```

---

## Configuration

### Step 1: Environment Configuration
Copy `.env.example` to `.env` and configure:

```env
# Application
APP_NAME="CSNExplore Travel Hub"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://csnexplore.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/travelhub.db

# Security
JWT_SECRET=your-secret-key-here-change-this
SESSION_LIFETIME=120

# Email (optional)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@csnexplore.com
MAIL_FROM_NAME="CSNExplore"

# Cache
CACHE_DRIVER=file
CACHE_LIFETIME=3600

# Rate Limiting
RATE_LIMIT_REQUESTS=1000
RATE_LIMIT_WINDOW=900
```

### Step 2: Generate JWT Secret
```bash
php -r "echo bin2hex(random_bytes(32));"
```
Copy the output and set it as `JWT_SECRET` in `.env`

---

## Security Setup

### Step 1: Enable Security Headers
Security headers are automatically loaded via `php/security-headers.php`. Ensure it's included in your main PHP files.

### Step 2: Configure CSRF Protection
CSRF protection is available via `php/csrf.php`. Include it in forms:

```php
<?php
require_once 'php/csrf.php';
echo CSRF::getTokenField();
?>
```

### Step 3: Set Up Rate Limiting
Rate limiting is configured in `php/rate-limiter.php`. Adjust limits in `.env`:

```env
RATE_LIMIT_REQUESTS=1000
RATE_LIMIT_WINDOW=900
```

### Step 4: Input Validation
Server-side validation is available via `php/input-validator.php`. Use it for all user inputs:

```php
<?php
require_once 'php/input-validator.php';

$email = InputValidator::validateEmail($_POST['email']);
$password = InputValidator::validatePassword($_POST['password']);
?>
```

---

## Database Setup

### SQLite (Default)
```bash
# Create database
php php/migrate-to-db.php

# Verify database
sqlite3 database/travelhub.db ".tables"
```

### MySQL (Alternative)
1. Create database:
```sql
CREATE DATABASE csnexplore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'csnexplore'@'localhost' IDENTIFIED BY 'your-password';
GRANT ALL PRIVILEGES ON csnexplore.* TO 'csnexplore'@'localhost';
FLUSH PRIVILEGES;
```

2. Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=csnexplore
DB_USERNAME=csnexplore
DB_PASSWORD=your-password
```

3. Import schema:
```bash
mysql -u csnexplore -p csnexplore < database/schema.sql
```

---

## Web Server Configuration

### Apache Configuration

**.htaccess** (already included):
```apache
RewriteEngine On
RewriteBase /

# Redirect to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# API routing
RewriteRule ^api/(.*)$ php/index.php [L,QSA]

# Deny access to sensitive files
<FilesMatch "\.(env|log|sql|db)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Security headers
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set X-Content-Type-Options "nosniff"
```

### Nginx Configuration

Create `/etc/nginx/sites-available/csnexplore`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name csnexplore.com www.csnexplore.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name csnexplore.com www.csnexplore.com;
    
    root /var/www/csnexplore/public;
    index index.html index.php;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/csnexplore.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/csnexplore.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # API routing
    location /api/ {
        try_files $uri $uri/ /php/index.php?$query_string;
    }
    
    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
    
    location ~ \.(log|sql|db)$ {
        deny all;
    }
    
    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/csnexplore /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## SSL/HTTPS Setup

### Using Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

# For Apache
sudo certbot --apache -d csnexplore.com -d www.csnexplore.com

# For Nginx
sudo certbot --nginx -d csnexplore.com -d www.csnexplore.com

# Auto-renewal
sudo certbot renew --dry-run
```

### Manual SSL Certificate
1. Obtain SSL certificate from your provider
2. Place files in `/etc/ssl/csnexplore/`
3. Update web server configuration with certificate paths

---

## Backup Configuration

### Automated Backups

Create cron job for daily backups:
```bash
# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /usr/bin/php /var/www/csnexplore/php/backup.php create >> /var/log/csnexplore-backup.log 2>&1
```

### Manual Backup
```bash
# Create backup
php php/backup.php create

# List backups
php php/backup.php list

# Backups are stored in: backups/
```

### Backup to Remote Storage (Optional)
```bash
# Sync to AWS S3
aws s3 sync backups/ s3://your-bucket/csnexplore-backups/

# Or use rsync to remote server
rsync -avz backups/ user@remote-server:/backups/csnexplore/
```

---

## Monitoring & Logging

### Enable Logging
Logs are automatically created in `logs/` directory.

View recent logs:
```bash
tail -f logs/$(date +%Y-%m-%d).log
```

### Log Rotation
Create `/etc/logrotate.d/csnexplore`:
```
/var/www/csnexplore/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

### Monitoring Tools

**Server Monitoring:**
- Install monitoring tools like Nagios, Zabbix, or New Relic
- Monitor disk space, CPU, memory, and network

**Application Monitoring:**
- Check logs regularly: `php php/logger.php`
- Monitor API response times
- Track error rates

**Uptime Monitoring:**
- Use services like UptimeRobot, Pingdom, or StatusCake
- Set up alerts for downtime

---

## Troubleshooting

### Common Issues

**1. 500 Internal Server Error**
- Check PHP error logs: `tail -f /var/log/apache2/error.log`
- Verify file permissions
- Check `.htaccess` syntax

**2. Database Connection Failed**
- Verify database file exists: `ls -la database/travelhub.db`
- Check file permissions: `chmod 644 database/travelhub.db`
- Verify PDO extension: `php -m | grep pdo`

**3. Images Not Loading**
- Check images directory permissions: `chmod 755 public/images/`
- Verify image paths in database
- Check web server configuration

**4. API Endpoints Not Working**
- Verify mod_rewrite is enabled (Apache): `a2enmod rewrite`
- Check API routing in `.htaccess` or nginx config
- Test with: `curl https://csnexplore.com/api/listings?category=stays`

**5. Rate Limiting Issues**
- Clear rate limit cache: `rm -rf cache/rate-limit/*`
- Adjust limits in `.env`
- Check IP detection in `php/rate-limiter.php`

### Debug Mode

Enable debug mode temporarily:
```env
APP_DEBUG=true
```

**Warning:** Never enable debug mode in production!

### Performance Issues

**Optimize Database:**
```bash
# SQLite
sqlite3 database/travelhub.db "VACUUM;"
sqlite3 database/travelhub.db "ANALYZE;"

# MySQL
mysql -u root -p -e "OPTIMIZE TABLE csnexplore.*;"
```

**Clear Cache:**
```bash
rm -rf cache/*
```

**Enable OPcache:**
Add to `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

---

## Post-Deployment Checklist

- [ ] SSL certificate installed and working
- [ ] All file permissions set correctly
- [ ] Database initialized and populated
- [ ] `.env` configured with production values
- [ ] Debug mode disabled
- [ ] Security headers enabled
- [ ] Rate limiting configured
- [ ] Backups scheduled
- [ ] Monitoring set up
- [ ] Logs rotation configured
- [ ] Sitemap generated
- [ ] robots.txt configured
- [ ] Admin credentials changed
- [ ] Test all major features
- [ ] Performance testing completed

---

## Support

For deployment support:
- Email: support@csnexplore.com
- Documentation: https://csnexplore.com/docs
- GitHub Issues: https://github.com/your-repo/csnexplore/issues

---

**Last Updated:** 2024-01-15
**Version:** 1.0.0
