# CSNExplore - Quick Reference Card

## Clean URLs - All Pages

### Main Pages
```
/                    → Homepage
/about               → About Us
/contact             → Contact Us
/blogs               → Blog Listing
/suggestor           → Trip Planner
```

### Listing Pages
```
/listing/stays       → Hotels & Stays
/listing/cars        → Car Rentals
/listing/bikes       → Bike Rentals
/listing/restaurants → Restaurants
/listing/attractions → Tourist Attractions
/listing/buses       → Bus Services
```

### Detail Pages
```
/listing-detail/stays-{id}-{slug}       → Hotel Details
/listing-detail/cars-{id}-{slug}        → Car Details
/listing-detail/bikes-{id}-{slug}       → Bike Details
/listing-detail/restaurants-{id}-{slug} → Restaurant Details
/listing-detail/attractions-{id}-{slug} → Attraction Details
/listing-detail/buses-{id}-{slug}       → Bus Details
/blogs/{id}-{slug}                      → Blog Post
```

## Important Commands

### Verify Listings
```bash
C:\xampp\php\php.exe php/regenerate-complete.php
```

### Check PHP Syntax
```bash
C:\xampp\php\php.exe -l filename.php
```

### Start Apache (if needed)
```bash
C:\xampp\apache\bin\httpd.exe
```

## Key Files

### Configuration
- `.env` - Database credentials
- `.htaccess` - URL rewriting rules
- `php/config.php` - Database connection

### Dynamic Pages
- `listing-detail.php` - All listing details
- `blog-detail.php` - All blog posts
- `listing.php` - Category listings
- `index.php` - Homepage

### No Static Files
- ❌ No HTML files in `listing-detail/`
- ✅ All pages served dynamically
- ✅ Database changes reflect immediately

## Database Tables
```
stays        - Hotels & accommodations
cars         - Car rentals
bikes        - Bike rentals
restaurants  - Dining options
attractions  - Tourist sites
buses        - Bus services
blogs        - Blog posts
users        - User accounts
bookings     - Booking requests
```

## URL Rules

### ✅ DO:
```html
<a href="/listing-detail/cars-1-sedan">View Car</a>
<a href="/about">About Us</a>
<a href="/contact">Contact</a>
```

### ❌ DON'T:
```html
<a href="/listing-detail.php?category=cars&id=1">View Car</a>
<a href="/about.php">About Us</a>
<a href="/listing-detail/cars-1-sedan.html">View Car</a>
```

## Image Paths

### ✅ Correct:
```html
<img src="/images/logo.png">
<img src="/images/uploads/photo.jpg">
```

### ❌ Wrong:
```html
<img src="images/logo.png">
<img src="../images/logo.png">
```

## Quick Checks

### Is mod_rewrite enabled?
```bash
C:\xampp\apache\bin\httpd.exe -M | findstr rewrite
```

### Test a URL:
```
http://localhost/CSNexplore/CSNExplore/listing-detail/cars-1-mahindra-scorpio-n
```

### Check database connection:
```bash
C:\xampp\php\php.exe -r "require 'php/config.php'; echo 'Connected!';"
```

## Common Issues

### 404 Error
- Check `.htaccess` exists
- Verify `mod_rewrite` is enabled
- Check file permissions

### Images Not Loading
- Use absolute paths from root
- Check image file exists
- Verify file permissions

### Database Error
- Check `.env` credentials
- Verify database exists
- Check MySQL is running

## Contact Info

### Support Email
supportcsnexplore@gmail.com

### Phone
+91 86009 68888

### WhatsApp
+91 86009 68888

## Deployment

### Localhost
```
http://localhost/CSNexplore/CSNExplore/
```

### Production
```
https://csnexplore.com/
```

### Hostinger Database
```
Host: localhost
Database: u108326050_csnexploredb
User: u108326050_omkesh360
Password: omkeshAa.1@
```

## Quick Stats

- **74 Active Listings** across 6 categories
- **603 Blog Posts** published
- **100% Clean URLs** implemented
- **0 Static HTML Files** (all dynamic)

---

**Last Updated:** April 17, 2026
