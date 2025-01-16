<?php

session_start();

require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です｡']);
    exit;
}
$userId = $_SESSION['user_id'];

// Databaseインスタンスを作成
$database = new Database();
$pdo = $database->getConnection(); // PDO接続を取得

$data = json_decode(file_get_contents('php://input'), true);
$partyId = $data['party_id'] ?? null;
error_log('リクエストデータ: ' . file_get_contents('php://input'));


if ($partyId === null) {
    echo json_encode(['success' => false, 'message' => 'パーティーIDが指定されていません。']);
    exit;
}

try {
    // データベースでパーティーIDが現在のユーザーのものであることを確認
    $sql = "SELECT user_id FROM parties WHERE id = :party_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['party_id' => $partyId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || $result['user_id'] !== $userId) {
        echo json_encode(['success' => false, 'message' => 'このパーティーを操作する権限がありません。']);
        exit;
    }


    // パーティーをアクティブ化する処理（例）
    $pdo->beginTransaction();     // トランザクション開始
    $sqlDeactivate = "UPDATE parties SET is_active = 0 WHERE user_id = :user_id";
    $stmtDeactivate = $pdo->prepare($sqlDeactivate);
    $stmtDeactivate->execute(['user_id' => $userId]);

    $sqlActivate = "UPDATE parties SET is_active = 1 WHERE id = :party_id";
    $stmtActivate = $pdo->prepare($sqlActivate);
    $stmtActivate->execute(['party_id' => $partyId]);
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()]);
}




?>
