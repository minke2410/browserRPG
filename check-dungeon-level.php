<?php
require_once 'db.php';         // Databaseクラスを読み込み
require_once 'Dungeon.php';    // Dungeonクラスを読み込み
require_once 'User.php';       // Userクラスを読み込み

session_start();

// ユーザーがログインしていることを確認
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
    exit;
}

$userId = intval($_SESSION['user_id']);
$dungeonId = intval($_POST['dungeon_id'] ?? 0);

try {
    // データベース接続を取得
    $db = new Database();
    $pdo = $db->getConnection();

    // UserクラスとDungeonクラスのインスタンスを作成
    $user = new User($pdo, $userId);
    $dungeon = new Dungeon($pdo);

    // ダンジョン挑戦可能か判定
    $playerLevel = $user->getLevel();
    $canChallenge = $dungeon->canChallengeDungeon($dungeonId, $playerLevel);

    echo json_encode(['success' => $canChallenge]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
