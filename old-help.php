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
    header('Location: ' . $path);
    exit;
}

/**
 * Send email verification link using Gmail SMTP (PHPMailer)
 */
function send_verification_email(string $toEmail, string $token): void
{
    // Load PHPMailer classes
    require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

    $verifyLink = APP_URL . '/verify.php?token=' . urlencode($token);

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        // Sender & recipient
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verify your Invoice Finance account';

        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Thank you for registering on <strong>Invoice Finance</strong>.</p>
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

        $mail->send();

    } catch (Exception $e) {
        // Never show email errors to users
        error_log('Email send failed: ' . $mail->ErrorInfo);
    }
}
