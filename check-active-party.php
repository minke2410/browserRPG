<?php
session_start();
require_once 'db.php';
require_once 'Party.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です。']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Partyクラスを使用してアクティブなパーティーを確認
    $party = new Party($pdo, $userId);
    $activeParty = $party->getActiveParty();

    if ($activeParty) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'アクティブなパーティーが存在しません。']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()]);
}
