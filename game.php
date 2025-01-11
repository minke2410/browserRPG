<?php
session_start(); // セッション開始

require_once 'database.php'; // データベース接続を含むファイル

// ログインしているユーザーIDを取得
if (!isset($_SESSION['user_id'])) {
    die('ログインしていません｡');
}

$userId = $_SESSION['user_id'];

try {
    // characters_inventory テーブルで、ユーザーIDに対応する主人公が存在するか確認
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM characters_inventory WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
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
        echo '<h2>セーブデータを確認しました｡</h2>';
        echo '<p>さあ､冒険に出ましょう｡</p>';
        echo '<a href="continue-game.php">冒険を続ける</a>';
    }

    // 現在のキャラクター情報を取得
    $stmt = $pdo->prepare('
        SELECT c.name, ci.level, ci.hp, ci.xp, ci.attack, ci.updated_at 
        FROM characters_inventory ci
        INNER JOIN characters c ON ci.character_id = c.character_id
        WHERE ci.user_id = :user_id
    ');
    $stmt->execute([':user_id' => $userId]);
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // キャラクター情報があれば表示
    if (!empty($characters)) {
        echo '<h3>キャラクター情報</h3>';
        foreach ($characters as $character) {
            echo '<p>名前: ' . htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<p>レベル: ' . $character['level'] . '</p>';
            echo '<p>HP: ' . $character['hp'] . '</p>';
            echo '<p>XP: ' . $character['xp'] . '</p>';
            echo '<p>攻撃力: ' . $character['attack'] . '</p>';
            echo '<p>最終更新: ' . $character['updated_at'] . '</p>';
            echo '<hr>';
        }
    }

} catch (PDOException $e) {
    // エラーハンドリング（データベースエラー発生時）
    die('データベースエラー: ' . $e->getMessage());
}

?>
