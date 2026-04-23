# CSNExplore Trip Planner - Verification & Testing Guide

## 🧪 How to Test the Changes

### Test URL 1: Trip Planner Form
**URL:** http://localhost/CSNExplore/suggestor

**What to Check:**
1. ✅ Scroll to "Stay & Travel Preferences" section
2. ✅ Look for "Number of People Travelling" with +/- buttons
3. ✅ Find "Preferred Travel Mode" - should show Private Car, Bike Rental, City Bus, **and Mini Bus**
4. ✅ Under "Select Vehicle Type" - should show SUV, Sedan, MUV, **and Mini Bus**
5. ✅ Look for "Driver Preference" section with radio buttons for:
   - Self Driven
   - With Driver

**Test Flow:**
1. Fill in form with test data
2. Select different options (especially Mini Bus)
3. Adjust number of travelers with +/- buttons
4. Choose driver preference
5. Submit form

---

### Test URL 2: Admin Trip Requests
**URL:** http://localhost/CSNExplore/admin/trip-requests.php

**What to Check:**
1. ✅ Desktop table view shows "Details" column with:
   - Interests
   - Stay Type
   - Travel Mode
   - **No. of Travelers** (NEW)
   - **Driver Preference** (NEW)
   - **Vehicle Type** (NEW)

2. ✅ Click "View Details" button to see modal with all information:
   - No. of Travellers: Shows number with "person(s)"
   - Driver Preference: Shows "Self Driven" or "With Driver"
   - Vehicle Type: Shows selected vehicle
   - Bike Type: Shows if bike was selected

3. ✅ Mobile view shows all same information in card format

---

### Test URL 3: Car Listing Pages
**URL:** http://localhost/CSNExplore/listing/cars

Then click on any car (e.g., cars-1-maruti-suzuki-ertiga)

**What to Check:**
1. ✅ Booking form includes driver preference options
2. ✅ Vehicle type information displayed
3. ✅ Self-drive vs. with-driver options clear
4. ✅ Number of travelers field present

---

## 📊 Database Verification

### Check Table Structure:
```sql
-- Connect to MySQL and run:
USE csnexplore;
DESCRIBE trip_requests;
```

**Expected Columns (Including New Ones):**
- id (INT, PK, AUTO_INCREMENT)
- full_name (VARCHAR 255)
- email (VARCHAR 255)
- phone (VARCHAR 50)
- interests (TEXT)
- stay_type (VARCHAR 100)
- travel_mode (VARCHAR 100)
- travel_details (TEXT)
- **car_service_type (VARCHAR 50)** ← NEW
- **car_sub_type (VARCHAR 100)** ← NEW
- **bike_sub_type (VARCHAR 100)** ← NEW
- **num_people (INT(11))** ← NEW
- extra_notes (TEXT)
- status (ENUM)
- created_at (DATETIME)
- updated_at (DATETIME)

### Check Sample Data:
```sql
-- See sample trip requests with new fields:
SELECT id, full_name, num_people, car_service_type, car_sub_type, travel_mode 
FROM trip_requests 
LIMIT 5;
```

---

## 🔍 Code Review Points

### File 1: suggestor.php
**Lines:** 45-67 (Form Processing)

**Check:**
- ✅ Form captures `num_people` from input
- ✅ Form captures `car_service_type` (SelfDrive/WithDriver)
- ✅ Form captures `car_sub_type` (SUV/Sedan/MUV/MiniBus)
- ✅ Form captures `bike_sub_type` (Cruiser/Scooter/Sports)
- ✅ All values inserted into database

### File 2: admin/trip-requests.php
**Lines:** 280-310 (Modal Display)

**Check:**
- ✅ Modal displays `num_people` with "person(s)" label
- ✅ Modal displays `car_service_type` with readable format
- ✅ Modal displays `car_sub_type` as vehicle type
- ✅ Modal displays `bike_sub_type` when applicable

**Lines:** 150-160 (Table Display)

**Check:**
- ✅ Table row shows all new fields in details column
- ✅ Driver preference formatted as "Self Driven" or "With Driver"
- ✅ Traveler count shown

### File 3: database/add_trip_requests_table.sql
**Check:**
- ✅ Contains all 4 new columns
- ✅ Proper data types (VARCHAR, INT)
- ✅ No duplicate columns
- ✅ Proper CREATE TABLE syntax

---

## ✅ Pre-Launch Checklist

### Database
- [x] Migration script created with new columns
- [x] Migration applied to database
- [x] New columns visible in DESCRIBE command
- [x] No errors during migration

### Form (suggestor.php)
- [x] Mini Bus option visible in travel mode
- [x] Mini Bus option visible in vehicle type
- [x] Number of travelers counter working (+/- buttons)
- [x] Driver preference section present
- [x] Form properly stores all new fields

### Admin Panel (trip-requests.php)
- [x] Modal displays number of travelers
- [x] Modal displays driver preference
- [x] Modal displays vehicle type
- [x] Modal displays bike type
- [x] Table view updated with new details
- [x] Mobile and desktop views both updated

### Car Listings
- [x] Booking form includes driver preference
- [x] Vehicle type information shown
- [x] Self-drive and with-driver options clear

---

## 🚨 Troubleshooting

### If Trip Requests Page Shows Error:

1. **Error:** "Table trip_requests not found"
   - **Solution:** Run the migration script again
   - Command: `C:\xampp\mysql\bin\mysql.exe -u root csnexplore < "C:\xampp\htdocs\CSNExplore\database\add_trip_requests_table.sql"`

2. **Error:** New fields not appearing in admin panel
   - **Solution:** Clear browser cache (Ctrl+F5)
   - Check that admin/trip-requests.php has the latest code

3. **Error:** Form data not saving
   - **Solution:** Check that all column names in suggestor.php match database schema
   - Verify MySQL connection in php/config.php

---

## 📞 Support Information

**Key Files for Reference:**
- Trip Planner Form: `/suggestor.php`
- Admin Panel: `/admin/trip-requests.php`
- Database Schema: `/database/add_trip_requests_table.sql`
- Configuration: `/php/config.php`

**Contact Points:**
- Form Submissions: suggestor.php processes POST
- Admin Display: trip-requests.php shows all data
- Database: csnexplore.trip_requests table

---

## 🎯 Success Criteria

All of the following should be working:

1. ✅ Mini Bus appears in vehicle type selection
2. ✅ Number of people can be adjusted with +/- buttons
3. ✅ Driver preference can be selected (Self Driven or With Driver)
4. ✅ Admin sees all trip request details with new fields
5. ✅ Data persists correctly in database
6. ✅ Car listing pages support vehicle/driver preferences
7. ✅ No JavaScript errors in browser console
8. ✅ Database has trip_requests table with new columns

---

**Version:** 1.0
**Last Updated:** April 23, 2026
**Status:** ✅ COMPLETE & TESTED
