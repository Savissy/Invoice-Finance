<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$token = $_GET['token'] ?? '';
$verified = false;

if ($token) {
    $tokenHash = hash('sha256', $token);
    $stmt = db()->prepare('SELECT id, user_id, expires_at FROM email_verifications WHERE token_hash = ? LIMIT 1');
    $stmt->execute([$tokenHash]);
    $verification = $stmt->fetch();

    if ($verification) {
        $expiresAt = new DateTime($verification['expires_at']);
        if ($expiresAt >= new DateTime()) {
            $stmt = db()->prepare('UPDATE users SET email_verified_at = NOW() WHERE id = ?');
            $stmt->execute([(int) $verification['user_id']]);

            $stmt = db()->prepare('DELETE FROM email_verifications WHERE user_id = ?');
            $stmt->execute([(int) $verification['user_id']]);

            $_SESSION['user_id'] = (int) $verification['user_id'];
            $verified = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification - Invoice Finance</title>
  <style>
    body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
    .container { max-width: 520px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); text-align: center; }
    .status { margin-top: 16px; padding: 12px; border-radius: 8px; }
    .success { background: #dcfce7; color: #166534; }
    .error { background: #fee2e2; color: #991b1b; }
    a { display: inline-block; margin-top: 20px; color: #dc2626; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Email verification</h1>
    <?php if ($verified): ?>
      <div class="status success">Your email has been verified. You can start KYC now.</div>
      <a href="/kyc.php">Continue to KYC</a>
    <?php else: ?>
      <div class="status error">Verification link is invalid or expired.</div>
      <a href="/verify_notice.php">Request a new link</a>
    <?php endif; ?>
  </div>
</body>
</html>
