<?php

/**
 * Email Service
 * 
 * Orchestrates email sending for booking confirmations and admin notifications
 * Uses PHPMailer with SMTP for reliable email delivery
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    /**
     * Send both user confirmation and admin notification emails
     * 
     * @param int $bookingId The booking record ID
     * @return array ['user_sent' => bool, 'admin_sent' => bool, 'errors' => array]
     */
    public static function sendBookingEmails(int $bookingId): array {
        $result = [
            'user_sent' => false,
            'admin_sent' => false,
            'errors' => []
        ];
        
        try {
            // Fetch complete booking record from database
            $db = getDB();
            $booking = $db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
            
            if (!$booking) {
                $error = "Booking not found: ID $bookingId";
                self::logError($error, ['booking_id' => $bookingId]);
                $result['errors'][] = $error;
                return $result;
            }
            
            // Send user confirmation email
            $result['user_sent'] = self::sendUserConfirmation($booking);
            
            // Send admin notification email
            $result['admin_sent'] = self::sendAdminNotification($booking);
            
        } catch (Exception $e) {
            $error = "Failed to send booking emails: " . $e->getMessage();
            self::logError($error, [
                'booking_id' => $bookingId,
                'exception' => get_class($e)
            ]);
            $result['errors'][] = $error;
        }
        
        return $result;
    }
    
    /**
     * Send status update email to user
     * 
     * @param int $bookingId The booking record ID
     * @param string $status New status (completed or cancelled)
     * @return bool Success status
     */
    public static function sendStatusUpdateEmail(int $bookingId, string $status): bool {
        try {
            // Fetch complete booking record from database
            $db = getDB();
            $booking = $db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
            
            if (!$booking) {
                self::logError("Booking not found for status update", ['booking_id' => $bookingId]);
                return false;
            }
            
            // Handle missing user email gracefully
            if (empty($booking['email'])) {
                self::logError("No email provided for status update", [
                    'booking_id' => $bookingId,
                    'status' => $status
                ]);
                return false;
            }
            
            // Validate email address
            if (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
                self::logError("Invalid email address for status update", [
                    'booking_id' => $bookingId,
                    'email' => $booking['email'],
                    'status' => $status
                ]);
                return false;
            }
            
            // Determine template and subject based on status
            if ($status === 'completed') {
                $template = 'booking-confirmed';
                $subject = 'Booking Confirmed - CSN Explore';
            } elseif ($status === 'cancelled') {
                $template = 'booking-cancelled';
                $subject = 'Booking Cancelled - CSN Explore';
            } else {
                self::logError("Invalid status for email", [
                    'booking_id' => $bookingId,
                    'status' => $status
                ]);
                return false;
            }
            
            // Render email template
            $htmlContent = self::renderTemplate($template, $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render status update template", [
                    'booking_id' => $bookingId,
                    'template' => $template
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress($booking['email'], $booking['full_name']);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send status update email", [
                    'booking_id' => $bookingId,
                    'email' => $booking['email'],
                    'status' => $status,
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendStatusUpdateEmail", [
                'booking_id' => $bookingId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send user confirmation email
     * 
     * @param array $booking Booking record data
     * @return bool Success status
     */
    private static function sendUserConfirmation(array $booking): bool {
        try {
            // Handle missing user email gracefully
            if (empty($booking['email'])) {
                self::logError("No email provided for booking", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Validate email address
            if (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
                self::logError("Invalid email address", [
                    'booking_id' => $booking['id'],
                    'email' => $booking['email'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Render email template
            $htmlContent = self::renderTemplate('user-confirmation', $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render user confirmation template", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress($booking['email'], $booking['full_name']);
            $mail->Subject = 'Booking Confirmation - CSN Explore';
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send user confirmation email", [
                    'booking_id' => $booking['id'],
                    'email' => $booking['email'],
                    'email_type' => 'user_confirmation',
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendUserConfirmation", [
                'booking_id' => $booking['id'],
                'email_type' => 'user_confirmation',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send trip request emails (user confirmation + admin notification)
     * 
     * @param int $tripRequestId The trip request record ID
     * @return array ['user_sent' => bool, 'admin_sent' => bool, 'errors' => array]
     */
    public static function sendTripRequestEmails(int $tripRequestId): array {
        $result = [
            'user_sent' => false,
            'admin_sent' => false,
            'errors' => []
        ];
        
        try {
            // Fetch complete trip request record from database
            $db = getDB();
            $tripRequest = $db->fetchOne("SELECT * FROM trip_requests WHERE id = ?", [$tripRequestId]);
            
            if (!$tripRequest) {
                $error = "Trip request not found: ID $tripRequestId";
                self::logError($error, ['trip_request_id' => $tripRequestId]);
                $result['errors'][] = $error;
                return $result;
            }
            
            // Send user confirmation email
            $result['user_sent'] = self::sendTripRequestUserConfirmation($tripRequest);
            
            // Send admin notification email
            $result['admin_sent'] = self::sendTripRequestAdminNotification($tripRequest);
            
        } catch (Exception $e) {
            $error = "Failed to send trip request emails: " . $e->getMessage();
            self::logError($error, [
                'trip_request_id' => $tripRequestId,
                'exception' => get_class($e)
            ]);
            $result['errors'][] = $error;
        }
        
        return $result;
    }
    
    /**
     * Send user confirmation email for trip request
     * 
     * @param array $tripRequest Trip request record data
     * @return bool Success status
     */
    private static function sendTripRequestUserConfirmation(array $tripRequest): bool {
        try {
            // Validate email address
            if (empty($tripRequest['email']) || !filter_var($tripRequest['email'], FILTER_VALIDATE_EMAIL)) {
                self::logError("Invalid email for trip request", [
                    'trip_request_id' => $tripRequest['id'],
                    'email' => $tripRequest['email'] ?? 'empty'
                ]);
                return false;
            }
            
            // Render email template
            $htmlContent = self::renderTemplate('trip-request-user', $tripRequest);
            
            if ($htmlContent === false) {
                self::logError("Failed to render trip request user template", [
                    'trip_request_id' => $tripRequest['id']
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress($tripRequest['email'], $tripRequest['full_name']);
            $mail->Subject = 'Trip Request Received - CSN Explore';
            $mail->Body = $htmlContent;
            $mail->AltBody = "Hi {$tripRequest['full_name']},\n\nWe received your trip request! Our local experts will contact you within 2 hours.\n\nThank you,\nCSN Explore Team";
            
            if (!$mail->send()) {
                self::logError("Failed to send trip request user email", [
                    'trip_request_id' => $tripRequest['id'],
                    'email' => $tripRequest['email'],
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendTripRequestUserConfirmation", [
                'trip_request_id' => $tripRequest['id'],
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send admin notification email for trip request
     * 
     * @param array $tripRequest Trip request record data
     * @return bool Success status
     */
    private static function sendTripRequestAdminNotification(array $tripRequest): bool {
        try {
            // Render email template
            $htmlContent = self::renderTemplate('trip-request-admin', $tripRequest);
            
            if ($htmlContent === false) {
                self::logError("Failed to render trip request admin template", [
                    'trip_request_id' => $tripRequest['id']
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress(ADMIN_NOTIFICATION_EMAIL, 'CSN Explore Admin');
            $mail->Subject = "New Trip Request #{$tripRequest['id']} - {$tripRequest['full_name']}";
            $mail->Body = $htmlContent;
            $mail->AltBody = "New trip request from {$tripRequest['full_name']}\nPhone: {$tripRequest['phone']}\nEmail: {$tripRequest['email']}";
            
            if (!$mail->send()) {
                self::logError("Failed to send trip request admin email", [
                    'trip_request_id' => $tripRequest['id'],
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendTripRequestAdminNotification", [
                'trip_request_id' => $tripRequest['id'],
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send email verification link to newly registered user
     */
    public static function sendVerificationEmail(string $email, string $name, string $verifyLink): bool {
        try {
            // Pass data as $booking so template can access $booking['name'] and $booking['verifyLink']
            $data = ['name' => $name, 'verifyLink' => $verifyLink];
            $htmlContent = self::renderTemplate('verify-email', $data);
            if ($htmlContent === false) {
                // Fallback: send plain-text if template fails
                $htmlContent = '<p>Hi ' . htmlspecialchars($name) . ',</p>'
                    . '<p>Please verify your email by clicking: <a href="' . htmlspecialchars($verifyLink) . '">' . htmlspecialchars($verifyLink) . '</a></p>'
                    . '<p>This link expires in 24 hours.</p>';
                self::logError("verify-email template failed, using fallback HTML", ['email' => $email]);
            }
            $mail = self::createMailer();
            $mail->addAddress($email, $name);
            $mail->Subject = 'Verify Your Email — CSN Explore';
            $mail->Body    = $htmlContent;
            $mail->AltBody = "Hi $name,\n\nVerify your email by visiting:\n$verifyLink\n\nThis link expires in 24 hours.\n\nCSN Explore Team";
            if (!$mail->send()) {
                self::logError("PHPMailer send() returned false for verification email", ['email' => $email, 'error' => $mail->ErrorInfo]);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            self::logError("Exception in sendVerificationEmail: " . $e->getMessage(), ['email' => $email]);
            return false;
        }
    }

    /**
     * Send password reset email to user
     */
    public static function sendPasswordResetEmail(string $email, string $name, string $resetLink): bool {
        try {
            $data = ['name' => $name, 'resetLink' => $resetLink];
            $htmlContent = self::renderTemplate('password-reset', $data);
            if ($htmlContent === false) {
                // Fallback plain HTML
                $htmlContent = '<p>Hi ' . htmlspecialchars($name) . ',</p>'
                    . '<p>Reset your password by clicking: <a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a></p>'
                    . '<p>This link expires in 30 minutes. If you did not request this, ignore this email.</p>';
                self::logError("password-reset template failed, using fallback HTML", ['email' => $email]);
            }
            $mail = self::createMailer();
            $mail->addAddress($email, $name);
            $mail->Subject = 'Reset Your Password — CSN Explore';
            $mail->Body    = $htmlContent;
            $mail->AltBody = "Hi $name,\n\nReset your password:\n$resetLink\n\nExpires in 30 minutes.\n\nCSN Explore Team";
            if (!$mail->send()) {
                self::logError("PHPMailer send() returned false for password reset", ['email' => $email, 'error' => $mail->ErrorInfo]);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            self::logError("Exception in sendPasswordResetEmail: " . $e->getMessage(), ['email' => $email]);
            return false;
        }
    }
    
    /**
     * Send admin notification email
     * 
     * @param array $booking Booking record data
     * @return bool Success status
     */
    private static function sendAdminNotification(array $booking): bool {
        try {
            // Render email template
            $htmlContent = self::renderTemplate('admin-notification', $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render admin notification template", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'admin_notification'
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress(ADMIN_NOTIFICATION_EMAIL, 'CSN Explore Admin');
            $serviceType = ucfirst($booking['service_type'] ?? 'Service');
            $mail->Subject = "New Booking #{$booking['id']} - {$serviceType}";
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send admin notification email", [
                    'booking_id' => $booking['id'],
                    'email' => ADMIN_NOTIFICATION_EMAIL,
                    'email_type' => 'admin_notification',
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendAdminNotification", [
                'booking_id' => $booking['id'],
                'email_type' => 'admin_notification',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Create and configure PHPMailer instance
     * 
     * @return PHPMailer Configured mailer instance
     */
    private static function createMailer(): PHPMailer {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)SMTP_PORT;

        // Timeouts — generous enough for slow SMTP but not blocking forever
        $mail->Timeout     = 30;
        $mail->getSMTPInstance()->Timeout = 30;

        // Keep connection alive for multiple sends in one request
        $mail->SMTPKeepAlive = true;

        // SSL options — needed on some XAMPP/localhost setups
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        // Sender — must match SMTP username for Gmail
        $fromEmail = SMTP_USERNAME ?: MAILERLITE_FROM_EMAIL;
        $fromName  = MAILERLITE_FROM_NAME ?: 'CSN Explore';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($fromEmail, $fromName);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        // Debug to log file (not stdout) — set to 0 in production
        $debugLevel = (APP_ENV === 'production') ? 0 : 2;
        $mail->SMTPDebug = $debugLevel;
        $logFile = __DIR__ . '/../../logs/smtp_debug.log';
        $mail->Debugoutput = function($str, $level) use ($logFile) {
            @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . trim($str) . "\n", FILE_APPEND | LOCK_EX);
        };

        return $mail;
    }
    
    /**
     * Render email template using output buffering
     * 
     * @param string $templateName Template name (without .php extension)
     * @param array $booking Booking data to pass to template
     * @return string|false Rendered HTML or false on failure
     */
    private static function renderTemplate(string $templateName, array $booking) {
        $templatePath = __DIR__ . "/../templates/emails/{$templateName}.php";
        
        if (!file_exists($templatePath)) {
            self::logError("Template file not found", [
                'template' => $templateName,
                'path' => $templatePath
            ]);
            return false;
        }
        
        try {
            // Start output buffering
            ob_start();
            
            // Include template (template has access to $booking variable)
            include $templatePath;
            
            // Get rendered content
            $htmlContent = ob_get_clean();
            
            return $htmlContent;
            
        } catch (Exception $e) {
            // Clean buffer on error
            ob_end_clean();
            
            self::logError("Template rendering exception", [
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Log email errors to logs/email_errors.log
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    private static function logError(string $message, array $context = []): void {
        $logDir = __DIR__ . '/../../logs';
        $logFile = $logDir . '/email_errors.log';
        
        // Ensure logs directory exists
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        // Format log entry
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] ERROR: {$message}\n";
        
        // Add context details
        foreach ($context as $key => $value) {
            $logEntry .= ucfirst(str_replace('_', ' ', $key)) . ": " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
        
        $logEntry .= "\n";
        
        // Write to log file
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
