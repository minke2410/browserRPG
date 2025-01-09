<?php

session_start();

// セッションからメッセージを取得
if (isset($_SESSION['message'])) {
    echo "<p style='color: blue;'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['message']); // メッセージを一度表示したら削除
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ブラウザRPG | ゲームスタート</title>
</head>
<body>
  <form action="game.php" method="get">
    <button type="submit">ゲーム開始</button>
  </form>
</body>
</html>