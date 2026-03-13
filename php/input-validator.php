<?php
/**
 * Input Validation and Sanitization Utility
 * Provides server-side validation for all user inputs
 */

class InputValidator {
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input, $maxLength = null) {
        $sanitized = htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        
        if ($maxLength !== null && strlen($sanitized) > $maxLength) {
            $sanitized = substr($sanitized, 0, $maxLength);
        }
        
        return $sanitized;
    }

    /**
     * Validate and sanitize email
     */
    public static function validateEmail($email) {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return $email;
    }

    /**
     * Validate phone number
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9+\-\s()]/', '', $phone);
        
        if (strlen($phone) < 10 || strlen($phone) > 20) {
            return false;
        }
        
        return $phone;
    }

    /**
     * Validate URL
     */
    public static function validateUrl($url) {
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        return $url;
    }

    /**
     * Validate integer
     */
    public static function validateInt($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $value = intval($value);
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return $value;
    }

    /**
     * Validate float/decimal
     */
    public static function validateFloat($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $value = floatval($value);
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return $value;
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return empty($errors) ? true : $errors;
    }

    /**
     * Validate date format
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Sanitize filename
     */
    public static function sanitizeFilename($filename) {
        // Remove any path components
        $filename = basename($filename);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        return $filename;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = 'Invalid file upload';
            return $errors;
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File size exceeds limit';
                return $errors;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file uploaded';
                return $errors;
            default:
                $errors[] = 'Unknown upload error';
                return $errors;
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds ' . ($maxSize / 1024 / 1024) . 'MB';
        }
        
        if (!empty($allowedTypes)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = 'Invalid file type';
            }
        }
        
        return empty($errors) ? true : $errors;
    }

    /**
     * Sanitize JSON input
     */
    public static function sanitizeJson($json) {
        if (is_string($json)) {
            $decoded = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
            return $decoded;
        }
        
        return $json;
    }

    /**
     * Validate array of values
     */
    public static function validateArray($array, $allowedKeys = []) {
        if (!is_array($array)) {
            return false;
        }
        
        if (!empty($allowedKeys)) {
            foreach (array_keys($array) as $key) {
                if (!in_array($key, $allowedKeys)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Prevent XSS attacks
     */
    public static function preventXSS($input) {
        if (is_array($input)) {
            return array_map([self::class, 'preventXSS'], $input);
        }
        
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate SQL input (additional layer)
     */
    public static function validateSqlInput($input) {
        // Check for common SQL injection patterns
        $patterns = [
            '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bUPDATE\b|\bDELETE\b|\bDROP\b)/i',
            '/--/',
            '/\/\*/',
            '/;/',
            '/\bOR\b.*=.*\bOR\b/i',
            '/\bAND\b.*=.*\bAND\b/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return false;
            }
        }
        
        return true;
    }
}
