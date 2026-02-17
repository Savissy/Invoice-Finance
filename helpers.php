<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Escape output safely
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect(string $path): void
{
    if (!headers_sent()) {
        header('Location: ' . $path);
    }
    exit;
}

/**
 * Build app base URL safely (works on InfinityFree subdomains too)
 */
function app_url(): string
{
    if (defined('APP_URL') && APP_URL) {
        return rtrim((string) APP_URL, '/');
    }

    $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

/**
 * Start secure session (REQUIRED for login, admin, CSRF)
 */
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_name('invoice_finance_session');

    session_set_cookie_params([
        'lifetime' => 7200,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');

    session_start();
}

/**
 * Send email verification link using Brevo SMTP (PHPMailer)
 */
function send_verification_email(string $toEmail, string $token): void
{
    // Load PHPMailer classes (local lib structure)
    require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

    $verifyLink = app_url() . '/verify.php?token=' . urlencode($token);

    $mail = new PHPMailer(true);

    try {
        // ✅ Brevo SMTP transport
        $mail->isSMTP();
        $mail->Host       = defined('MAIL_HOST') ? (string) MAIL_HOST : 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('MAIL_USERNAME') ? (string) MAIL_USERNAME : '';
        $mail->Password   = defined('MAIL_PASSWORD') ? (string) MAIL_PASSWORD : '';
        $mail->Port       = defined('MAIL_PORT') ? (int) MAIL_PORT : 587;

        // Brevo recommended encryption on port 587
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Optional: if your host has SSL issues, you can uncomment this:
        /*
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];
        */

        // (Optional) Debug: ONLY enable during testing
        // $mail->SMTPDebug = 2; // shows SMTP logs
        // $mail->Debugoutput = 'error_log';

        // Sender
        $fromEmail = defined('MAIL_FROM_EMAIL')
            ? (string) MAIL_FROM_EMAIL
            : ('no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        $fromName = defined('MAIL_FROM_NAME')
            ? (string) MAIL_FROM_NAME
            : 'invoice Finance';

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verify your invoice Finance account';

        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Thank you for registering on <strong>invoice Finance</strong>.</p>
            <p>Please click the button below to verify your email address:</p>
            <p>
                <a href='{$verifyLink}'
                   style='display:inline-block;padding:12px 20px;background:#dc2626;color:#ffffff;
                          text-decoration:none;border-radius:6px;font-weight:600;'>
                   Verify Email
                </a>
            </p>
            <p>This link expires in 24 hours.</p>
        ";

        $mail->AltBody = "Verify your email by visiting: {$verifyLink}";

        // Deliverability headers (optional but nice)
        $mail->addCustomHeader('X-Mailer', 'PHPMailer');

        $mail->send();
    } catch (Exception $e) {
        // Never show errors to users
        error_log('Email send failed: ' . $mail->ErrorInfo);
    }
}
