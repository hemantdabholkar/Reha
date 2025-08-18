<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

// Load the email configuration
require_once __DIR__ . '/../config.php';

/**
 * Sends an email using PHPMailer and the configured SMTP settings.
 *
 * @param string $to_email The recipient's email address.
 * @param string $to_name The recipient's name.
 * @param string $subject The email subject.
 * @param string $html_body The HTML content of the email.
 * @param bool $send_admin_copy Whether to send a copy to the admin.
 * @return bool True on success, false on failure.
 */
function send_order_email($to_email, $to_name, $subject, $html_body, $send_admin_copy = true) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Corresponds to 'ssl'
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        if ($send_admin_copy) {
            $mail->addBCC(FROM_EMAIL); // Send a Blind Carbon Copy to the admin
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = strip_tags($html_body); // Simple text version

        $mail->send();
        return true;
    } catch (Exception $e) {
        // For debugging, you might want to log this error
        // error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
