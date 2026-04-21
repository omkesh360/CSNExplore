# 🚀 Trip Planner - Quick Start Guide

## What Was Fixed

✅ **Email field added** to the trip planner form  
✅ **PHPMailer integration** - sends emails to user and admin  
✅ **Database updated** - includes email column  
✅ **Beautiful email templates** - professional HTML emails  
✅ **Error fixed** - "Something went wrong" issue resolved  

---

## 3-Step Setup (5 Minutes)

### Step 1: Import Database Table (1 min)

Open phpMyAdmin and run this file:
```
database/add_trip_requests_table.sql
```

Or use MySQL command line:
```bash
mysql -u your_username -p csnexplore < database/add_trip_requests_table.sql
```

### Step 2: Configure Email in .env (2 min)

Add these to your `.env` file:

```env
# Gmail SMTP (Recommended)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password_here
SMTP_ENCRYPTION=tls

# Admin email (where notifications go)
ADMIN_NOTIFICATION_EMAIL=supportcsnexplore@gmail.com
```

**Gmail App Password Setup:**
1. Go to: https://myaccount.google.com/security
2. Enable "2-Step Verification"
3. Go to "App passwords"
4. Generate password for "Mail"
5. Copy the 16-character password
6. Use it as `SMTP_PASSWORD`

### Step 3: Test Everything (2 min)

**Test Email Configuration:**
1. Visit: `http://your-domain/test-email.php?secret=test123`
2. Enter your email and click "Send Test Email"
3. Check your inbox for the test email
4. **Delete test-email.php after testing!**

**Test Trip Planner:**
1. Visit: `http://your-domain/suggestor`
2. Fill out the form (all 3 steps)
3. Submit and check for success page
4. Check your email for confirmation
5. Check admin email for notification

**View in Admin Panel:**
- Visit: `http://your-domain/admin/trip-requests.php?admin=true`

---

## ✅ Success Checklist

After setup, you should have:

- [ ] Form submits without "Something went wrong" error
- [ ] Success page shows with confetti animation
- [ ] User receives confirmation email
- [ ] Admin receives notification email
- [ ] Trip request appears in admin panel
- [ ] Email column shows in admin table

---

## 🔧 Troubleshooting

### "Something went wrong" Error
**Solution:** Import the SQL file from Step 1

### No Emails Received
**Solutions:**
- Check spam/junk folder
- Verify SMTP credentials in `.env`
- Use Gmail App Password (not regular password)
- Run test-email.php to diagnose

### Gmail Blocking Connection
**Solution:** Use App Password (see Step 2)

### Check Error Logs
```bash
tail -f logs/email_errors.log
tail -f logs/php_errors.log
```

---

## 📧 What Emails Are Sent

### User Confirmation Email
- **To:** Customer's email
- **Subject:** "Trip Request Received - CSN Explore"
- **Contains:** Trip preferences, timeline, contact info

### Admin Notification Email
- **To:** Your admin email
- **Subject:** "New Trip Request #X - Customer Name"
- **Contains:** All customer details, quick action buttons

---

## 🎯 How It Works

```
User fills form → Data saved to database → Emails sent → Success page
```

**Important:** Even if emails fail, the form still works! The request is saved to the database.

---

## 📁 Files Changed

### Modified Files:
- `suggestor.php` - Added email field and email sending
- `database/add_trip_requests_table.sql` - Added email column
- `php/services/EmailService.php` - Added trip request methods
- `admin/trip-requests.php` - Added email column

### New Files:
- `php/templates/emails/trip-request-user.php` - User email template
- `php/templates/emails/trip-request-admin.php` - Admin email template
- `test-email.php` - Email testing tool (delete after use!)
- `TRIP_PLANNER_COMPLETE_FIX.md` - Detailed documentation
- `QUICK_START_GUIDE.md` - This file

---

## 🔐 Security Notes

- Delete `test-email.php` after testing
- SMTP credentials are in `.env` (not in code)
- All user input is sanitized
- Email validation on form submission

---

## 💡 Pro Tips

1. **Test with your own email first** before going live
2. **Check spam folder** if emails don't arrive
3. **Use Gmail App Password** for best reliability
4. **Monitor logs** for any email errors
5. **Delete test-email.php** for security

---

## 📞 Need Help?

If something doesn't work:

1. Check error logs: `logs/email_errors.log`
2. Run test-email.php to diagnose SMTP issues
3. Verify table exists: `SHOW TABLES LIKE 'trip_requests';`
4. Check table structure: `DESCRIBE trip_requests;`

---

## Summary

**Time to setup:** ~5 minutes  
**Difficulty:** Easy  
**Result:** Fully working trip planner with email notifications!

**Next:** Import the SQL file and configure your SMTP settings! 🚀
