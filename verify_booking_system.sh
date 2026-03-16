#!/bin/bash
echo "=========================================="
echo "BOOKING SYSTEM VERIFICATION"
echo "=========================================="
echo ""

echo "1. Checking for remaining WhatsApp links..."
WA_COUNT=$(grep -r "wa.me" public/*.html 2>/dev/null | wc -l)
if [ "$WA_COUNT" -eq 0 ]; then
    echo "   ✅ PASS: No WhatsApp links found"
else
    echo "   ❌ FAIL: Found $WA_COUNT WhatsApp links"
fi
echo ""

echo "2. Checking for Book Now buttons..."
BOOK_COUNT=$(grep -r "data-book-now" public/*.html 2>/dev/null | wc -l)
if [ "$BOOK_COUNT" -gt 0 ]; then
    echo "   ✅ PASS: Found $BOOK_COUNT Book Now buttons"
else
    echo "   ❌ FAIL: No Book Now buttons found"
fi
echo ""

echo "3. Checking for booking popup script..."
SCRIPT_COUNT=$(grep -r "booking-popup.js" public/*.html 2>/dev/null | wc -l)
if [ "$SCRIPT_COUNT" -gt 0 ]; then
    echo "   ✅ PASS: Booking script included in $SCRIPT_COUNT files"
else
    echo "   ❌ FAIL: Booking script not found"
fi
echo ""

echo "4. Checking if booking API exists..."
if [ -f "php/api/bookings.php" ]; then
    echo "   ✅ PASS: Booking API file exists"
else
    echo "   ❌ FAIL: Booking API file missing"
fi
echo ""

echo "5. Checking if admin booking page exists..."
if [ -f "public/admin-booking-calls.html" ]; then
    echo "   ✅ PASS: Admin booking page exists"
else
    echo "   ❌ FAIL: Admin booking page missing"
fi
echo ""

echo "6. Checking for Booking Calls link in admin pages..."
ADMIN_LINK_COUNT=$(grep -r "admin-booking-calls.html" public/admin-*.html 2>/dev/null | wc -l)
if [ "$ADMIN_LINK_COUNT" -gt 0 ]; then
    echo "   ✅ PASS: Booking Calls link found in $ADMIN_LINK_COUNT admin pages"
else
    echo "   ❌ FAIL: Booking Calls link not found in admin pages"
fi
echo ""

echo "=========================================="
echo "VERIFICATION COMPLETE"
echo "=========================================="
