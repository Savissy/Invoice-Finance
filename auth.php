<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, email, email_verified_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('/login.php');
    }
}

function require_email_verified(): void
{
    require_login();
    $user = current_user();

    if (!$user || empty($user['email_verified_at'])) {
        redirect('/verify_notice.php');
    }
}

function latest_kyc_submission(int $userId): ?array
{
    $stmt = db()->prepare('SELECT * FROM kyc_submissions WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1');
    $stmt->execute([$userId]);
    $submission = $stmt->fetch();

    return $submission ?: null;
}

function require_kyc_approved(): void
{
    require_email_verified();
    $user = current_user();

    if (!$user) {
        redirect('/login.php');
    }

    $submission = latest_kyc_submission((int) $user['id']);

    if (!$submission || $submission['status'] !== 'approved') {
        redirect('/kyc.php');
    }
}

