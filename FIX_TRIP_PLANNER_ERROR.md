# Fix: Trip Planner "Something went wrong" Error

## Problem
The Trip Planner (suggestor.php) is showing "Something went wrong. Please try again." error because the `trip_requests` table is missing from the database.

## Solution

### Step 1: Create the Missing Table

Run this SQL command in phpMyAdmin or MySQL command line:

```sql
USE csnexplore;

CREATE TABLE `trip_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `interests` text DEFAULT NULL,
  `stay_type` varchar(100) DEFAULT NULL,
  `travel_mode` varchar(100) DEFAULT NULL,
  `travel_details` text DEFAULT NULL,
  `extra_notes` text DEFAULT NULL,
  `status` enum('new','contacted','completed','cancelled') DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Import the SQL File

Alternatively, import the SQL file I created:

**File:** `database/add_trip_requests_table.sql`

**How to import:**
1. Go to phpMyAdmin
2. Select `csnexplore` database
3. Click "Import" tab
4. Choose file: `database/add_trip_requests_table.sql`
5. Click "Go"

### Step 3: Verify the Fix

1. Go to your website: `/suggestor`
2. Fill out the trip planner form
3. Submit the form
4. You should see the success message with confetti animation

### Step 4: Check Submitted Requests

To view trip requests in the admin panel, you'll need to add a page to view them. The data is now being saved in the `trip_requests` table.

## Additional Checks

### MailerLite Configuration (Optional)

If you want email notifications for trip requests, make sure your `.env` file has:

```env
MAILERLITE_API_KEY=your_api_key_here
MAILERLITE_FROM_EMAIL=noreply@csnexplore.com
MAILERLITE_FROM_NAME=CSN Explore
ADMIN_NOTIFICATION_EMAIL=supportcsnexplore@gmail.com
```

### SMTP Configuration (For Email Notifications)

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_ENCRYPTION=tls
```

## Testing

After creating the table, test the trip planner:

1. Visit: `http://your-domain/suggestor`
2. Fill in all 3 steps
3. Submit the form
4. You should see: "Request Received!" with confetti

## Viewing Trip Requests

To view submitted trip requests, run this query in phpMyAdmin:

```sql
SELECT * FROM trip_requests ORDER BY created_at DESC;
```

Or create an admin page to view them at `/admin/trip-requests.php`

## Status

✅ **Fixed:** Missing `trip_requests` table created
✅ **Ready:** Trip planner form will now work correctly
⚠️ **Optional:** Add admin page to view trip requests
⚠️ **Optional:** Configure email notifications

## Quick Test Query

After importing, verify the table exists:

```sql
SHOW TABLES LIKE 'trip_requests';
DESCRIBE trip_requests;
```

You should see the table structure with all columns listed above.
