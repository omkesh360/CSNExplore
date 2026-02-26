# TravelHub - PHP Travel Booking Platform

TravelHub is a comprehensive travel booking platform for Chhatrapati Sambhajinagar, featuring Stays, Car/Bike Rentals, Restaurants, and Attractions. Built with PHP backend and vanilla JavaScript frontend.

## 🚀 Quick Start

### Requirements
- PHP 7.4 or higher
- Apache with mod_rewrite (or Nginx)

### Start in 30 Seconds

**Linux/Mac:**
```bash
./start-php.sh
```

**Windows:**
```bash
start-php.bat
```

**Manual:**
```bash
php -S localhost:8000
```

Then open: **http://localhost:8000**

## Features

- **Full-featured Listing Pages**: Stays, Car Rentals, Bike Rentals, Restaurants, Attractions, Buses.
- **Dynamic Search**: Interactive search components with real-time filters.
- **Authentication**:
  - Secure Login/Register with JWT (JSON Web Tokens).
  - Role-Based Access Control (Admin vs User).
  - **Admin Dashboard**: Manage bookings, users, and inventory.
  - **User Dashboard**: View booking history and profile.
- **Performance**:
  - Validated HTML5/CSS3.
  - Optimized asset loading.
  - Mobile-responsive design using Tailwind CSS.
- **Security**:
  - Helmet for HTTP headers.
  - Bcrypt for password hashing.
  - Input validation.

## Tech Stack

- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Data**: JSON-based storage (migratable to MySQL/PostgreSQL)
- **Security**: JWT, bcrypt, rate limiting, CORS, security headers

## Setup & Installation

1.  **Quick Start**:
    ```bash
    ./start-php.sh        # Linux/Mac
    start-php.bat         # Windows
    ```

2.  **Manual Start**:
    ```bash
    php -S localhost:8000
    ```
    Access the app at `http://localhost:8000`.

3.  **System Check**:
    ```bash
    php php/test.php
    ```

## Project Structure

- `public/`: Static assets (HTML, CSS, Images, JS)
- `php/`: PHP backend (API endpoints, core files)
- `data/`: JSON data files for persistence
- `.htaccess`: Apache URL rewriting
- `index.php`: Entry point

## Credentials

- **Admin Login**: `admin@travelhub.com` / `password123`
- **User Login**: `rahul@example.com` / `password123`

## License

Private.


## 📚 Documentation

- **[QUICK-START.md](QUICK-START.md)** - 5-minute guide
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Production deployment
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture
- **[FAQ.md](FAQ.md)** - Common questions
- **[INDEX.md](INDEX.md)** - All documentation

## 🛠️ Utilities

### Reset Admin Password
```bash
php php/reset_password.php
```

### Create New Admin
```bash
php php/create_admin.php
```

### Test API
```bash
./test-api.sh php
```

## 🚢 Deployment

**Shared Hosting:** Upload files, set permissions, done!

**VPS/Dedicated:** See [DEPLOYMENT.md](DEPLOYMENT.md) for Apache/Nginx setup

**SSL/HTTPS:** Use Let's Encrypt for free SSL certificates

## ✨ Why PHP?

- ✅ **Universal hosting** - Works on shared hosting
- ✅ **Simple deployment** - Just upload files
- ✅ **No dependencies** - Uses native PHP
- ✅ **Cost effective** - Lower hosting costs
- ✅ **Production ready** - Proven technology
