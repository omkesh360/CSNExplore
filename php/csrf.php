<?php
/**
 * CSRF Protection Utility
 * Generates and validates CSRF tokens for form submissions
 */

class CSRF {
    private static $tokenName = 'csrf_token';
    private static $tokenExpiry = 3600; // 1 hour

    /**
     * Generate a new CSRF token
     */
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$tokenName] = $token;
        $_SESSION[self::$tokenName . '_time'] = time();

        return $token;
    }

    /**
     * Get the current CSRF token
     */
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::$tokenName])) {
            return self::generateToken();
        }

        // Check if token has expired
        if (isset($_SESSION[self::$tokenName . '_time'])) {
            $tokenAge = time() - $_SESSION[self::$tokenName . '_time'];
            if ($tokenAge > self::$tokenExpiry) {
                return self::generateToken();
            }
        }

        return $_SESSION[self::$tokenName];
    }

    /**
     * Validate CSRF token
     */
    public static function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::$tokenName])) {
            return false;
        }

        // Check if token has expired
        if (isset($_SESSION[self::$tokenName . '_time'])) {
            $tokenAge = time() - $_SESSION[self::$tokenName . '_time'];
            if ($tokenAge > self::$tokenExpiry) {
                return false;
            }
        }

        return hash_equals($_SESSION[self::$tokenName], $token);
    }

    /**
     * Validate token from request
     */
    public static function validateRequest() {
        $token = null;

        // Check POST data
        if (isset($_POST[self::$tokenName])) {
            $token = $_POST[self::$tokenName];
        }
        // Check headers
        elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        if (!$token) {
            return false;
        }

        return self::validateToken($token);
    }

    /**
     * Get token as hidden input field
     */
    public static function getTokenField() {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Get token as meta tag for AJAX requests
     */
    public static function getTokenMeta() {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
}
