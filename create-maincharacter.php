<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // キャラクター名をフォームから取得
    $mcName = trim($_POST['mc-name']);

    if (empty($mcName)) {
        die('キャラクター名が入力されていません｡');
    }

    $mcName = htmlspecialchars($mcName, ENT_QUOTES, 'UTF-8');

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        die('ユーザーがログインしていません｡');
    }

    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // characters_inventory テーブルにユーザーごとに入力された名前でキャラクターを挿入
        $stmt = $pdo->prepare('
            INSERT INTO characters_inventory (user_id, character_id, name, level, hp, xp, attack) 
            VALUES (:user_id, 0, :name, 1, 100, 0, 5)
        ');
        $stmt->execute([ ':user_id' => $userId, ':name' => $mcName ]);

        // トランザクションコミット
        $pdo->commit();

        echo '主人公「' . $mcName . '」が作成されました｡';
        echo '<p>さあ､冒険に出ましょう｡</p>';
        echo '<a href="continue-game.php">冒険を続ける</a>';

    } catch (PDOException $e) {
        // トランザクションロールバック
        $pdo->rollBack();
        die('データベースエラー: ' . $e->getMessage());
    }

} else {
    die('不正なリクエストです｡');
}
?>
