<?php
session_start();
require_once 'db.php';
require_once 'Character.php';

// データベース接続を取得
$database = new Database();
$pdo = $database->getConnection();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    die('ユーザーがログインしていません｡');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc-name'])) {
    // 主人公作成フォームの送信処理
    $mcName = trim($_POST['mc-name']);

    if (empty($mcName)) {
        die('キャラクター名が入力されていません｡');
    }
    
    $mcName = htmlspecialchars($mcName, ENT_QUOTES, 'UTF-8');
    
    // Character クラスを使って主人公の名前を更新
    try {
        $character = new Character($pdo, $userId);
        $character->updateMainCharacterName($mcName);

        echo '<p>主人公の名前が変更されました｡</p>';
        echo '<p>さあ､冒険に出ましょう｡</p>';
        echo '<a href="game-intro.php">ゲームを開始する</a>';
    } catch (Exception $e) {
        echo '<p>エラー: ' . $e->getMessage() . '</p>';
    }
} else {
    showCharacterCreationForm();

}

// 主人公作成フォームを表示
function showCharacterCreationForm() {
    echo '<h2>このアカウントはセーブデータがありません｡</h2>';
    echo '<p>最初に主人公を作成しましょう｡</p>';
    echo '<form action="create-maincharacter.php" method="post">';
    echo '  <label for="character-name">キャラクター名</label>';
    echo '  <input type="text" name="mc-name" required>';
    echo '  <input type="submit" value="主人公を作成する">';
    echo '</form>';
}
?>
