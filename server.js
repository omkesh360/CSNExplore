const express = require('express');
const path = require('path');
const fs = require('fs');
const helmet = require('helmet');
const morgan = require('morgan');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const multer = require('multer');

const app = express();

// SECURITY CONFIGURATION
// Use Helmet for secure HTTP headers
app.use(helmet({
  contentSecurityPolicy: false, // Disabled for simple local dev/images, enable in strict production
}));

// Logging
app.use(morgan('dev'));

// CORS
app.use(cors());

// Middleware to parse JSON bodies
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Disable caching for all API routes
app.use('/api', (req, res, next) => {
  res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
  res.setHeader('Pragma', 'no-cache');
  res.setHeader('Expires', '0');
  next();
});

// Rate Limiting (Custom In-Memory Implementation)
const rateLimitMap = new Map();
const RATE_LIMIT_WINDOW = 15 * 60 * 1000; // 15 minutes
const RATE_LIMIT_MAX = 100; // Limit each IP to 100 requests per window

const limiter = (req, res, next) => {
  const ip = req.ip || req.connection.remoteAddress;
  const now = Date.now();

  if (!rateLimitMap.has(ip)) {
    rateLimitMap.set(ip, { count: 1, startTime: now });
    return next();
  }

  const data = rateLimitMap.get(ip);

  if (now - data.startTime > RATE_LIMIT_WINDOW) {
    // Reset window
    data.count = 1;
    data.startTime = now;
    return next();
  }

  if (data.count >= RATE_LIMIT_MAX) {
    return res.status(429).json({ error: 'Too many requests, please try again later.' });
  }

  data.count++;
  next();
};

// Apply rate limiting to all requests
app.use('/api/', limiter);


// CONSTANTS
const JWT_SECRET = process.env.JWT_SECRET || 'travelhub_secure_secret_key_2024';
const DATA_DIR = path.join(__dirname, 'data');

// Ensure data directory exists
if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

// ==========================================
// AUTHENTICATION MIDDLEWARE & UTILS
// ==========================================

// Middleware to verify JWT token
const verifyToken = (req, res, next) => {
  const bearerHeader = req.headers['authorization'];

  if (!bearerHeader) {
    return res.status(403).json({ error: 'Access denied. No token provided.' });
  }

  const bearer = bearerHeader.split(' ');
  const token = bearer[1];

  if (!token) {
    return res.status(403).json({ error: 'Access denied. Malformed token.' });
  }

  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    res.status(401).json({ error: 'Invalid token.' });
  }
};

// Middleware to check Admin role
const isAdmin = (req, res, next) => {
  if (req.user && req.user.role === 'Admin') {
    next();
  } else {
    res.status(403).json({ error: 'Access denied. Admin privileges required.' });
  }
};

// ==========================================
// AUTH ROUTES
// ==========================================

// Register Endpoint
app.post('/api/auth/register', async (req, res) => {
  const { name, email, password } = req.body;

  if (!name || !email || !password) {
    return res.status(400).json({ error: 'Name, email and password are required' });
  }

  if (password.length < 6) {
    return res.status(400).json({ error: 'Password must be at least 6 characters' });
  }

  const usersFilePath = path.join(DATA_DIR, 'users.json');

  let users = [];
  try {
    if (fs.existsSync(usersFilePath)) {
      users = JSON.parse(fs.readFileSync(usersFilePath, 'utf8'));
    }
  } catch (e) { users = []; }

  // Check if email already exists
  if (users.find(u => u.email.toLowerCase() === email.toLowerCase())) {
    return res.status(409).json({ error: 'An account with this email already exists' });
  }

  try {
    const hashedPassword = await bcrypt.hash(password, 10);
    const newUser = {
      id: Date.now().toString(),
      name,
      email,
      role: 'user',
      password: hashedPassword,
      createdAt: new Date().toISOString()
    };

    users.push(newUser);
    fs.writeFileSync(usersFilePath, JSON.stringify(users, null, 2));

    const token = jwt.sign(
      { id: newUser.id, email: newUser.email, role: newUser.role, name: newUser.name },
      JWT_SECRET,
      { expiresIn: '2h' }
    );

    res.status(201).json({
      success: true,
      user: { id: newUser.id, email: newUser.email, name: newUser.name, role: newUser.role },
      token
    });
  } catch (err) {
    console.error('Register error:', err);
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Login Endpoint
app.post('/api/auth/login', (req, res) => {
  const { email, password } = req.body;

  if (!email || !password) {
    return res.status(400).json({ error: 'Email and password are required' });
  }

  const usersFilePath = path.join(DATA_DIR, 'users.json');

  if (!fs.existsSync(usersFilePath)) {
    return res.status(401).json({ error: 'Invalid credentials' });
  }

  fs.readFile(usersFilePath, 'utf8', (err, data) => {
    if (err) {
      console.error('Error reading users:', err);
      return res.status(500).json({ error: 'Internal server error' });
    }

    try {
      const users = JSON.parse(data);
      const user = users.find(u => u.email.toLowerCase() === email.toLowerCase());

      if (!user || !user.password) {
        return res.status(401).json({ error: 'Invalid credentials' });
      }

      const passwordMatch = bcrypt.compareSync(password, user.password);
      if (!passwordMatch) {
        return res.status(401).json({ error: 'Invalid credentials' });
      }

      const token = jwt.sign(
        { id: user.id, email: user.email, role: user.role, name: user.name },
        JWT_SECRET,
        { expiresIn: '8h' }
      );

      res.json({
        success: true,
        user: { id: user.id, email: user.email, name: user.name, role: user.role },
        token
      });

    } catch (parseErr) {
      console.error('Error parsing users:', parseErr);
      res.status(500).json({ error: 'Internal server error' });
    }
  });
});

// Verify Token Endpoint (for client-side check)
app.get('/api/auth/me', verifyToken, (req, res) => {
  res.json({ success: true, user: req.user });
});

// ==========================================
// STATIC FILES
// ==========================================

// Serve static files with NO caching
app.use(express.static(path.join(__dirname, 'public'), {
  maxAge: 0,
  etag: false,
  lastModified: false,
  extensions: ['html', 'htm'],
  setHeaders: (res, filePath) => {
    // Disable all caching
    res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
    res.setHeader('Pragma', 'no-cache');
    res.setHeader('Expires', '0');
    res.setHeader('Surrogate-Control', 'no-store');
  }
}));

// Route for root to serve index.html
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// ==========================================
// API ROUTES
// ==========================================

// Configure Multer for file uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const uploadDir = path.join(__dirname, 'public/images/uploads');
    // Ensure directory exists
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: function (req, file, cb) {
    // secure filename: timestamp-original
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({ storage: storage });

// ==========================================
// HOMEPAGE CONTENT API (Public GET, Admin PUT)
// ==========================================

const HOMEPAGE_FILE = path.join(DATA_DIR, 'homepage-content.json');

// GET homepage content (public)
app.get('/api/homepage-content', (req, res) => {
  fs.readFile(HOMEPAGE_FILE, 'utf8', (err, data) => {
    if (err) {
      if (err.code === 'ENOENT') return res.json({});
      return res.status(500).json({ error: 'Failed to read homepage content' });
    }
    try {
      res.json(JSON.parse(data));
    } catch (e) {
      res.status(500).json({ error: 'Failed to parse homepage content' });
    }
  });
});

// PUT homepage content (admin only)
app.put('/api/homepage-content', verifyToken, isAdmin, (req, res) => {
  const content = req.body;
  if (!content || typeof content !== 'object') {
    return res.status(400).json({ error: 'Invalid content body' });
  }
  fs.writeFile(HOMEPAGE_FILE, JSON.stringify(content, null, 2), (err) => {
    if (err) {
      return res.status(500).json({ error: 'Failed to save homepage content' });
    }
    res.json({ success: true, message: 'Homepage content updated successfully' });
  });
});

// Upload a single image (admin only) — returns { url: '/images/uploads/filename.jpg' }
app.post('/api/upload', verifyToken, isAdmin, upload.single('image'), (req, res) => {
  if (!req.file) {
    return res.status(400).json({ error: 'No image file provided' });
  }
  // Return the public URL path relative to /public
  const urlPath = `/images/uploads/${req.file.filename}`;
  res.json({ success: true, url: urlPath });
});

// List all available images (for image browser in admin) — admin only
app.get('/api/images', verifyToken, isAdmin, (req, res) => {
  const imagesDir = path.join(__dirname, 'public/images');
  const uploadsDir = path.join(__dirname, 'public/images/uploads');
  const validExts = new Set(['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.avif']);
  const result = [];

  try {
    // Root images folder
    fs.readdirSync(imagesDir).forEach(f => {
      const ext = path.extname(f).toLowerCase();
      if (validExts.has(ext)) result.push({ name: f, url: `/images/${f}` });
    });
    // Uploads subfolder
    if (fs.existsSync(uploadsDir)) {
      fs.readdirSync(uploadsDir).forEach(f => {
        const ext = path.extname(f).toLowerCase();
        if (validExts.has(ext)) result.push({ name: f, url: `/images/uploads/${f}` });
      });
    }
    res.json(result);
  } catch (e) {
    res.status(500).json({ error: 'Could not list images' });
  }
});


// Get all items for a category
app.get('/api/:category', (req, res) => {

  const category = req.params.category;
  const publicCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
  const adminCategories = ['bookings', 'users', 'vendors'];
  const validCategories = [...publicCategories, ...adminCategories];

  if (!validCategories.includes(category)) {
    return res.status(400).json({ error: 'Invalid category' });
  }

  // SECURITY: Admin-only categories require authentication
  if (adminCategories.includes(category)) {
    return verifyToken(req, res, () => {
      return isAdmin(req, res, () => {
        readCategoryData(category, res);
      });
    });
  }

  // Public categories are accessible without authentication
  readCategoryData(category, res);
});

// Get a single item by ID
app.get('/api/:category/:id', (req, res) => {
  const category = req.params.category;
  const id = req.params.id;
  const validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses', 'users', 'bookings', 'vendors'];

  if (!validCategories.includes(category)) {
    return res.status(400).json({ error: 'Invalid category' });
  }

  // Security: Admin check for protected categories
  const adminCategories = ['users', 'bookings', 'vendors'];
  if (adminCategories.includes(category)) {
    return verifyToken(req, res, () => {
      isAdmin(req, res, () => {
        readCategoryDataById(category, id, res);
      });
    });
  }

  readCategoryDataById(category, id, res);
});

// Helper to read by ID
function readCategoryDataById(category, id, res) {
  const filePath = path.join(DATA_DIR, `${category}.json`);
  fs.readFile(filePath, 'utf8', (err, data) => {
    if (err) {
      if (err.code === 'ENOENT') return res.status(404).json({ error: 'Item not found' });
      return res.status(500).json({ error: 'Server error' });
    }
    try {
      const items = JSON.parse(data);
      const item = items.find(i => i.id.toString() === id.toString());
      if (!item) return res.status(404).json({ error: 'Item not found' });
      res.json(item);
    } catch (e) {
      res.status(500).json({ error: 'Parse error' });
    }
  });
}


// Helper function to read category data
function readCategoryData(category, res) {
  const filePath = path.join(DATA_DIR, `${category}.json`);

  fs.readFile(filePath, 'utf8', (err, data) => {
    if (err) {
      if (err.code === 'ENOENT') {
        return res.json([]);
      }
      console.error(`Error reading ${category} data:`, err);
      return res.status(500).json({ error: 'Failed to retrieve data' });
    }

    try {
      res.json(JSON.parse(data));
    } catch (parseErr) {
      console.error(`Error parsing ${category} data:`, parseErr);
      res.status(500).json({ error: 'Failed to parse data' });
    }
  });
}

// Add a new item
// Listings: Admin only | Bookings: any authenticated user
app.post('/api/:category', verifyToken, upload.single('image'), (req, res) => {
  const category = req.params.category;
  const listingCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
  const userCategories = ['bookings']; // Users can create bookings

  if (!listingCategories.includes(category) && !userCategories.includes(category)) {
    return res.status(400).json({ error: 'Invalid category or operation not allowed' });
  }

  // Listing categories require admin
  if (listingCategories.includes(category) && req.user.role !== 'Admin') {
    return res.status(403).json({ error: 'Admin privileges required' });
  }

  const newItem = { ...req.body };

  // If a file was uploaded, add its path
  if (req.file) {
    newItem.image = `images/uploads/${req.file.filename}`;
  }

  // Type conversions
  if (newItem.rating) newItem.rating = parseFloat(newItem.rating);
  if (newItem.price) newItem.price = parseFloat(newItem.price);
  if (newItem.reviews) newItem.reviews = parseInt(newItem.reviews);
  if (newItem.entryFee) newItem.entryFee = parseInt(newItem.entryFee);

  newItem.id = Date.now().toString();
  newItem.createdAt = new Date().toISOString();
  if (!newItem.reviews) newItem.reviews = 0;

  const filePath = path.join(DATA_DIR, `${category}.json`);

  fs.readFile(filePath, 'utf8', (readErr, data) => {
    let items = [];
    if (!readErr && data) {
      try { items = JSON.parse(data); } catch (e) { }
    }

    items.push(newItem);

    fs.writeFile(filePath, JSON.stringify(items, null, 2), (err) => {
      if (err) {
        console.error(`Error writing ${category}:`, err);
        return res.status(500).json({ error: 'Failed to save data' });
      }
      res.status(201).json({ message: 'Item added successfully', item: newItem });
    });
  });
});

// Update item (Protected)
app.put('/api/:category/:id', verifyToken, isAdmin, upload.single('image'), (req, res) => {
  const category = req.params.category;
  const id = req.params.id; // ID is string now
  const validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

  if (!validCategories.includes(category)) {
    return res.status(400).json({ error: 'Invalid category' });
  }

  const filePath = path.join(DATA_DIR, `${category}.json`);

  if (!fs.existsSync(filePath)) {
    return res.status(404).json({ error: 'Category data not found' });
  }

  try {
    const fileData = fs.readFileSync(filePath, 'utf8');
    let items = JSON.parse(fileData);
    const itemIndex = items.findIndex(item => item.id.toString() === id.toString());

    if (itemIndex === -1) {
      return res.status(404).json({ error: 'Item not found' });
    }

    const updatedItem = { ...items[itemIndex], ...req.body, id: id.toString() };

    if (req.file) {
      updatedItem.image = `images/uploads/${req.file.filename}`;
    }

    if (updatedItem.rating) updatedItem.rating = parseFloat(updatedItem.rating);
    if (updatedItem.price) updatedItem.price = parseFloat(updatedItem.price);

    items[itemIndex] = updatedItem;

    fs.writeFile(filePath, JSON.stringify(items, null, 2), (err) => {
      if (err) {
        return res.status(500).json({ error: 'Failed to save data' });
      }
      res.json({ message: 'Item updated successfully', item: updatedItem });
    });
  } catch (err) {
    res.status(500).json({ error: 'Server error' });
  }
});

// Delete item (Protected)
// ============================================================
// DELETE users (admin-only, with safeguards)
// ============================================================
app.delete('/api/users/:id', verifyToken, isAdmin, (req, res) => {
  const id = req.params.id;
  const filePath = path.join(DATA_DIR, 'users.json');
  try {
    let users = JSON.parse(fs.readFileSync(filePath, 'utf8'));
    const target = users.find(u => u.id.toString() === id.toString());
    if (!target) return res.status(404).json({ error: 'User not found' });
    // Prevent deleting self
    if (req.user && req.user.id && req.user.id.toString() === id.toString()) {
      return res.status(400).json({ error: 'You cannot delete your own account.' });
    }
    // Prevent deleting the last admin
    const remaining = users.filter(u => u.id.toString() !== id.toString());
    const adminsLeft = remaining.filter(u => u.role === 'Admin').length;
    if (target.role === 'Admin' && adminsLeft === 0) {
      return res.status(400).json({ error: 'Cannot delete the last Admin account.' });
    }
    users = remaining;
    fs.writeFileSync(filePath, JSON.stringify(users, null, 2));
    res.json({ success: true, message: 'User deleted successfully' });
  } catch (err) {
    console.error('Delete user error:', err);
    res.status(500).json({ error: 'Failed to delete user' });
  }
});

// DELETE vendors (admin-only)
app.delete('/api/vendors/:id', verifyToken, isAdmin, (req, res) => {
  const id = req.params.id;
  const filePath = path.join(DATA_DIR, 'vendors.json');
  try {
    let vendors = JSON.parse(fs.readFileSync(filePath, 'utf8'));
    const initialLength = vendors.length;
    vendors = vendors.filter(v => v.id.toString() !== id.toString());
    if (vendors.length === initialLength) return res.status(404).json({ error: 'Vendor not found' });
    fs.writeFileSync(filePath, JSON.stringify(vendors, null, 2));
    res.json({ success: true, message: 'Vendor deleted successfully' });
  } catch (err) {
    console.error('Delete vendor error:', err);
    res.status(500).json({ error: 'Failed to delete vendor' });
  }
});

app.delete('/api/:category/:id', verifyToken, isAdmin, (req, res) => {
  const category = req.params.category;
  const id = req.params.id;
  const validCategories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

  if (!validCategories.includes(category)) {
    return res.status(400).json({ error: 'Invalid category' });
  }

  const filePath = path.join(DATA_DIR, `${category}.json`);

  try {
    if (!fs.existsSync(filePath)) return res.status(404).json({ error: 'Category not found' });

    let items = JSON.parse(fs.readFileSync(filePath, 'utf8'));
    const initialLength = items.length;
    items = items.filter(item => item.id.toString() !== id.toString());

    if (items.length === initialLength) {
      return res.status(404).json({ error: 'Item not found' });
    }

    fs.writeFile(filePath, JSON.stringify(items, null, 2), (err) => {
      if (err) return res.status(500).json({ error: 'Failed to save data' });
      res.json({ message: 'Item deleted successfully' });
    });
  } catch (err) {
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Global Error Handler
app.use((err, req, res, next) => {
  console.error('Server Error:', err);
  if (err instanceof multer.MulterError) {
    return res.status(400).json({ error: `Upload error: ${err.message}` });
  }
  res.status(500).json({ error: 'Internal Server Error' });
});

// Export for Vercel
module.exports = app;

// Start server
if (process.env.NODE_ENV !== 'production') {
  const PORT = process.env.PORT || 3000;
  app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
  });
}
