<?php

session_start();

require_once 'db.php';

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

    // パーティーが現在のユーザーのものであるか確認
    $sql = "SELECT user_id FROM parties WHERE id = :party_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['party_id' => $partyId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || $result['user_id'] !== $userId) {
        echo json_encode(['success' => false, 'message' => 'このパーティーを削除する権限がありません。']);
        exit;
    }

    // パーティーを削除
    $sqlDelete = "DELETE FROM parties WHERE id = :party_id";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->execute(['party_id' => $partyId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()]);
}
