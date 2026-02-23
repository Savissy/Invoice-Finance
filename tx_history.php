<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$address = trim((string)($_GET['address'] ?? ''));
if ($address === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Missing address']);
  exit;
}

$addrHash = hash('sha256', strtolower($address));

$pdo = db();
$stmt = $pdo->prepare("
  SELECT tx_hash, action_type, invoice_ref, amount_lovelace, asset_unit, status, created_at
  FROM invoice_transactions
  WHERE actor_wallet_hash = ? OR counterparty_wallet_hash = ?
  ORDER BY id DESC
  LIMIT 200
");
$stmt->execute([$addrHash, $addrHash]);
$rows = $stmt->fetchAll();

echo json_encode(['ok' => true, 'transactions' => $rows]);
