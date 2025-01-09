<?php
session_start(); // セッションを開始

// db.php でデータベース接続を行う
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー名とパスワードをフォームから取得
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL文でデータベースからユーザー情報を取得
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // ユーザーが存在するか確認
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザーが見つかった場合
    if ($user) {
        // パスワードが一致した場合
        if (password_verify($password, $user['password'])) {
            // セッションにユーザーIDを保存
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // セッションにユーザー名を保存
            
            // ログイン成功時にメッセージを表示
            $_SESSION['message'] = 'ログインに成功しました｡';
            header('Location: start.php');
            exit;
        } else {
            // パスワードが間違っている場合
            $_SESSION['message'] = "ユーザー名かパスワードが間違っています。";
            header('Location: index.php');
        }
    } else {
        // ユーザーが存在しない場合
        $_SESSION['message'] = 'アカウントが存在しません｡新しく作成しますか?';
        header('Location: insert.php');
        exit;
    }
}
?>
