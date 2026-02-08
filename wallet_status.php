<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$input = json_decode(file_get_contents('php://input'), true);
$address = trim((string)($input['address'] ?? ''));

if ($address === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing address']);
    exit;
}

$pdo = db();
$userId = (int)$_SESSION['user_id'];
$addrHash = hash('sha256', strtolower($address));

$stmt = $pdo->prepare("
  SELECT 1
  FROM user_wallets
  WHERE user_id = ?
    AND wallet_address_hash = ?
    AND status = 'verified'
  LIMIT 1
");
$stmt->execute([$userId, $addrHash]);

$allowed = (bool)$stmt->fetchColumn();

header('Content-Type: application/json');
echo json_encode([
  'ok' => true,
  'allowed' => $allowed
]);
