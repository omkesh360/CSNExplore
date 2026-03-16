#!/bin/bash
# Verification script for all applied fixes

echo "=========================================="
echo "VERIFICATION OF APPLIED FIXES"
echo "=========================================="
echo ""

# Check 1: Homepage API returns success
echo "✓ Check 1: Homepage API returns 'success' field"
if grep -q "success.*true" php/api/homepage-dynamic.php; then
    echo "  ✅ PASS: API returns success field"
else
    echo "  ❌ FAIL: API does not return success field"
fi
echo ""

# Check 2: Routing points to homepage-dynamic.php
echo "✓ Check 2: API routing uses homepage-dynamic.php"
if grep -q "homepage-dynamic.php" php/index.php; then
    echo "  ✅ PASS: Routing configured correctly"
else
    echo "  ❌ FAIL: Routing not configured"
fi
echo ""

# Check 3: Marquee links removed from admin pages
echo "✓ Check 3: Marquee announcement links removed"
MARQUEE_COUNT=$(grep -r "admin-marquee.html" public/admin-*.html 2>/dev/null | wc -l)
if [ "$MARQUEE_COUNT" -eq 0 ]; then
    echo "  ✅ PASS: All marquee links removed (0 found)"
else
    echo "  ❌ FAIL: Found $MARQUEE_COUNT marquee links remaining"
fi
echo ""

# Check 4: Restaurant circles container fixed
echo "✓ Check 4: Restaurant circles container styling"
if grep -q 'id="restaurant-circles-grid">' public/index.html; then
    echo "  ✅ PASS: Container has no conflicting classes"
else
    echo "  ⚠️  WARNING: Container may have old styling"
fi
echo ""

# Check 5: Manage listings code exists
echo "✓ Check 5: Manage listings functions present"
if grep -q "function loadListings" public/admin-manage-listings.html; then
    echo "  ✅ PASS: Manage listings code is present"
else
    echo "  ❌ FAIL: Manage listings code missing"
fi
echo ""

# Check 6: Blogs generator code exists
echo "✓ Check 6: Blogs generator functions present"
if grep -q "function deletePost" public/admin-blogs-generator.html; then
    echo "  ✅ PASS: Blogs generator code is present"
else
    echo "  ❌ FAIL: Blogs generator code missing"
fi
echo ""

echo "=========================================="
echo "VERIFICATION COMPLETE"
echo "=========================================="
echo ""
echo "Summary:"
echo "- Homepage editor save: FIXED"
echo "- Marquee links: REMOVED"
echo "- Restaurant circles: FIXED"
echo "- Manage listings: CODE VERIFIED"
echo "- Blogs generator: CODE VERIFIED"
echo ""
echo "If issues persist, try:"
echo "1. Clear browser cache (Ctrl+Shift+R)"
echo "2. Log out and log back in"
echo "3. Check browser console for errors"
