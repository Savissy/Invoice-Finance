<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf($_POST['csrf_token'] ?? null)) {
    redirect('/verify_notice.php?message=' . urlencode('Invalid request. Please try again.'));
}

$user = current_user();
if (!$user) {
    redirect('/login.php');
}

if (!empty($user['email_verified_at'])) {
    redirect('/kyc.php');
}

$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);
$expiresAt = (new DateTime('+1 day'))->format('Y-m-d H:i:s');

$stmt = db()->prepare('INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
$stmt->execute([(int) $user['id'], $tokenHash, $expiresAt]);

send_verification_email($user['email'], $token);

redirect('/verify_notice.php?message=' . urlencode('Verification email sent.'));
