# Admin Panel - Quick Test Guide

## Login Credentials

### Admin Accounts
```
Username: omkeshadmin
Password: omkeshAa.1@

OR

Username: rupeshadmin
Password: rupeshAa.1@

OR

Email: admin@csnexplore.com
Password: admin123
```

## Quick Test Sequence (5 minutes)

### 1. Dashboard (30 seconds)
- [ ] Navigate to `/admin/dashboard.php`
- [ ] Verify stats display (bookings, users, blogs, listings)
- [ ] Click "Refresh" button
- [ ] Check recent bookings table

### 2. Listings (1 minute)
- [ ] Go to Listings
- [ ] Click each category tab (Hotels, Cars, Bikes, Dining, Attractions, Buses)
- [ ] Click "Add New Listing"
- [ ] Fill in basic info and save
- [ ] Verify listing appears in table
- [ ] Edit the listing
- [ ] Delete the listing

### 3. Bookings (1 minute)
- [ ] Go to Bookings
- [ ] Filter by status (Pending, Completed, Cancelled)
- [ ] Search for a booking
- [ ] Click on a booking to view details
- [ ] Update status
- [ ] Verify activity log entry

### 4. Blogs (1 minute)
- [ ] Go to Blogs
- [ ] Click "New Post"
- [ ] Enter title and content
- [ ] Upload image
- [ ] Publish
- [ ] Verify blog appears in list
- [ ] Edit blog
- [ ] Delete blog

### 5. Gallery (1 minute)
- [ ] Go to Gallery
- [ ] Click "Upload New"
- [ ] Select an image
- [ ] Verify upload completes
- [ ] Click image to preview in lightbox
- [ ] Copy URL
- [ ] Delete image

### 6. Users (30 seconds)
- [ ] Go to Users
- [ ] Search for a user
- [ ] Change user role
- [ ] Verify changes saved

### 7. Activity Logs (30 seconds)
- [ ] Go to Activity Logs
- [ ] Filter by action type
- [ ] Search for recent actions
- [ ] Verify all your changes are logged

## Feature Verification Checklist

### Core Features
- [ ] Dashboard loads and displays metrics
- [ ] All 6 listing categories work
- [ ] Add/Edit/Delete listings works
- [ ] Gallery upload and preview works
- [ ] Bookings can be created and updated
- [ ] Trip requests display correctly
- [ ] Blogs can be created and published
- [ ] Users can be managed
- [ ] Content can be edited
- [ ] Activity logs record all actions

### UI/UX
- [ ] Sidebar navigation works
- [ ] Mobile menu toggle works
- [ ] Search functionality works
- [ ] Filters work correctly
- [ ] Modals open and close properly
- [ ] Toast notifications appear
- [ ] Responsive design works on mobile

### Security
- [ ] Login requires credentials
- [ ] Invalid credentials rejected
- [ ] Logout clears session
- [ ] Admin-only pages require auth
- [ ] Token expiration works
- [ ] Rate limiting prevents brute force

### Performance
- [ ] Pages load quickly
- [ ] Images load smoothly
- [ ] No console errors
- [ ] No network errors
- [ ] Animations are smooth

## Common Issues & Solutions

### Issue: "Unauthorized" Error
**Solution:** 
- Clear browser cache and localStorage
- Log out and log back in
- Check JWT token in browser console: `localStorage.getItem('csn_admin_token')`

### Issue: Images Not Uploading
**Solution:**
- Check `/images/uploads/` directory exists and is writable
- Verify file size < 5MB
- Check file format (JPG, PNG, WebP, GIF)
- Check browser console for errors

### Issue: Bookings Not Showing
**Solution:**
- Verify database connection in `.env`
- Check if bookings table exists: `SHOW TABLES;`
- Check error logs: `/logs/php_errors.log`

### Issue: Email Notifications Not Sending
**Solution:**
- Verify SMTP credentials in `.env`
- Check `/logs/smtp_test_output.txt`
- Verify email service is configured
- Check spam folder

### Issue: Activity Logs Empty
**Solution:**
- Verify activity_logs table exists
- Check if logging is enabled in API files
- Verify database connection

## Database Verification

### Check Database Connection
```bash
# From command line
mysql -h localhost -u root -p csnexplore
```

### Verify All Tables Exist
```sql
SHOW TABLES;
```

Should show:
- activity_logs
- about_contact
- bikes
- blogs
- bookings
- buses
- cars
- contact_messages
- email_verification_tokens
- password_resets
- restaurants
- room_types
- rooms
- stays
- trip_requests
- users
- vendors

### Check Admin User
```sql
SELECT * FROM users WHERE role = 'admin';
```

Should show at least one admin user.

## API Testing

### Test Dashboard API
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/php/api/dashboard.php
```

### Test Listings API
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/php/api/listings.php?category=stays
```

### Test Bookings API
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/php/api/bookings.php
```

### Test Trip Requests API
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/php/api/trip_requests.php
```

## Performance Metrics

### Expected Load Times
- Dashboard: < 1 second
- Listings: < 1 second
- Bookings: < 1 second
- Blogs: < 1 second
- Gallery: < 2 seconds (with images)
- Users: < 1 second
- Activity Logs: < 1 second

### Database Query Performance
- List queries: < 100ms
- Insert queries: < 50ms
- Update queries: < 50ms
- Delete queries: < 50ms

## Troubleshooting Commands

### Check PHP Errors
```bash
tail -f logs/php_errors.log
```

### Check Database Connection
```bash
php -r "require 'php/config.php'; echo getDB()->getConnection() ? 'Connected' : 'Failed';"
```

### Test JWT Token
```bash
php -r "require 'php/jwt.php'; \$token = createJWT(['id'=>1,'role'=>'admin'], 'test'); echo \$token;"
```

### Clear Rate Limit Cache
```bash
rm -rf logs/rate_limit/*
```

## Success Indicators

✅ All pages load without errors  
✅ All CRUD operations work  
✅ Search and filter work  
✅ Images upload and display  
✅ Activity logs record actions  
✅ Responsive design works  
✅ No console errors  
✅ No network errors  
✅ Performance is acceptable  

---

**If all checks pass, the admin panel is ready for production use!**
