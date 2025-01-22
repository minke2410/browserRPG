<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'ログインが必要です。']);
        exit;
    }

    // データ準備
    $partyName = $_POST['party_name'] ?? null;
    $member1 = $_POST['member1'] ?? null;
    $member2 = $_POST['member2'] ?? null;
    $member3 = $_POST['member3'] ?? null;
    $member4 = $_POST['member4'] ?? null;

    // NULL値を許容するための調整
    $member1 = empty($member1) ? null : $member1;
    $member2 = empty($member2) ? null : $member2;
    $member3 = empty($member3) ? null : $member3;
    $member4 = empty($member4) ? null : $member4;

    try {
        $db = new Database();
        $pdo = $db->getConnection();

        // レコードの挿入
        $stmt = $pdo->prepare("
            INSERT INTO parties (user_id, party_name, member1, member2, member3, member4, is_active)
            VALUES (:user_id, :party_name, :member1, :member2, :member3, :member4, 0)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':party_name' => $partyName,
            ':member1' => $member1,
            ':member2' => $member2,
            ':member3' => $member3,
            ':member4' => $member4,
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
