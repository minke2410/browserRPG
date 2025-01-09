<?php
session_start();

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ユーザー名とパスワードをフォームから取得
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
    $stmt = $pdo->prepare('INSERT INTO characters (id, name) VALUES (:id, :name)');

    $stmt->execute([
      ':id' => $userId,
      ':name' => $mcName
    ]);
    echo '主人公"'. $mcName. '"が作成されました｡';
  } catch (PDOException $e) {
    die('データベースエラー: ' . $e->getMessage());
  }

} else {
  die ('不正なリクエストです｡');
}
?>