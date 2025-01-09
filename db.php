<?php
$host = 'localhost';     // MySQLのホスト名（通常はlocalhost）
$dbname = 'ブラウザRPG';     // データベース名
$username = 'root';      // MySQLのユーザー名
$password = 'Usami71524';          // MySQLのパスワード（XAMPPなどでは空の場合もあります）

try {
    // PDOでデータベース接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // 接続のエラーモードを設定（例外をスロー）
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続失敗時のエラーメッセージ
    die("接続に失敗しました: " . $e->getMessage());
}
?>
