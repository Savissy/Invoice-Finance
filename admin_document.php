<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/admin/admin_auth.php';

require_admin();

$id = (int)($_GET['kyc_id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('Bad request'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT id_document_path, id_document_mime FROM kyc_submissions WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row || empty($row['id_document_path'])) { http_response_code(404); exit('Not found'); }

$path = (string)$row['id_document_path'];

// Safety: ensure it stays inside KYC_UPLOAD_DIR
$realBase = realpath(KYC_UPLOAD_DIR);
$realFile = realpath($path);

if (!$realBase || !$realFile || strpos($realFile, $realBase) !== 0) {
    http_response_code(403);
    exit('Forbidden');
}

$mime = (string)($row['id_document_mime'] ?? 'application/octet-stream');
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="kyc_document"');
readfile($realFile);
