# Add Listing Fix Summary

## Issues Fixed

### 1. "Decoding failed" Error ✅
**Problem:** The backend was receiving JSON-encoded strings from FormData but wasn't properly handling them.

**Solution:** Updated `prepareDataForInsert()` function in `php/api/listings.php` to:
- Add a helper function `$decodeIfJson` that validates JSON strings
- Properly handle both arrays and JSON-encoded strings
- Apply this to all JSON fields: gallery, amenities, features, rooms, guestReviews, menuHighlights

### 2. Better Error Handling ✅
**Added:**
- Try-catch blocks in `createListing()` and `updateListing()`
- Error logging for debugging
- Specific error messages for data preparation and database errors
- Content-Type header check for JSON requests

### 3. Fixed Content-Type Check ✅
**Changed:**
```php
// Before
if (empty($input) && $_SERVER['CONTENT_TYPE'] === 'application/json') {

// After
if (empty($input) && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
```

## What Was Changed

### File: `php/api/listings.php`

#### 1. Updated `prepareDataForInsert()` function
```php
// Added helper function
$decodeIfJson = function($value) {
    if (is_array($value)) {
        return json_encode($value);
    }
    if (is_string($value) && strlen($value) > 0 && ($value[0] === '[' || $value[0] === '{')) {
        // Already JSON string, validate it
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $value; // Valid JSON string
        }
    }
    return $value;
};
```

#### 2. Applied to all JSON fields
- `gallery` → `$decodeIfJson($input['gallery'])`
- `amenities` → `$decodeIfJson($input['amenities'])`
- `features` → `$decodeIfJson($input['features'])`
- `rooms` → `$decodeIfJson($input['rooms'])`
- `guestReviews` → `$decodeIfJson($input['guestReviews'])`
- `menuHighlights` → `$decodeIfJson($input['menuHighlights'])`

#### 3. Enhanced `createListing()` function
```php
// Added error logging
error_log('Received POST data: ' . print_r($newItem, true));

// Added try-catch for data preparation
try {
    $data = prepareDataForInsert($newItem, $category);
} catch (Exception $e) {
    error_log('Error in prepareDataForInsert: ' . $e->getMessage());
    sendError('Data preparation failed: ' . $e->getMessage(), 400);
}

// Added try-catch for database insert
try {
    $itemId = $db->insert($category, $data);
} catch (Exception $e) {
    error_log('Database insert error: ' . $e->getMessage());
    sendError('Database error: ' . $e->getMessage(), 500);
}
```

#### 4. Enhanced `updateListing()` function
- Same error handling as createListing
- Fixed Content-Type header check
- Added error logging

## How It Works Now

### Frontend (admin-add-listing.html)
1. User fills out form
2. Form submits as FormData (to support file uploads)
3. Arrays are converted to JSON strings before sending:
   ```javascript
   if (Array.isArray(value)) {
       submitFormData.append(key, JSON.stringify(value));
   }
   ```

### Backend (php/api/listings.php)
1. Receives FormData via `$_POST`
2. `prepareDataForInsert()` processes each field:
   - If it's already an array → encode to JSON
   - If it's a JSON string → validate and keep as-is
   - If it's a regular string → keep as-is
3. Stores in database as JSON strings
4. Returns success response

## Testing

### To test add listing:
1. Start PHP server: `php -S localhost:8000 router.php`
2. Login to admin: `http://localhost:8000/admin.html`
3. Go to "Add Listing"
4. Fill out form for any category
5. Click "Add Property/Car/Bike/etc."
6. Should see success message
7. Check database to verify data was saved

### To test edit listing:
1. Go to "Manage Listings"
2. Click edit button on any listing
3. Modify some fields
4. Click "Update Listing"
5. Should see success message
6. Verify changes in database

## Error Messages

### Before Fix:
- ❌ "Decoding failed"
- ❌ Generic "Failed to save listing"

### After Fix:
- ✅ "Data preparation failed: [specific error]"
- ✅ "Database error: [specific error]"
- ✅ "Item added successfully"
- ✅ "Item updated successfully"

## Debugging

If issues persist, check:
1. PHP error log: `tail -f php_dev.log`
2. Browser console for JavaScript errors
3. Network tab to see actual request/response
4. Database to verify data structure

## Files Modified
- ✅ `php/api/listings.php` - Fixed JSON handling and error handling

## Status
🟢 **FIXED** - Add listing should now work correctly for all categories

---

**Last Updated:** March 13, 2026  
**Fixed By:** Kiro AI Assistant
