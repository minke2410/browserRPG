<?php
session_start(); // セッション開始

require_once 'db.php'; // データベース接続を含むファイル
require_once 'Character.php'; // Character クラスのファイル

// データベース接続を取得
$database = new Database();
$pdo = $database->getConnection();

// ログインしているユーザーIDを取得
if (!isset($_SESSION['user_id'])) {
    die('ログインしていません｡');
}

$userId = $_SESSION['user_id'];

try {
    // Characterクラスを使って、キャラクター情報を取得
    $characterObj = new Character($pdo, $userId);
    $characters = $characterObj->getCharacters();

    // 主人公が作成されていない場合、作成フォームを表示
    if (empty($characters)) {
        showCharacterCreationForm();
    } else {
        showCharacterData($characters);
    }

} catch (PDOException $e) {
    // エラーハンドリング（データベースエラー発生時）
    die('データベースエラー: ' . $e->getMessage());
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

// キャラクター情報を表示
function showCharacterData($characters) {
    echo '<h2>セーブデータを確認しました｡</h2>';
    echo '<p>さあ､冒険に出ましょう｡</p>';
    echo '<a href="game-main.php">冒険を続ける</a>';

    // 最初のキャラクターを取得（リストから最初のキャラクター）
    $character = $characters[0]; 

    echo '<h3>セーブデータ</h3>';
    echo '<p>主人公の名前: ' . htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p>レベル: ' . $character['level'] . '</p>';
    echo '<p>HP: ' . $character['hp'] . '</p>';
    echo '<p>XP: ' . $character['xp'] . '</p>';
    echo '<p>攻撃力: ' . $character['attack'] . '</p>';
    echo '<p>最終更新: ' . $character['updated_at'] . '</p>';
}

?>
