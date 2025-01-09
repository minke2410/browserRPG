<?php
session_start();

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
        // すでにユーザーが存在してる場合
        $_SESSION['message'] = 'そのユーザー名は既に使用されています｡他の名前を試して下さい｡';
        header('Location: insert.php');
        exit;  
  } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); //パスワードをハッシュ化

    $insert_sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $insert_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

    if ($insert_stmt->execute()) {
      $_SESSION['message'] = 'アカウントが作成されました！ログインしてください｡';
      header('Location: index.php');
      exit;
    } else {
      $_SESSION['message'] = "アカウントの作成に失敗しました。もう一度お試しください。";
      header('Location: insert.php');
      exit;
    }
  }
}
?>
