<?php
require_once 'db.php';
require_once 'User.php';
require_once 'Dungeon.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ログインが必要です。',
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$dungeonId = $data['dungeon_id'] ?? null;

if (!$dungeonId) {
    echo json_encode([
        'success' => false,
        'message' => 'ダンジョンIDが指定されていません。',
    ]);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $user = new User($pdo, $userId);
    $dungeon = new Dungeon($pdo);
    $dungeonInfo = $dungeon->getDungeonById($dungeonId);

    if ($user->getLevel() < $dungeonInfo['required_level']) {
        echo json_encode([
            'success' => false,
            'message' => '必要レベルが足りません。',
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => '挑戦可能です。',
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'エラーが発生しました: ' . $e->getMessage(),
    ]);
}
