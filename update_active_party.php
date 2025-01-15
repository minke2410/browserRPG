<?php

require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$partyId = $data['party_id'] ?? null;
$newIsActive = $data['is_active'] ?? null;

if ($partyId === null || $newIsActive === null) {
    echo json_encode(['success' => false, 'message' => '無効なデータです。']);
    exit;
}

try {
    $pdo->beginTransaction();

    // すべてのパーティーを非アクティブに
    $sqlDeactivate = "UPDATE parties SET is_active = 0 WHERE user_id = (SELECT user_id FROM parties WHERE id = :party_id)";
    $stmtDeactivate = $pdo->prepare($sqlDeactivate);
    $stmtDeactivate->execute(['party_id' => $partyId]);

    // 指定されたパーティーをアクティブに
    if ($newIsActive) {
        $sqlActivate = "UPDATE parties SET is_active = 1 WHERE id = :party_id";
        $stmtActivate = $pdo->prepare($sqlActivate);
        $stmtActivate->execute(['party_id' => $partyId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
