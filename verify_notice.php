<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
if ($user && !empty($user['email_verified_at'])) {
    redirect('/kyc.php');
}

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Email - Invoice Finance</title>
  <style>
    body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
    .container { max-width: 520px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
    button { margin-top: 24px; width: 100%; padding: 12px; border: none; background: #dc2626; color: #fff; font-weight: 700; border-radius: 8px; cursor: pointer; }
    .notice { background: #e0f2fe; color: #075985; padding: 12px; border-radius: 8px; margin-top: 16px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Verify your email</h1>
    <p>You must verify your email before starting KYC.</p>

    <?php if ($message): ?>
      <div class="notice"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" action="/resend_verification.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <button type="submit">Resend verification email</button>
    </form>
  </div>
</body>
</html>
