<?php

declare(strict_types=1);

$httpsEnabled = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

session_name('invoice_finance_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $httpsEnabled,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

const DB_HOST = '127.0.0.1';
const DB_NAME = 'invoice_finance';
const DB_USER = 'invoice_finance_user';
const DB_PASS = 'Saviourchibuike@23';

/**
 * IMPORTANT:
 * APP_URL must match how you access the app in the browser.
 * - Local dev (typical): http://localhost:8000  OR  http://localhost/Invoice-Finance
 * - Production: https://your-domain.com
 *
 * If your verify links are not working, APP_URL is usually the reason.
 */
const APP_URL = 'http://localhost:8000';

const SUPPORT_EMAIL = 'no-reply@example.com';

const KYC_UPLOAD_DIR = __DIR__ . '/storage/kyc_uploads';
const KYC_MAX_FILE_SIZE = 5242880; // 5MB
const KYC_ALLOWED_MIME_TYPES = [
    'application/pdf',
    'image/jpeg',
    'image/png',
];

/**
 * =========================
 * SMTP / EMAIL SETTINGS
 * =========================
 * Use Gmail SMTP with an App Password (NOT your normal Gmail password).
 *
 * Steps:
 * 1) Enable 2-Step Verification on the sender Gmail account
 * 2) Create an App Password for "Mail"
 * 3) Paste it into MAIL_PASSWORD below
 *
 * NOTE: Do NOT commit real credentials to a public GitHub repo.
 */
/* =====================================================
   MAIL CONFIG (Brevo SMTP)
===================================================== */

define('MAIL_HOST', 'smtp-relay.brevo.com');
define('MAIL_PORT', 587);

define('MAIL_USERNAME', 'a295d8001@smtp-brevo.com'); 
define('MAIL_PASSWORD', 'xsmtpsib-1ddf3b48ade1e3180bd27d00412708bdd3ad4cf7271a30084cf630c97184e66b-duYVye4ltnC1AAqn');

define('MAIL_FROM_EMAIL', 'precioussammy75@gmail.com');
define('MAIL_FROM_NAME', 'InsuFinance');

