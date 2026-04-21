# Trip Planner - Complete Fix Guide

## ✅ What Has Been Fixed

### 1. Form Updates
- ✅ Added **Email Address** field to Step 3
- ✅ Updated validation to require email
- ✅ Email format validation added

### 2. Database Updates
- ✅ Updated SQL file to include `email` column
- ✅ Added email index for performance
- ✅ File: `database/add_trip_requests_table.sql`

### 3. Email Notifications (PHPMailer)
- ✅ Added `sendTripRequestEmails()` method to `EmailService.php`
- ✅ Created user confirmation email template
- ✅ Created admin notification email template
- ✅ Integrated email sending into form submission
- ✅ Emails send AFTER database save (non-blocking)

### 4. Admin Panel Updates
- ✅ Added email column to trip requests table
- ✅ Email shown in details modal
- ✅ Clickable email links (mailto:)

## 🚀 Setup Instructions

### Step 1: Import the Updated SQL File

**IMPORTANT:** This will create the table with the email column.

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin
2. Select your `csnexplore` database
3. Click "Import" tab
4. Choose file: `database/add_trip_requests_table.sql`
5. Click "Go"

**Option B: Using MySQL Command Line**
```bash
mysql -u your_username -p csnexplore < database/add_trip_requests_table.sql
```

**Option C: If Table Already Exists (Add Email Column)**
If you already created the table without the email column, run this:
```sql
ALTER TABLE trip_requests 
ADD COLUMN email VARCHAR(255) NOT NULL AFTER full_name,
ADD INDEX idx_email (email);
```

### Step 2: Configure Email Settings in .env

Make sure your `.env` file has these settings:

```env
# SMTP Configuration (Required for emails)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_ENCRYPTION=tls

# Admin notification email
ADMIN_NOTIFICATION_EMAIL=supportcsnexplore@gmail.com

# Optional: MailerLite settings (for branding)
MAILERLITE_FROM_EMAIL=noreply@csnexplore.com
MAILERLITE_FROM_NAME=CSN Explore
```

### Step 3: Gmail App Password Setup (If Using Gmail)

If you're using Gmail for SMTP, you need an **App Password**:

1. Go to your Google Account: https://myaccount.google.com/
2. Click "Security" in the left menu
3. Enable "2-Step Verification" (if not already enabled)
4. Go back to Security → "App passwords"
5. Select "Mail" and "Other (Custom name)"
6. Enter "CSN Explore" as the name
7. Click "Generate"
8. Copy the 16-character password
9. Use this password in your `.env` file as `SMTP_PASSWORD`

**Example:**
```env
SMTP_USERNAME=youremail@gmail.com
SMTP_PASSWORD=abcd efgh ijkl mnop
```

### Step 4: Test the Form

1. Visit: `http://your-domain/suggestor`
2. Fill out all 3 steps:
   - Step 1: Select interests
   - Step 2: Choose stay type and travel mode
   - Step 3: Enter name, **email**, and phone
3. Submit the form
4. You should see: "Request Received!" with confetti

### Step 5: Check Emails

After submitting the form, you should receive:

**User Email (to customer):**
- Subject: "Trip Request Received - CSN Explore"
- Contains trip preferences
- Timeline of what happens next

**Admin Email (to you):**
- Subject: "New Trip Request #X - Customer Name"
- Contains all customer details
- Quick action buttons (Call, WhatsApp)

### Step 6: View in Admin Panel

Access: `http://your-domain/admin/trip-requests.php?admin=true`

You'll see:
- All trip requests in a table
- Email column with clickable links
- Status management
- Details modal with full information

## 🔍 Troubleshooting

### Issue: "Something went wrong. Please try again."

**Cause:** Database table doesn't exist or email column is missing.

**Solution:**
1. Check if table exists:
   ```sql
   SHOW TABLES LIKE 'trip_requests';
   ```
2. Check table structure:
   ```sql
   DESCRIBE trip_requests;
   ```
3. Make sure `email` column exists
4. If not, run the ALTER TABLE command from Step 1

### Issue: Form submits but no emails received

**Possible Causes:**

1. **SMTP credentials not configured**
   - Check your `.env` file
   - Make sure `SMTP_USERNAME` and `SMTP_PASSWORD` are set

2. **Gmail blocking the connection**
   - Use App Password (see Step 3)
   - Enable "Less secure app access" (not recommended)

3. **Email in spam folder**
   - Check spam/junk folder
   - Add sender to contacts

4. **Check error logs**
   ```bash
   tail -f logs/email_errors.log
   tail -f logs/php_errors.log
   ```

### Issue: Emails send but look broken

**Solution:** Email templates use inline CSS and are tested. If they look broken:
- Check if email client supports HTML emails
- Try viewing in a different email client
- Check `php/templates/emails/trip-request-user.php` for any syntax errors

## 📧 Email Flow

```
User Submits Form
       ↓
Data Saved to Database ✅
       ↓
Get Trip Request ID
       ↓
Send User Confirmation Email 📧
       ↓
Send Admin Notification Email 📧
       ↓
Redirect to Success Page 🎉
```

**Note:** If emails fail, the form still works! The request is saved to the database, and you can view it in the admin panel.

## ✅ Verification Checklist

After setup, verify:

- [ ] Table exists with email column
- [ ] Form shows email field in Step 3
- [ ] Form validates email format
- [ ] Form submits successfully
- [ ] Success page shows with confetti
- [ ] User receives confirmation email
- [ ] Admin receives notification email
- [ ] Data appears in admin panel
- [ ] Email column shows in admin table
- [ ] Status updates work

## 📝 Test Email Addresses

For testing, you can use:
- Your personal email
- A test email service like Mailtrap.io
- Gmail with App Password

## 🎯 What Happens When User Submits

1. **Form Validation**
   - Checks name, email, phone are filled
   - Validates email format

2. **Database Save**
   - Saves all trip preferences
   - Gets the new trip request ID

3. **Email Notifications**
   - Sends confirmation to user
   - Sends notification to admin
   - Errors are logged but don't block the form

4. **Success Page**
   - Shows confetti animation
   - Displays confirmation message
   - Tells user to expect contact within 2 hours

## 🔐 Security Notes

- All user input is sanitized using `htmlspecialchars()`
- Email validation using `filter_var()`
- SQL injection protection via prepared statements
- SMTP credentials stored in `.env` (not in code)
- Email templates escape all variables

## 📞 Support

If you encounter any issues:
1. Check error logs: `logs/email_errors.log` and `logs/php_errors.log`
2. Verify SMTP credentials in `.env`
3. Test SMTP connection manually
4. Check if table and email column exist

---

## Summary

✅ **Form:** Email field added and validated  
✅ **Database:** Email column added to table  
✅ **Emails:** PHPMailer integration complete  
✅ **Templates:** Beautiful HTML emails created  
✅ **Admin:** Email column added to admin panel  
✅ **Error Handling:** Graceful fallback if emails fail  

**Next Step:** Import the SQL file and configure your SMTP settings in `.env`!
