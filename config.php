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

const DB_HOST = 'sql102.infinityfree.com';
const DB_NAME = 'if0_41180584_invoice_finance';
const DB_USER = 'if0_41180584';
const DB_PASS = 'T05Itu9FUCrLV';

/**
 * IMPORTANT:
 * APP_URL must match how you access the app in the browser.
 * - Local dev (typical): http://localhost:8000  OR  http://localhost/Invoice-Finance
 * - Production: https://your-domain.com
 *
 * If your verify links are not working, APP_URL is usually the reason.
 */
const APP_URL = 'https://invoice-finance.infinityfree.me/';

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



