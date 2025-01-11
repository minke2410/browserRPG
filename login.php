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
    <title>ブラウザRPG</title>
</head>
<body>
    <h2>ログイン</h2>
    
    <form action="login.php" method="POST">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">ログイン</button>
    </form>

    <br>
    
    <?php
    // ログイン試行があった場合
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'db.php';
        // データベース接続を取得
        $db = new Database();
        $pdo = $db->getConnection();

        // フォームからの入力を取得
        $username = $_POST['username'];
        $password = $_POST['password'];

        // ユーザーが存在するかチェック
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ユーザーが存在すれば、パスワードを確認
            if (password_verify($password, $user['password'])) {
                // ログイン成功時にユーザーIDをセッションに保存
                $_SESSION['user_id'] = $user['id']; // ユーザーIDをセッションに保存
                $_SESSION['message'] = 'ログイン成功！';
                header('Location: game.php'); // ログイン後のダッシュボードページにリダイレクト
                exit;
            } else {
                $_SESSION['message'] = 'パスワードが間違っています。';
                header('Location: login.php'); // 再度ログイン画面にリダイレクト
                exit;
            }
        } else {
            // ユーザーが存在しない場合、新規アカウント作成を促すメッセージ
            $_SESSION['message'] = 'ユーザーが存在しません。新しいアカウントを作成しますか？';
            header('Location: insert.php'); // アカウント作成ページへリダイレクト
            exit;
        }
    }
    ?>
</body>
</html>
