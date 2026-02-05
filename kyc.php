<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_email_verified();

$user = current_user();
if (!$user) {
    redirect('/login.php');
}

$errors = [];
$notice = '';

$submission = latest_kyc_submission((int) $user['id']);
if ($submission && $submission['status'] === 'approved') {
    redirect('/app.php');
}

$showForm = !$submission || $submission['status'] === 'rejected';

if ($submission && $submission['status'] === 'pending') {
    $notice = 'Your KYC submission is under review. We will notify you once it is approved.';
}

if ($submission && $submission['status'] === 'rejected') {
    $notice = 'Your previous submission was rejected. Please correct the details and resubmit.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm) {
    if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $businessName = trim($_POST['business_name'] ?? '');

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    if ($phoneNumber === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($country === '') {
        $errors[] = 'Country is required.';
    }

    $filePath = null;
    $fileMime = null;
    $fileSize = null;

    if (!empty($_FILES['id_document']['name'])) {
        $file = $_FILES['id_document'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Unable to upload ID document. Please try again.';
        } elseif ($file['size'] > KYC_MAX_FILE_SIZE) {
            $errors[] = 'ID document exceeds the 5MB size limit.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);

            if (!in_array($mimeType, KYC_ALLOWED_MIME_TYPES, true)) {
                $errors[] = 'Invalid file type. Allowed types: PDF, JPG, PNG.';
            } else {
                if (!is_dir(KYC_UPLOAD_DIR)) {
                    mkdir(KYC_UPLOAD_DIR, 0700, true);
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $safeName = bin2hex(random_bytes(16)) . ($extension ? '.' . $extension : '');
                $destination = rtrim(KYC_UPLOAD_DIR, '/') . '/' . $safeName;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $errors[] = 'Unable to store the uploaded document.';
                } else {
                    $filePath = $destination;
                    $fileMime = $mimeType;
                    $fileSize = (int) $file['size'];
                }
            }
        }
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO kyc_submissions (user_id, full_name, phone_number, country, business_name, id_document_path, id_document_mime, id_document_size, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            (int) $user['id'],
            $fullName,
            $phoneNumber,
            $country,
            $businessName !== '' ? $businessName : null,
            $filePath,
            $fileMime,
            $fileSize,
            'pending',
        ]);

        redirect('/kyc.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KYC - Invoice Finance</title>
  <style>
    body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #0f172a; }
    .container { max-width: 640px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
    label { display: block; margin-top: 16px; font-weight: 600; }
    input, select { width: 100%; padding: 12px; margin-top: 8px; border-radius: 8px; border: 1px solid #cbd5f5; }
    button { margin-top: 24px; width: 100%; padding: 12px; border: none; background: #dc2626; color: #fff; font-weight: 700; border-radius: 8px; cursor: pointer; }
    .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-top: 16px; }
    .notice { background: #e0f2fe; color: #075985; padding: 12px; border-radius: 8px; margin-top: 16px; }
    .nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .nav a { color: #334155; text-decoration: none; }
  </style>
</head>
<body>
  <div class="container">
    <div class="nav">
      <strong>KYC Verification</strong>
      <a href="/logout.php">Logout</a>
    </div>

    <?php if ($notice): ?>
      <div class="notice"><?= e($notice) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($showForm): ?>
      <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" required>

        <label for="phone_number">Phone number</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="country">Country</label>
        <input type="text" id="country" name="country" required>

        <label for="business_name">Business name (optional)</label>
        <input type="text" id="business_name" name="business_name">

        <label for="id_document">Government-issued ID (optional)</label>
        <input type="file" id="id_document" name="id_document" accept="application/pdf,image/jpeg,image/png">

        <button type="submit">Submit KYC</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
