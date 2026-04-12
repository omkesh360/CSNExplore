<?php
/**
 * Preservation Property Tests for Performance Page Functions Fix
 * 
 * This test verifies that the fix for performance page functions does not break
 * existing functionality on other admin pages and footer features.
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**
 * 
 * Expected outcome: Tests PASS (confirms no regressions)
 */

class PreservationPropertyTest {
    private $errors = [];
    private $passed_tests = 0;
    private $failed_tests = 0;
    
    /**
     * Test 1: Verify api() function is available on dashboard page
     */
    public function test_api_available_on_dashboard() {
        echo "Test 1: api() available on dashboard page\n";
        
        // Simulate loading dashboard page
        ob_start();
        $admin_page = 'dashboard';
        $admin_title = 'Dashboard | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in dashboard page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 2: Verify showAdminToast() function is available on dashboard page
     */
    public function test_showAdminToast_available_on_dashboard() {
        echo "Test 2: showAdminToast() available on dashboard page\n";
        
        // Simulate loading dashboard page
        ob_start();
        $admin_page = 'dashboard';
        $admin_title = 'Dashboard | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if showAdminToast() is defined
        if (strpos($header_output, 'function showAdminToast(') === false) {
            $this->errors[] = "FAIL: showAdminToast() function not found in dashboard page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 3: Verify api() function is available on blogs page
     */
    public function test_api_available_on_blogs() {
        echo "Test 3: api() available on blogs page\n";
        
        // Simulate loading blogs page
        ob_start();
        $admin_page = 'blogs';
        $admin_title = 'Blogs | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in blogs page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 4: Verify api() function is available on bookings page
     */
    public function test_api_available_on_bookings() {
        echo "Test 4: api() available on bookings page\n";
        
        // Simulate loading bookings page
        ob_start();
        $admin_page = 'bookings';
        $admin_title = 'Bookings | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in bookings page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 5: Verify api() function is available on users page
     */
    public function test_api_available_on_users() {
        echo "Test 5: api() available on users page\n";
        
        // Simulate loading users page
        ob_start();
        $admin_page = 'users';
        $admin_title = 'Users | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in users page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 6: Verify api() function is available on gallery page
     */
    public function test_api_available_on_gallery() {
        echo "Test 6: api() available on gallery page\n";
        
        // Simulate loading gallery page
        ob_start();
        $admin_page = 'gallery';
        $admin_title = 'Gallery | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in gallery page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 7: Verify api() function is available on listings page
     */
    public function test_api_available_on_listings() {
        echo "Test 7: api() available on listings page\n";
        
        // Simulate loading listings page
        ob_start();
        $admin_page = 'listings';
        $admin_title = 'Listings | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in listings page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 8: Verify api() function is available on content page
     */
    public function test_api_available_on_content() {
        echo "Test 8: api() available on content page\n";
        
        // Simulate loading content page
        ob_start();
        $admin_page = 'content';
        $admin_title = 'Page Content | CSNExplore';
        require 'admin-header.php';
        $header_output = ob_get_clean();
        
        // Check if api() is defined
        if (strpos($header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not found in content page header";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 9: Verify gallery picker modal is present in footer
     */
    public function test_gallery_picker_modal_in_footer() {
        echo "Test 9: Gallery picker modal present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if gallery picker modal is present
        if (strpos($footer_output, 'gallery-picker-modal') === false) {
            $this->errors[] = "FAIL: gallery-picker-modal not found in footer";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 10: Verify gallery picker functions are in footer
     */
    public function test_gallery_picker_functions_in_footer() {
        echo "Test 10: Gallery picker functions present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if gallery picker functions are present
        $required_functions = [
            'openGalleryPicker',
            'closeGalleryPicker',
            'loadPickerImages',
            'renderPickerGrid',
            'selectPickerImage'
        ];
        
        foreach ($required_functions as $func) {
            if (strpos($footer_output, 'function ' . $func) === false && 
                strpos($footer_output, $func . ' = function') === false) {
                $this->errors[] = "FAIL: $func function not found in footer";
                $this->failed_tests++;
                echo "  ✗ FAIL\n";
                return false;
            }
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 11: Verify pending count loading function is in footer
     */
    public function test_pending_count_function_in_footer() {
        echo "Test 11: Pending count loading function present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if loadPendingCount function is present
        if (strpos($footer_output, 'loadPendingCount') === false) {
            $this->errors[] = "FAIL: loadPendingCount function not found in footer";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 12: Verify logout function is in footer
     */
    public function test_logout_function_in_footer() {
        echo "Test 12: Logout function present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if adminLogout function is present
        if (strpos($footer_output, 'function adminLogout') === false) {
            $this->errors[] = "FAIL: adminLogout function not found in footer";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 13: Verify api() and showAdminToast() are NOT duplicated in footer
     */
    public function test_no_duplicate_functions_in_footer() {
        echo "Test 13: No duplicate api() or showAdminToast() in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check that api() and showAdminToast() are NOT defined in footer
        // (they should only be in header)
        if (strpos($footer_output, 'async function api(') !== false || 
            strpos($footer_output, 'function api(') !== false) {
            $this->errors[] = "FAIL: api() function found in footer (should only be in header)";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        if (strpos($footer_output, 'function showAdminToast(') !== false) {
            $this->errors[] = "FAIL: showAdminToast() function found in footer (should only be in header)";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 14: Verify sidebar toggle functionality is in footer
     */
    public function test_sidebar_toggle_in_footer() {
        echo "Test 14: Sidebar toggle functionality present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if sidebar toggle code is present
        if (strpos($footer_output, 'sidebar-toggle') === false) {
            $this->errors[] = "FAIL: sidebar-toggle functionality not found in footer";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Test 15: Verify user info population is in footer
     */
    public function test_user_info_population_in_footer() {
        echo "Test 15: User info population present in footer\n";
        
        // Simulate loading footer
        ob_start();
        require 'admin-footer.php';
        $footer_output = ob_get_clean();
        
        // Check if user info population code is present
        if (strpos($footer_output, 'admin-name') === false || strpos($footer_output, 'admin-email') === false) {
            $this->errors[] = "FAIL: user info population not found in footer";
            $this->failed_tests++;
            echo "  ✗ FAIL\n";
            return false;
        }
        
        $this->passed_tests++;
        echo "  ✓ PASS\n";
        return true;
    }
    
    /**
     * Run all preservation tests
     */
    public function run_all_tests() {
        echo "=== Preservation Property Tests ===\n";
        echo "Verifying that other admin pages and footer functionality remain unchanged\n\n";
        
        $this->test_api_available_on_dashboard();
        $this->test_showAdminToast_available_on_dashboard();
        $this->test_api_available_on_blogs();
        $this->test_api_available_on_bookings();
        $this->test_api_available_on_users();
        $this->test_api_available_on_gallery();
        $this->test_api_available_on_listings();
        $this->test_api_available_on_content();
        $this->test_gallery_picker_modal_in_footer();
        $this->test_gallery_picker_functions_in_footer();
        $this->test_pending_count_function_in_footer();
        $this->test_logout_function_in_footer();
        $this->test_no_duplicate_functions_in_footer();
        $this->test_sidebar_toggle_in_footer();
        $this->test_user_info_population_in_footer();
        
        echo "\n=== Test Results ===\n";
        echo "Passed: " . $this->passed_tests . "\n";
        echo "Failed: " . $this->failed_tests . "\n";
        
        if (!empty($this->errors)) {
            echo "\n=== Errors Found ===\n";
            foreach ($this->errors as $error) {
                echo "- $error\n";
            }
        }
        
        return $this->failed_tests === 0;
    }
}

// Run the tests
$test = new PreservationPropertyTest();
$all_passed = $test->run_all_tests();

// Exit with appropriate code
exit($all_passed ? 0 : 1);
?>
