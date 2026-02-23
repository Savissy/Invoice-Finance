<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$txHash = trim((string)($input['tx_hash'] ?? ''));
$actionType = trim((string)($input['action_type'] ?? ''));
$invoiceRef = isset($input['invoice_ref']) ? trim((string)$input['invoice_ref']) : null;

$actorAddr = trim((string)($input['actor_wallet_address'] ?? ''));
$counterAddr = isset($input['counterparty_wallet_address']) ? trim((string)$input['counterparty_wallet_address']) : null;

$amount = isset($input['amount_lovelace']) && $input['amount_lovelace'] !== ''
  ? (string)$input['amount_lovelace']
  : null;

$assetUnit = isset($input['asset_unit']) && $input['asset_unit'] !== ''
  ? trim((string)$input['asset_unit'])
  : 'lovelace';

// ✅ NEW: optional invoice economics (must be sent as strings from JS)
$faceValue = isset($input['face_value_lovelace']) && $input['face_value_lovelace'] !== ''
  ? (string)$input['face_value_lovelace']
  : null;

$repaymentValue = isset($input['repayment_lovelace']) && $input['repayment_lovelace'] !== ''
  ? (string)$input['repayment_lovelace']
  : null;

if ($txHash === '' || $actionType === '' || $actorAddr === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Missing required fields.']);
  exit;
}

$actorHash = hash('sha256', strtolower($actorAddr));
$counterHash = $counterAddr ? hash('sha256', strtolower($counterAddr)) : null;

try {
  $pdo = db();

  $stmt = $pdo->prepare("
    INSERT INTO invoice_transactions
      (tx_hash, action_type, invoice_ref, actor_wallet_address, actor_wallet_hash,
       counterparty_wallet_address, counterparty_wallet_hash,
       amount_lovelace, asset_unit, face_value_lovelace, repayment_lovelace, status)
    VALUES
      (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted')
    ON DUPLICATE KEY UPDATE
      invoice_ref = VALUES(invoice_ref),
      counterparty_wallet_address = VALUES(counterparty_wallet_address),
      counterparty_wallet_hash = VALUES(counterparty_wallet_hash),
      amount_lovelace = VALUES(amount_lovelace),
      asset_unit = VALUES(asset_unit),
      face_value_lovelace = VALUES(face_value_lovelace),
      repayment_lovelace = VALUES(repayment_lovelace)
  ");

  $stmt->execute([
    $txHash,
    $actionType,
    $invoiceRef,
    $actorAddr,
    $actorHash,
    $counterAddr,
    $counterHash,
    $amount,
    $assetUnit,
    $faceValue,
    $repaymentValue
  ]);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  error_log('log_tx error: ' . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error.']);
}
