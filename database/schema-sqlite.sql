-- TravelHub Database Schema (SQLite)
-- Version: 2.0.0 - Fully Dynamic

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    is_verified INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (role IN ('user', 'admin', 'vendor'))
);

-- Vendors Table
CREATE TABLE IF NOT EXISTS vendors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    description TEXT,
    rating DECIMAL(3, 2) DEFAULT 0,
    total_bookings INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Stays Table
CREATE TABLE IF NOT EXISTS stays (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vendor_id INTEGER,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    amenities TEXT,
    room_type VARCHAR(100),
    max_guests INTEGER DEFAULT 2,
    top_location_rating VARCHAR(10),
    breakfast_info TEXT,
    rooms TEXT,
    guest_reviews TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- Cars Table
CREATE TABLE IF NOT EXISTS cars (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vendor_id INTEGER,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_day DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    features TEXT,
    fuel_type VARCHAR(50),
    transmission VARCHAR(50),
    seats INTEGER DEFAULT 5,
    guest_reviews TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- Bikes Table
CREATE TABLE IF NOT EXISTS bikes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vendor_id INTEGER,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_day DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    features TEXT,
    fuel_type VARCHAR(50),
    cc INTEGER,
    guest_reviews TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- Restaurants Table
CREATE TABLE IF NOT EXISTS restaurants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vendor_id INTEGER,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    cuisine VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_person DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    menu_highlights TEXT,
    guest_reviews TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- Attractions Table
CREATE TABLE IF NOT EXISTS attractions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    description TEXT,
    entry_fee DECIMAL(10, 2) DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    opening_hours VARCHAR(100),
    best_time VARCHAR(100),
    guest_reviews TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Buses Table
CREATE TABLE IF NOT EXISTS buses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vendor_id INTEGER,
    operator VARCHAR(255) NOT NULL,
    bus_type VARCHAR(100),
    from_location VARCHAR(255) NOT NULL,
    to_location VARCHAR(255) NOT NULL,
    departure_time VARCHAR(50),
    arrival_time VARCHAR(50),
    duration VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    badge VARCHAR(100),
    image VARCHAR(255),
    gallery TEXT,
    amenities TEXT,
    seats_available INTEGER DEFAULT 40,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- Bookings Table (booking call requests from public users)
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    booking_date VARCHAR(100) NOT NULL,
    number_of_people INTEGER NOT NULL,
    service_page VARCHAR(255),
    listing_id INTEGER,
    listing_name VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (status IN ('pending', 'completed', 'cancelled'))
);

-- Homepage Content Table
CREATE TABLE IF NOT EXISTS homepage_content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    display_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- About & Contact Content Table
CREATE TABLE IF NOT EXISTS about_contact (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section VARCHAR(100) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for Performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_stays_location ON stays(location);
CREATE INDEX IF NOT EXISTS idx_cars_location ON cars(location);
CREATE INDEX IF NOT EXISTS idx_bikes_location ON bikes(location);
CREATE INDEX IF NOT EXISTS idx_restaurants_location ON restaurants(location);
CREATE INDEX IF NOT EXISTS idx_attractions_location ON attractions(location);
CREATE INDEX IF NOT EXISTS idx_buses_from ON buses(from_location);
CREATE INDEX IF NOT EXISTS idx_buses_to ON buses(to_location);
CREATE INDEX IF NOT EXISTS idx_bookings_user ON bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_category ON bookings(category);


-- Index for faster queries
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_bookings_created ON bookings(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_bookings_email ON bookings(email);
