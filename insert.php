<?php
session_start();

// セッションからメッセージを取得
if (isset($_SESSION['message'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['message']); // メッセージを一度表示したら削除
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブラウザRPG | アカウント作成</title>
</head>
<body>
    <h2>アカウント作成</h2>
    <form action="signup.php" method="POST">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">パスワード確認:</label>
        <input type="password" name="confirm_password" required><br>
        
        <button type="submit">作成</button>
    </form>
</body>
</html>