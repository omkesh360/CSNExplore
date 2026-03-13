# CSNExplore Travel Hub - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Prerequisites
- PHP 7.4+ installed
- Web server (Apache/Nginx) or PHP built-in server
- SQLite or MySQL

---

## Installation

### 1. Clone/Download
```bash
git clone https://github.com/your-repo/csnexplore.git
cd csnexplore
```

### 2. Set Permissions
```bash
chmod 755 cache/ data/ database/ logs/ backups/ public/images/
chmod 600 .env admin_credentials.txt
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env with your settings
```

### 4. Initialize Database
```bash
php php/migrate-to-db.php
```

### 5. Start Development Server
```bash
php -S localhost:8000 -t public router.php
```

### 6. Access Application
- **Website:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin-dashboard.html
- **Default Credentials:** Check `admin_credentials.txt`

---

## 📁 Project Structure

```
csnexplore/
├── public/              # Public web files
│   ├── *.html          # HTML pages (108 files)
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── images/         # Image assets
├── php/                # Backend PHP files
│   ├── api/            # API endpoints
│   ├── config.php      # Configuration
│   ├── database.php    # Database connection
│   ├── csrf.php        # CSRF protection
│   ├── input-validator.php  # Input validation
│   ├── logger.php      # Logging system
│   ├── backup.php      # Backup system
│   └── ...
├── data/               # JSON data files
├── database/           # SQLite database
├── cache/              # Cache files
├── logs/               # Application logs
├── backups/            # Automated backups
└── vendor/             # Dependencies
```

---

## 🔐 Security Features

### CSRF Protection
```php
// In forms
<?php
require_once 'php/csrf.php';
echo CSRF::getTokenField();
?>

// Validate
if (!CSRF::validateRequest()) {
    die('Invalid CSRF token');
}
```

### Input Validation
```php
require_once 'php/input-validator.php';

$email = InputValidator::validateEmail($_POST['email']);
$password = InputValidator::validatePassword($_POST['password']);
$name = InputValidator::sanitizeString($_POST['name'], 100);
```

### Rate Limiting
```php
require_once 'php/rate-limiter.php';

$limiter = new RateLimiter(100, 15);
if ($limiter->tooManyAttempts('login')) {
    die('Too many attempts');
}
$limiter->hit('login');
```

### Logging
```php
require_once 'php/logger.php';

Logger::info('User action', ['user_id' => 123]);
Logger::error('Error occurred', ['error' => $message]);
Logger::apiRequest('POST', '/api/endpoint', $data);
```

---

## 🛠️ Common Tasks

### Create Backup
```bash
php php/backup.php create
```

### Generate Sitemap
```bash
php php/generate-sitemap.php
```

### View Logs
```bash
tail -f logs/$(date +%Y-%m-%d).log
```

### Clear Cache
```bash
rm -rf cache/*
```

### Database Operations
```bash
# SQLite
sqlite3 database/travelhub.db "SELECT * FROM users;"

# Backup database
cp database/travelhub.db database/travelhub.db.backup
```

---

## 🔌 API Usage

### Authentication
```bash
curl -X POST http://localhost:8000/api/auth \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Get Listings
```bash
curl http://localhost:8000/api/listings?category=stays
```

### Create Listing (Admin)
```bash
curl -X POST http://localhost:8000/api/listings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"category":"stays","name":"New Hotel","price":3000}'
```

---

## 🎨 Frontend Development

### Key JavaScript Files
- `public/js/app.js` - Main application logic
- `public/js/admin-dashboard.js` - Dashboard functionality
- `public/js/search-custom.js` - Search functionality
- `public/js/marquee.js` - Marquee animations

### Key CSS Files
- `public/css/main.css` - Main styles
- `public/css/admin.css` - Admin panel styles
- `public/css/tailwind.css` - Utility classes

---

## 🐛 Debugging

### Enable Debug Mode
```env
# .env
APP_DEBUG=true
```

### Check PHP Errors
```bash
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
```

### Test API Endpoints
```bash
# Test with curl
curl -v http://localhost:8000/api/listings?category=stays

# Test with browser
http://localhost:8000/api/listings?category=stays
```

---

## 📊 Admin Panel

### Access Admin Panel
1. Navigate to: http://localhost:8000/admin-dashboard.html
2. Login with credentials from `admin_credentials.txt`

### Admin Features
- **Dashboard** - View statistics (6 key metrics)
- **Page Editors** - Edit Homepage, About, Contact pages
- **Listings** - Add, manage, reorder listings
- **Users** - Manage user accounts
- **Images** - Upload and manage images
- **Blogs** - Create and edit blog posts
- **Marquee** - Manage announcements
- **Performance** - Monitor cache and performance

---

## 🧪 Testing

### Manual Testing
1. Test all public pages
2. Test admin panel features
3. Test API endpoints
4. Test authentication
5. Test form submissions
6. Test mobile responsiveness

### Security Testing
```bash
# Test CSRF protection
curl -X POST http://localhost:8000/api/listings \
  -H "Content-Type: application/json" \
  -d '{"name":"Test"}'
# Should fail without CSRF token

# Test rate limiting
for i in {1..150}; do
  curl http://localhost:8000/api/listings?category=stays
done
# Should be blocked after threshold
```

---

## 📚 Documentation

- **API Documentation:** `API_DOCUMENTATION.md`
- **Deployment Guide:** `DEPLOYMENT_GUIDE.md`
- **Security Guide:** `SECURITY_IMPROVEMENTS.md`
- **This Guide:** `QUICK_START.md`

---

## 🆘 Troubleshooting

### Issue: 500 Internal Server Error
```bash
# Check PHP error logs
tail -f /var/log/apache2/error.log

# Check file permissions
ls -la database/
chmod 644 database/travelhub.db
```

### Issue: Database Not Found
```bash
# Initialize database
php php/migrate-to-db.php

# Verify database exists
ls -la database/travelhub.db
```

### Issue: Images Not Loading
```bash
# Check permissions
chmod 755 public/images/
ls -la public/images/
```

### Issue: API Not Working
```bash
# Test API directly
curl http://localhost:8000/api/listings?category=stays

# Check .htaccess (Apache)
cat .htaccess

# Enable mod_rewrite (Apache)
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🚀 Deployment

### Quick Deploy to Production
1. Upload files to server
2. Configure `.env` for production
3. Set file permissions
4. Initialize database
5. Configure web server
6. Enable SSL/HTTPS
7. Set up backups
8. Test everything

**See `DEPLOYMENT_GUIDE.md` for detailed instructions.**

---

## 📞 Support

- **Email:** support@csnexplore.com
- **Documentation:** https://csnexplore.com/docs
- **Issues:** https://github.com/your-repo/csnexplore/issues

---

## ✅ Checklist

### Development Setup
- [ ] PHP installed and configured
- [ ] Web server running
- [ ] Database initialized
- [ ] Environment configured
- [ ] Admin credentials set
- [ ] Test pages loading
- [ ] Test API endpoints

### Production Deployment
- [ ] SSL certificate installed
- [ ] Environment set to production
- [ ] Debug mode disabled
- [ ] Security headers enabled
- [ ] Backups scheduled
- [ ] Monitoring configured
- [ ] All features tested

---

**Happy Coding! 🎉**

For detailed information, refer to the comprehensive documentation files.

---

**Last Updated:** 2024-01-15
**Version:** 1.0.0
