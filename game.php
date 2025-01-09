<?php
session_start(); // セッション開始

require_once 'db.php'; // データベース接続を含むファイル

// ログインしているユーザーIDを取得
if (!isset($_SESSION['user_id'])) {
    die('ログインしていません｡');
}

$userId = $_SESSION['user_id'];

try {
    // charactersテーブルで、ユーザーIDに対応する主人公が存在するか確認
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM characters WHERE id = :id');
    $stmt->execute([':id' => $userId]);
    $characterCount = $stmt->fetchColumn();

    // 主人公が作成されていない場合
    if ($characterCount == 0) {
        // 主人公作成フォームを表示
        echo '<h2>このアカウントはセーブデータがありません｡</h2>';
        echo '<p>最初に主人公を作成しましょう｡</p>';
        echo '<form action="create-maincharacter.php" method="post">';
        echo '  <label for="character-name">キャラクター名</label>';
        echo '  <input type="text" name="mc-name" required>';
        echo '  <input type="submit" value="主人公を作成する">';
        echo '</form>';
    } else {
        // 既に主人公が作成されている場合
        echo '<p>さあ､冒険に出ましょう｡</p>';
        echo '<a href="continue-game.php">冒険を続ける</a>';
    }

} catch (PDOException $e) {
    // エラーハンドリング（データベースエラー発生時）
    die('データベースエラー: ' . $e->getMessage());
}
?>
