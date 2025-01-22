<?php

session_start();

require_once 'db.php';
require_once 'Party.php';

header('Content-Type: application/json');

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です。']);
    exit;
}

$userId = $_SESSION['user_id'];

// リクエストデータを取得
$data = json_decode(file_get_contents('php://input'), true);
$partyId = $data['party_id'] ?? null;

if ($partyId === null) {
    echo json_encode(['success' => false, 'message' => 'パーティーIDが指定されていません。']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $party = new Party($pdo, $userId);

    // パーティーが現在のユーザーのものであるか確認
    $result = $party->deleteParty($partyId, $userId);


    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()]);
}
