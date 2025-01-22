<?php
session_start();

require_once 'db.php';
require_once 'Party.php';

header('Content-Type: application/json');

// ユーザーIDの取得
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です。']);
    exit;
}

$userId = $_SESSION['user_id'];
// データベース接続
try {
  $db = new Database();
  $pdo = $db->getConnection();
  $party = new Party($pdo, $userId);

  // ユーザーのパーティー数を取得
  $partyCount = $party->getPartyCount($userId); // PartyクラスにgetPartyCountメソッドが必要

  if ($partyCount >= 3) {
      echo json_encode(['success' => false, 'message' => '作成可能なパーティー数が最大です。']);
  } else {
      echo json_encode(['success' => true]);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'サーバーエラーが発生しました。']);
}

