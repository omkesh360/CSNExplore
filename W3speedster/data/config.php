<?php
$storage_types = ['file', 'database'];


if (!defined('W3SPEEDSTER_CONFIG')) {
  define('W3SPEEDSTER_CONFIG', [
    // Plugin version
    'version' => '9.7.2',

    // Storage type to use ('file' or 'database')
    'storage_type' => $storage_types[0],

    // Enable or disable debug show
    'debug_show' => false,

    // Enable or disable debug log
    'debug_log' => false,

    'default_error_log_path' => __DIR__ . '/../w3speedster.log',

    'users' => [
      'admin@example.com' => [
        'name' => 'Administrator',                // User's display name
        'email' => 'admin@example.com',           // User's email address (used as key)
        'password' => '4de93544234adffbb681ed60ffcfb941',                 // User's password (should be hashed in production)
        'role' => 'administrator',                // User's role
        'created_at' => date('Y-m-d H:i:s'),      // Account creation timestamp
        'last_login' => null,                     // Last login timestamp
        'login_attempts' => 0,                    // Number of failed login attempts
        'locked_until' => null,                   // Account lockout expiration timestamp
        'is_active' => true,                      // Whether the account is active
      ],
    ],

    'smtp' => [
      'host' => '',                              // SMTP server host
      'port' => 587,                             // SMTP server port
      'username' => '',                          // SMTP username
      'password' => '',                          // SMTP password
      'from_email' => '',                        // Default sender email address
      'from_name' => 'W3speedster',              // Default sender name
      'encryption' => 'tls',                     // Encryption type ('tls' or 'ssl')
      'status' => false,                         // Whether SMTP is enabled
      'test_email' => '',                        // Email address for sending test emails
      'last_test' => null,                       // Timestamp of last SMTP test
    ],

    'database' => [
      'connection' => 'mysql',
      'host' => 'localhost',
      'port' => 3306,
      'database' => '',
      'username' => '',
      'password' => '',
    ],
  ]);
}
